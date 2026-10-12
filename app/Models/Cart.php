<?php

declare(strict_types=1);

namespace App\Models;

// Giỏ hàng lưu trong session: [food_id => quantity]
class Cart
{
    public const MAX_QUANTITY = 50;

    public static function items(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public static function add(int $foodId, int $quantity = 1): void
    {
        self::update($foodId, (self::items()[$foodId] ?? 0) + $quantity);
    }

    public static function update(int $foodId, int $quantity): void
    {
        if ($quantity <= 0) {
            self::remove($foodId);
            return;
        }
        $_SESSION['cart'][$foodId] = min($quantity, self::MAX_QUANTITY);
    }

    public static function remove(int $foodId): void
    {
        unset($_SESSION['cart'][$foodId]);
    }

    public static function clear(): void
    {
        unset($_SESSION['cart']);
    }

    public static function count(): int
    {
        return array_sum(self::items());
    }

    // Ghép thông tin món ăn từ CSDL; bỏ qua món đã bị xoá hoặc ngừng bán
    public static function detailed(): array
    {
        $items = [];
        $subtotal = 0;

        foreach (self::items() as $foodId => $quantity) {
            $food = Food::find((int) $foodId);
            if ($food === null || !$food['is_available']) {
                self::remove((int) $foodId);
                continue;
            }

            $lineTotal = (int) $food['price'] * $quantity;
            $items[] = $food + ['quantity' => $quantity, 'line_total' => $lineTotal];
            $subtotal += $lineTotal;
        }

        $shippingFee = $items === [] ? 0 : Order::shippingFee($subtotal);

        return [
            'items'             => $items,
            'count'             => self::count(),
            'subtotal'          => $subtotal,
            'shipping_fee'      => $shippingFee,
            'total'             => $subtotal + $shippingFee,
            'shipping_progress' => min(1, $subtotal / Order::FREE_SHIPPING_FROM),
            'shipping_message'  => $subtotal >= Order::FREE_SHIPPING_FROM
                ? 'Đơn của bạn được <strong>miễn phí giao hàng</strong>.'
                : 'Mua thêm <strong>' . format_price(Order::FREE_SHIPPING_FROM - $subtotal) . '</strong> để được miễn phí giao hàng.',
        ];
    }
}
