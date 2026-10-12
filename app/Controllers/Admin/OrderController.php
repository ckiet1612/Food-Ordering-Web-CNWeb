<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function index(): void
    {
        $status = $this->input('status');

        $this->view('admin/order/index', [
            'title'  => 'Đơn hàng',
            'status' => $status,
            'orders' => Order::filter($status),
        ], 'admin');
    }

    public function show(string $id): void
    {
        $order = Order::find((int) $id) ?? $this->notFound();

        $this->view('admin/order/detail', [
            'title' => 'Đơn hàng #' . $order['id'],
            'order' => $order,
            'items' => OrderItem::byOrder((int) $order['id']),
        ], 'admin');
    }

    public function updateStatus(string $id): void
    {
        $order = Order::find((int) $id) ?? $this->notFound();
        $status = (string) $this->input('status');

        if (array_key_exists($status, Order::STATUSES)) {
            Order::update((int) $order['id'], ['status' => $status]);
            flash('success', 'Đơn #' . $order['id'] . ': ' . Order::STATUSES[$status] . '.');
        }

        $this->redirect('/admin/orders/' . $order['id']);
    }
}
