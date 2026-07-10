<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->ensureAdmin();

        $orders = Order::query()
            ->with(['user', 'files'])
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
            'admins' => $users->where('role', 'admin')->count(),
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
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('orders')
            ->latest()
            ->get();

        return view('admin.users', compact('users', 'search'));
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
            'password' => ['required', Password::min(6)],
            'role' => ['required', Rule::in(['customer', 'admin'])],
        ]);

        $user = User::query()->create($data);

        return redirect()
            ->route($user->role === 'admin' ? 'admin.users' : 'admin.customers')
            ->with('status', 'تم إضافة المستخدم بنجاح.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'password' => ['nullable', Password::min(6)],
            'role' => ['required', Rule::in(['customer', 'admin'])],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route($user->role === 'admin' ? 'admin.users' : 'admin.customers')
            ->with('status', 'تم تحديث بيانات المستخدم.');
    }

    public function destroyUser(User $user)
    {
        $this->ensureAdmin();

        if ($user->is(Auth::user())) {
            return back()->withErrors(['user' => 'لا يمكنك حذف حسابك الحالي.']);
        }

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
            'password' => ['nullable', Password::min(6)],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

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

    private function ensureAdmin(): void
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
    }
}
