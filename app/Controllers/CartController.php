<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Food;

class CartController extends Controller
{
    public function index(): void
    {
        $this->view('cart/index', ['title' => 'Giỏ hàng', 'cart' => Cart::detailed()]);
    }

    public function add(): void
    {
        $quantity = $this->quantity(1);
        $food = Food::find((int) $this->input('food_id'));

        if ($food === null || !$food['is_available']) {
            $this->respond('Món này hiện không còn bán.', 422);
        }

        Cart::add((int) $food['id'], $quantity);

        $this->respond('Đã thêm vào giỏ hàng.', 200, [
            'item' => [
                'name'     => $food['name'],
                'price'    => format_price($food['price']),
                'image'    => image_url($food['image']),
                'bg_color' => $food['bg_color'],
            ],
        ]);
    }

    public function update(): void
    {
        $foodId = (int) $this->input('food_id');
        $quantity = $this->quantity();
        if (!isset(Cart::items()[$foodId])) {
            $this->respond('Món không còn trong giỏ hàng. Vui lòng tải lại trang.', 422);
        }
        Cart::update($foodId, $quantity);
        $this->respond(null);
    }

    private function quantity(?int $default = null): int
    {
        if (isset($_POST['quantity']) && !is_scalar($_POST['quantity'])) {
            $this->respond('Số lượng không hợp lệ.', 422);
        }
        $raw = $this->input('quantity', $default);
        if ($raw === null || filter_var($raw, FILTER_VALIDATE_INT) === false || (int) $raw < 1 || (int) $raw > Cart::MAX_QUANTITY) {
            $this->respond('Số lượng phải là số nguyên từ 1 đến ' . Cart::MAX_QUANTITY . '.', 422);
        }
        return (int) $raw;
    }

    public function remove(): void
    {
        Cart::remove((int) $this->input('food_id'));
        $this->respond('Đã xoá món khỏi giỏ hàng.');
    }

    // Trả JSON khi gọi bằng fetch(), ngược lại redirect về trang giỏ hàng
    private function respond(?string $message, int $status = 200, array $extra = []): never
    {
        if ($this->wantsJson()) {
            $cart = Cart::detailed();
            $this->json($extra + [
                'ok'                => $status < 400,
                'message'           => $message,
                'count'             => $cart['count'],
                'items'             => array_map(fn ($item) => [
                    'id' => (int) $item['id'], 'quantity' => (int) $item['quantity'],
                    'line_total' => format_price($item['line_total']),
                ], $cart['items']),
                'subtotal'          => format_price($cart['subtotal']),
                'shipping_fee'      => $cart['shipping_fee'] > 0 ? format_price($cart['shipping_fee']) : 'Miễn phí',
                'total'             => format_price($cart['total']),
                'shipping_progress' => $cart['shipping_progress'],
                'shipping_message'  => $cart['shipping_message'],
            ], $status);
        }

        if ($message !== null) {
            flash($status < 400 ? 'success' : 'error', $message);
        }

        $this->redirect('/cart');
    }
}
