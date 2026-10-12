<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    // GET /checkout
    public function checkout(): void
    {
        $cart = Cart::detailed();

        if ($cart['items'] === []) {
            flash('error', 'Giỏ hàng của bạn đang trống.');
            $this->redirect('/cart');
        }

        $this->view('order/checkout', ['title' => 'Thanh toán', 'cart' => $cart]);
    }

    // POST /checkout
    public function store(): void
    {
        $data = $this->validate([
            'receiver_name'  => 'required|max:100',
            'phone'          => 'required|phone',
            'address'        => 'required|max:255',
            'note'           => 'max:500',
            'payment_method' => 'required|in:' . implode(',', array_keys(Order::PAYMENT_METHODS)),
        ], [
            'receiver_name'  => 'Tên người nhận',
            'phone'          => 'Số điện thoại',
            'address'        => 'Địa chỉ giao hàng',
            'note'           => 'Ghi chú',
            'payment_method' => 'Phương thức thanh toán',
        ], '/checkout');

        $cart = Cart::detailed();
        if ($cart['items'] === []) {
            $this->redirect('/cart');
        }

        $orderId = Order::place((int) auth()['id'], [
            'receiver_name'  => $data['receiver_name'],
            'phone'          => $data['phone'],
            'address'        => $data['address'],
            'note'           => ($data['note'] ?? '') !== '' ? $data['note'] : null,
            'payment_method' => $data['payment_method'],
        ], $cart);

        Cart::clear();
        flash('success', 'Đặt hàng thành công! Yumio đang chuẩn bị món cho bạn.');
        $this->redirect('/orders/' . $orderId);
    }

    // GET /orders
    public function index(): void
    {
        $this->view('order/history', [
            'title'  => 'Đơn hàng của tôi',
            'orders' => Order::byUser((int) auth()['id']),
        ]);
    }

    // GET /orders/{id}
    public function show(string $id): void
    {
        $order = Order::find((int) $id);

        if ($order === null || (int) $order['user_id'] !== (int) auth()['id']) {
            $this->notFound();
        }

        $this->view('order/detail', [
            'title' => 'Đơn hàng #' . $order['id'],
            'order' => $order,
            'items' => OrderItem::byOrder((int) $order['id']),
        ]);
    }

    // POST /orders/{id}/cancel — khách chỉ huỷ được khi đơn còn chờ xác nhận
    public function cancel(string $id): void
    {
        $order = Order::find((int) $id);

        if ($order === null || (int) $order['user_id'] !== (int) auth()['id']) {
            $this->notFound();
        }

        if ($order['status'] === 'pending') {
            Order::update((int) $order['id'], ['status' => 'cancelled']);
            flash('success', 'Đã huỷ đơn hàng #' . $order['id'] . '.');
        } else {
            flash('error', 'Đơn hàng đã được xác nhận, không thể huỷ.');
        }

        $this->redirect('/orders/' . $order['id']);
    }
}
