<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\OrderItem;

class ShipperController extends Controller
{
    public function index(): void
    {
        $this->view('shipper/index', ['title' => 'Đơn giao của tôi', 'orders' => Order::forShipper((int) auth()['id'])]);
    }

    public function show(string $id): void
    {
        $order = $this->assigned($id);
        $this->view('shipper/detail', [
            'title' => 'Giao đơn #' . $order['id'], 'order' => $order,
            'items' => OrderItem::byOrder((int) $id), 'events' => Order::events((int) $id),
        ]);
    }

    public function updateStatus(string $id): void
    {
        $order = $this->assigned($id);
        try {
            $next = $_POST['status'] ?? '';
            Order::transition((int) $order['id'], is_string($next) ? $next : '', (int) auth()['id']);
            flash('success', 'Đã cập nhật tiến trình giao hàng.');
        } catch (\DomainException $e) {
            flash('error', $e->getMessage());
        }
        $this->redirect('/shipper/orders/' . $order['id']);
    }

    private function assigned(string $id): array
    {
        $order = Order::find((int) $id);
        if (!$order || (int) $order['shipper_id'] !== (int) auth()['id']) {
            $this->notFound();
        }
        return $order;
    }
}
