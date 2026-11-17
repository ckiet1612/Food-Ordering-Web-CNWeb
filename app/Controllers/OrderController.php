<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Delivery;
use App\Models\User;

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

        // Mỗi trang checkout có token riêng: tab cũ không được đặt giỏ đã đổi ở tab khác.
        $_SESSION['checkout_token'] = bin2hex(random_bytes(32));
        $_SESSION['checkout_snapshot'] = $this->snapshot($cart);
        unset($_SESSION['checkout_review']);
        $this->view('order/checkout', ['title' => 'Thanh toán', 'cart' => $cart, 'checkoutToken' => $_SESSION['checkout_token']]);
    }

    // Bước xem lại không ghi đơn, không xoá giỏ; vẫn hoạt động khi tắt JavaScript.
    public function review(): void
    {
        $token = $_POST['checkout_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['checkout_token'] ?? '', $token) || $token === '') {
            flash('error', 'Phiên thanh toán đã thay đổi. Vui lòng kiểm tra lại.');
            $this->redirect('/checkout');
        }
        $data = $this->checkoutData();
        $cart = Cart::detailed();
        if (!$cart['items'] || ($_SESSION['checkout_snapshot'] ?? null) !== $this->snapshot($cart)) {
            $_SESSION['_old'] = $data;
            flash('error', 'Giỏ hàng đã thay đổi. Vui lòng kiểm tra lại trước khi xác nhận.');
            $this->redirect('/checkout');
        }
        try {
            $data += Delivery::location($_POST['delivery_lat'] ?? null, $_POST['delivery_lng'] ?? null);
        } catch (\DomainException $e) {
            $_SESSION['_old'] = $data;
            flash('error', $e->getMessage());
            $this->redirect('/checkout');
        }
        $_SESSION['checkout_review'] = $this->reviewSignature($data, $cart, $token);
        $_SESSION['_old'] = $data;
        $this->view('order/review', ['title' => 'Xem lại đơn hàng', 'cart' => $cart, 'info' => $data, 'checkoutToken' => $token]);
    }

    // POST /checkout
    public function store(): void
    {
        $token = $_POST['checkout_token'] ?? '';
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            flash('error', 'Phiên đặt hàng không hợp lệ. Vui lòng kiểm tra lại đơn.');
            $this->redirect('/checkout');
        }
        if ($existing = Order::byCheckoutToken((int) auth()['id'], $token)) {
            $this->redirect('/orders/' . $existing['id']);
        }
        if (!hash_equals($_SESSION['checkout_token'] ?? '', $token)) {
            flash('error', 'Phiên đặt hàng đã thay đổi. Vui lòng kiểm tra lại đơn.');
            $this->redirect('/checkout');
        }
        if (($_POST['checkout_action'] ?? '') !== 'confirm') {
            flash('error', 'Hãy xem lại đơn và chủ động nhấn xác nhận đặt hàng.');
            $this->redirect('/checkout');
        }
        $data = $this->checkoutData();

        $cart = Cart::detailed();
        if ($cart['items'] === []) {
            $this->redirect('/cart');
        }

        if (($_SESSION['checkout_snapshot'] ?? null) !== $this->snapshot($cart)) {
            $_SESSION['_old'] = $data;
            flash('error', 'Giỏ hàng hoặc giá đã thay đổi. Vui lòng kiểm tra lại trước khi xác nhận.');
            $this->redirect('/checkout');
        }
        try {
            $location = Delivery::location($_POST['delivery_lat'] ?? null, $_POST['delivery_lng'] ?? null);
            if (!hash_equals($_SESSION['checkout_review'] ?? '', $this->reviewSignature($data + $location, $cart, $token))) {
                throw new \DomainException('Thông tin đơn cần được xem lại trước khi xác nhận.');
            }
            $orderId = Order::place((int) auth()['id'], [
                'receiver_name'  => $data['receiver_name'],
                'phone'          => $data['phone'],
                'address'        => $data['address'],
                'note'           => ($data['note'] ?? '') !== '' ? $data['note'] : null,
                'payment_method' => $data['payment_method'],
                'checkout_token' => $token,
            ] + $location, $cart);
        } catch (\Throwable $e) {
            error_log('Checkout: ' . $e->getMessage());
            $_SESSION['_old'] = array_intersect_key(array_filter($_POST, 'is_scalar'), array_flip(['receiver_name', 'phone', 'address', 'note', 'payment_method', 'delivery_lat', 'delivery_lng']));
            flash('error', $e instanceof \DomainException ? $e->getMessage() : 'Chưa lưu được đơn hàng. Giỏ hàng được giữ lại, vui lòng thử lại.');
            $this->redirect('/checkout');
        }

        Cart::clear();
        unset($_SESSION['checkout_token'], $_SESSION['checkout_snapshot'], $_SESSION['checkout_review'], $_SESSION['_old']);
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
            'events' => Order::events((int) $order['id']),
            'shipper' => $order['shipper_id'] ? User::find((int) $order['shipper_id']) : null,
        ]);
    }

    // POST /orders/{id}/cancel — khách chỉ huỷ được khi đơn còn chờ xác nhận
    public function cancel(string $id): void
    {
        $order = Order::find((int) $id);

        if ($order === null || (int) $order['user_id'] !== (int) auth()['id']) {
            $this->notFound();
        }

        try {
            Order::transition((int) $order['id'], 'cancelled', (int) auth()['id']);
            flash('success', 'Đã huỷ đơn hàng #' . $order['id'] . '.');
        } catch (\DomainException $e) {
            flash('error', $e->getMessage());
        }

        $this->redirect('/orders/' . $order['id']);
    }

    private function snapshot(array $cart): array
    {
        $snapshot = [];
        foreach ($cart['items'] as $item) {
            $snapshot[(int) $item['id']] = [(int) $item['price'], (int) $item['quantity']];
        }
        ksort($snapshot);
        return $snapshot;
    }

    private function checkoutData(): array
    {
        $data = $this->validate([
            'receiver_name' => 'required|max:100', 'phone' => 'required|phone',
            'address' => 'required|max:255', 'note' => 'max:500', 'payment_method' => 'required|in:cod',
        ], [
            'receiver_name' => 'Tên người nhận', 'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ giao hàng', 'note' => 'Ghi chú', 'payment_method' => 'Phương thức thanh toán',
        ], '/checkout');
        $data['note'] ??= '';
        return $data;
    }

    private function reviewSignature(array $data, array $cart, string $token): string
    {
        unset($data['distance_km']); // Giá trị suy ra, không phải đầu vào người dùng xác nhận.
        ksort($data);
        return hash('sha256', json_encode([$token, $data, $this->snapshot($cart)], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }
}
