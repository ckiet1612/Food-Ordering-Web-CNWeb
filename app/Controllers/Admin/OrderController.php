<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

class OrderController extends Controller
{
    public function index(): void
    {
        $status = $this->input('status');
        $status = is_string($status) ? $status : null;

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
            'shippers' => User::shippers(),
            'events' => Order::events((int) $order['id']),
        ], 'admin');
    }

    public function updateStatus(string $id): void
    {
        $order = Order::find((int) $id) ?? $this->notFound();
        $status = $this->input('status');
        $status = is_string($status) ? $status : '';

        try {
            Order::transition((int) $order['id'], $status, (int) auth()['id']);
            flash('success', 'Đơn #' . $order['id'] . ': ' . Order::STATUSES[$status] . '.');
        } catch (\DomainException $e) {
            flash('error', $e->getMessage());
        }

        $this->redirect('/admin/orders/' . $order['id']);
    }

    public function assign(string $id): void
    {
        try {
            Order::assign((int) $id, (int) $this->input('shipper_id'), (int) auth()['id']);
            flash('success', 'Đã phân công nhân viên giao hàng.');
        } catch (\DomainException $e) {
            flash('error', $e->getMessage());
        }
        $this->redirect('/admin/orders/' . (int) $id);
    }
}
