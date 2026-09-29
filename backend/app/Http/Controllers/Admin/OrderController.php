<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q'));

        $orders = Order::with('product')
            ->when(in_array($status, ['new', 'viewed', 'completed', 'cancelled'], true), fn ($query) => $query->where('status', $status))
            ->when($search, fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"))))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'counts', 'status', 'search'));
    }

    public function show(Order $order)
    {
        if ($order->status === 'new') {
            $order->update(['status' => 'viewed']);
        }

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:new,viewed,completed,cancelled',
        ]);

        $order->update($data);

        return redirect()->route('admin.orders.show', $order)->with('success', __('تم تحديث حالة الاستفسار'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', __('تم حذف الاستفسار'));
    }
}
