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
        $food = Food::find((int) $this->input('food_id'));

        if ($food === null || !$food['is_available']) {
            $this->respond('Món này hiện không còn bán.', 422);
        }

        Cart::add((int) $food['id'], max(1, (int) $this->input('quantity', 1)));

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
        Cart::update((int) $this->input('food_id'), (int) $this->input('quantity'));
        $this->respond(null);
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
