<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDeliveredFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with(['files', 'deliveredFiles'])
            ->withCount('files')
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function destroy(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if ($order->payment_status === 'paid') {
            return back()->withErrors([
                'order' => 'لا يمكن حذف الطلب بعد إتمام الدفع.',
            ]);
        }

        $order->load(['files', 'deliveredFiles']);

        foreach ($order->files as $file) {
            $absolutePath = storage_path('app/' . $file->path);

            if (File::isFile($absolutePath)) {
                File::delete($absolutePath);
            }
        }

        foreach ($order->deliveredFiles as $deliveredFile) {
            $deliveredPath = storage_path('app/' . $deliveredFile->path);
            if (File::isFile($deliveredPath)) {
                File::delete($deliveredPath);
            }
        }

        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('status', 'تم حذف الطلب وجميع ملفاته بنجاح.');
    }

    public function downloadDeliveredFile(Order $order, OrderDeliveredFile $deliveredFile)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        abort_unless(in_array($order->service_type, ['formatting', 'research'], true), 404);
        abort_unless($deliveredFile->order_id === $order->id, 404);

        $absolutePath = storage_path('app/' . $deliveredFile->path);

        abort_unless(File::isFile($absolutePath), 404);

        if (request()->boolean('view')) {
            return response()->file($absolutePath, [
                'Content-Type' => $deliveredFile->mime ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . addslashes($deliveredFile->original_name) . '"',
            ]);
        }

        return Response::download($absolutePath, $deliveredFile->original_name);
    }
}
