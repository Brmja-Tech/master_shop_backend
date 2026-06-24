<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        if (!auth('admin')->user()->hasAccess('users') && !auth('admin')->user()->hasAccess('orders')) {
            abort(403);
        }

        return view('dashboard.orders.index');
    }

    public function show($id)
    {
        if (!auth('admin')->user()->hasAccess('users') && !auth('admin')->user()->hasAccess('orders')) {
            abort(403);
        }

        $order = Order::with([
            'items.product.images',
            'user',
            'vendor',
            'delivery'
        ])->find($id);

        if (!$order) {
            flash()->error(__('dashboard.order-not-found'));
            return redirect()->back();
        }

        return view('dashboard.orders.show', compact('order'));
    }
}
