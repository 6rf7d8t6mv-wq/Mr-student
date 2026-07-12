<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDeliveredFile;
use App\Models\OrderFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    private const ADMIN_PERMISSION_KEYS = [
        'users_create',
        'users_update',
        'users_delete',
        'customers_create',
        'customers_update',
        'customers_delete',
        'orders_delete',
    ];

    public function dashboard()
    {
        $this->ensureAdmin();

        $orders = Order::query()
            ->with(['user', 'files', 'deliveredFiles'])
            ->latest()
            ->get();

        $users = User::query()
            ->withCount('orders')
            ->latest()
            ->get();

        $stats = [
            'orders' => $orders->count(),
            'new_orders' => $orders->where('status', 'new')->count(),
            'customers' => $users->where('role', 'customer')->count(),
            'admins' => $users->where('role', 'admin')->whereNotNull('admin_permissions')->count(),
            'print_total' => $orders->sum('print_total'),
            'binding_total' => $orders->sum('binding_total'),
            'grand_total' => $orders->sum('grand_total'),
        ];

        return view('admin.dashboard', compact('orders', 'users', 'stats'));
    }

    public function orders(Request $request)
    {
        $this->ensureAdmin();

        $search = trim((string) $request->query('search', ''));

        $orders = Order::query()
            ->with(['user', 'files'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->get();

        return view('admin.orders', compact('orders', 'search'));
    }

    public function users(Request $request)
    {
        $this->ensureAdmin();

        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->where('role', 'admin')
            ->whereNotNull('admin_permissions')
            ->whereKeyNot(Auth::id())
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('orders')
            ->latest()
            ->get();

        $permissionOptions = $this->permissionOptions();

        return view('admin.users', compact('users', 'search', 'permissionOptions'));
    }

    public function customers(Request $request)
    {
        $this->ensureAdmin();

        $search = trim((string) $request->query('search', ''));

        $customers = User::query()
            ->where('role', 'customer')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('orders')
            ->latest()
            ->get();

        return view('admin.customers', compact('customers', 'search'));
    }

    public function settings()
    {
        $this->ensureAdmin();

        return view('admin.settings');
    }

    public function storeUser(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', Password::min(6), 'confirmed'],
            'role' => ['required', Rule::in(['customer', 'admin'])],
            'admin_permissions' => ['nullable', 'array'],
            'admin_permissions.*' => ['string', Rule::in(self::ADMIN_PERMISSION_KEYS)],
        ]);

        $this->ensurePermission($data['role'] === 'admin' ? 'users_create' : 'customers_create');

        if ($data['role'] === 'admin') {
            $data['admin_permissions'] = $this->adminPermissionsFromRequest($request);
        } else {
            $data['admin_permissions'] = null;
        }

        unset($data['password_confirmation']);

        $user = User::query()->create($data);

        return redirect()
            ->route($user->role === 'admin' ? 'admin.users' : 'admin.customers')
            ->with('status', 'تم إضافة المستخدم بنجاح.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->ensureAdmin();

        if ($user->is(Auth::user())) {
            return back()->withErrors(['user' => 'عدّل حسابك من صفحة الإعدادات فقط.']);
        }

        if ($user->role === 'admin' && $user->admin_permissions === null) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'password' => ['nullable', Password::min(6), 'confirmed'],
            'role' => ['required', Rule::in(['customer', 'admin'])],
        ]);

        $this->ensurePermission($user->role === 'admin' ? 'users_update' : 'customers_update');

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        unset($data['password_confirmation']);

        $user->update($data);

        return redirect()
            ->route($user->role === 'admin' ? 'admin.users' : 'admin.customers')
            ->with('status', 'تم تحديث بيانات المستخدم.');
    }

    public function updateUserPermissions(Request $request, User $user)
    {
        $this->ensureAdmin();
        $this->ensurePermission('users_update');

        if ($user->is(Auth::user())) {
            return back()->withErrors(['user' => 'عدّل صلاحيات حسابك من مدير النظام الأساسي فقط.']);
        }

        if ($user->role !== 'admin' || $user->admin_permissions === null) {
            abort(403);
        }

        $request->validate([
            'admin_permissions' => ['nullable', 'array'],
            'admin_permissions.*' => ['string', Rule::in(self::ADMIN_PERMISSION_KEYS)],
        ]);

        $user->update([
            'admin_permissions' => $this->adminPermissionsFromRequest($request),
        ]);

        return redirect()->route('admin.users')->with('status', 'تم تحديث صلاحيات المستخدم.');
    }

    public function destroyUser(User $user)
    {
        $this->ensureAdmin();

        if ($user->is(Auth::user())) {
            return back()->withErrors(['user' => 'لا يمكنك حذف حسابك الحالي.']);
        }

        if ($user->role === 'admin' && $user->admin_permissions === null) {
            return back()->withErrors(['user' => 'لا يمكن حذف مدير النظام الأساسي من صفحة المستخدمين.']);
        }

        $this->ensurePermission($user->role === 'admin' ? 'users_delete' : 'customers_delete');

        $route = $user->role === 'admin' ? 'admin.users' : 'admin.customers';
        $user->delete();

        return redirect()->route($route)->with('status', 'تم حذف المستخدم.');
    }

    public function updateSettings(Request $request)
    {
        $this->ensureAdmin();

        $user = Auth::user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', Password::min(6), 'confirmed'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        unset($data['current_password'], $data['password_confirmation']);

        $user->update($data);

        return redirect()->route('admin.settings')->with('status', 'تم تحديث إعدادات حسابك.');
    }

    public function download(OrderFile $file)
    {
        $this->ensureAdmin();

        $absolutePath = storage_path('app/' . $file->path);

        abort_unless(is_file($absolutePath), 404);

        return Response::download($absolutePath, $file->original_name);
    }

    public function uploadDeliveredFile(Request $request, Order $order)
    {
        $this->ensureAdmin();
        abort_unless(in_array($order->service_type, ['formatting', 'research'], true), 404);

        $data = $request->validate([
            'delivered_file' => ['required', 'file', 'max:51200'],
        ]);

        $file = $data['delivered_file'];
        $storagePath = 'delivered/orders/' . $order->id;
        $fullPath = storage_path('app/' . $storagePath);

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $storedName = 'delivered_' . now()->format('YmdHis') . '_' . $order->id . ($extension ? '.' . $extension : '');
        $file->move($fullPath, $storedName);

        $path = $storagePath . '/' . $storedName;

        $order->deliveredFiles()->create([
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => filesize($fullPath . '/' . $storedName) ?: 0,
            'uploaded_by' => Auth::id(),
        ]);

        $order->update([
            'delivered_file_original_name' => $file->getClientOriginalName(),
            'delivered_file_stored_name' => $storedName,
            'delivered_file_path' => $path,
            'delivered_file_mime' => $file->getClientMimeType(),
            'delivered_file_size' => filesize($fullPath . '/' . $storedName) ?: 0,
            'delivered_file_uploaded_at' => now(),
        ]);

        return back()->with('status', 'تم إرفاق ملف التسليم للعميل بنجاح.');
    }

    public function downloadDeliveredFile(OrderDeliveredFile $deliveredFile)
    {
        $this->ensureAdmin();

        $absolutePath = storage_path('app/' . $deliveredFile->path);

        abort_unless(File::isFile($absolutePath), 404);

        return Response::download($absolutePath, $deliveredFile->original_name);
    }

    public function destroyDeliveredFile(OrderDeliveredFile $deliveredFile)
    {
        $this->ensureAdmin();

        $absolutePath = storage_path('app/' . $deliveredFile->path);
        if (File::isFile($absolutePath)) {
            File::delete($absolutePath);
        }

        $order = $deliveredFile->order;
        $deliveredFile->delete();

        $latestFile = $order->deliveredFiles()->first();
        $order->update([
            'delivered_file_original_name' => $latestFile?->original_name,
            'delivered_file_stored_name' => $latestFile?->stored_name,
            'delivered_file_path' => $latestFile?->path,
            'delivered_file_mime' => $latestFile?->mime,
            'delivered_file_size' => $latestFile?->size,
            'delivered_file_uploaded_at' => $latestFile?->created_at,
        ]);

        return back()->with('status', 'تم حذف ملف التسليم بنجاح.');
    }

    public function destroyOrder(Order $order)
    {
        $this->ensureAdmin();
        $this->ensurePermission('orders_delete');

        $order->load(['files', 'deliveredFiles']);

        foreach ($order->files as $file) {
            $absolutePath = storage_path('app/' . $file->path);
            if (File::isFile($absolutePath)) {
                File::delete($absolutePath);
            }
        }

        foreach ($order->deliveredFiles as $deliveredFile) {
            $absolutePath = storage_path('app/' . $deliveredFile->path);
            if (File::isFile($absolutePath)) {
                File::delete($absolutePath);
            }
        }

        if (filled($order->delivered_file_path)) {
            $deliveredPath = storage_path('app/' . $order->delivered_file_path);
            if (File::isFile($deliveredPath)) {
                File::delete($deliveredPath);
            }
        }

        $order->delete();

        return redirect()->route('admin.orders')->with('status', 'تم حذف الطلب بنجاح.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
    }

    private function ensurePermission(string $permission): void
    {
        abort_unless(Auth::user()?->hasAdminPermission($permission), 403);
    }

    private function adminPermissionsFromRequest(Request $request): array
    {
        return collect($request->input('admin_permissions', []))
            ->filter(fn ($permission) => in_array($permission, self::ADMIN_PERMISSION_KEYS, true))
            ->values()
            ->all();
    }

    private function permissionOptions(): array
    {
        return [
            'users_create' => 'إضافة مستخدمين',
            'users_update' => 'تعديل مستخدمين',
            'users_delete' => 'حذف مستخدمين',
            'customers_create' => 'إضافة عملاء',
            'customers_update' => 'تعديل عملاء',
            'customers_delete' => 'حذف عملاء',
            'orders_delete' => 'حذف طلب',
        ];
    }
}
