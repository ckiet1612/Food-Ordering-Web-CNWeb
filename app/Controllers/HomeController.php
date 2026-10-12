<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Food;

class HomeController extends Controller
{
    public const STORES = [
        ['name' => 'Yumio Bách Khoa', 'address' => '1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội', 'hours' => '07:00 – 23:00', 'phone' => '024 3868 1234'],
        ['name' => 'Yumio Hoàn Kiếm', 'address' => '25 Hàng Bài, Hoàn Kiếm, Hà Nội', 'hours' => '07:00 – 23:30', 'phone' => '024 3936 5678'],
        ['name' => 'Yumio Cầu Giấy', 'address' => '144 Xuân Thuỷ, Cầu Giấy, Hà Nội', 'hours' => '08:00 – 22:30', 'phone' => '024 3754 2468'],
        ['name' => 'Yumio Quận 1', 'address' => '68 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 'hours' => '07:00 – 24:00', 'phone' => '028 3822 1357'],
        ['name' => 'Yumio Thảo Điền', 'address' => '12 Quốc Hương, Thủ Đức, TP. Hồ Chí Minh', 'hours' => '08:00 – 22:00', 'phone' => '028 3744 9753'],
        ['name' => 'Yumio Hải Châu', 'address' => '90 Bạch Đằng, Hải Châu, Đà Nẵng', 'hours' => '07:30 – 22:30', 'phone' => '0236 382 8642'],
    ];

    public function index(): void
    {
        $featured = Food::featured(10);

        $this->view('home/index', [
            'title'       => 'Đồ ăn nhanh, ngon thật',
            'featured'    => $featured,
            'categories'  => Category::ordered(),
            'stores'      => array_slice(self::STORES, 0, 3),
            'heroFood'    => $this->nthOf('combo', 0) ?? ($featured[0] ?? null),
            'featureFood' => $this->nthOf('combo', 2) ?? ($featured[1] ?? null),
            'clubFood'    => $this->nthOf('ga-ran', 2) ?? ($featured[2] ?? null),
        ]);
    }

    private function nthOf(string $categorySlug, int $index): ?array
    {
        $category = Category::findBySlug($categorySlug);
        return $category ? (Food::byCategory((int) $category['id'])[$index] ?? null) : null;
    }

    public function about(): void
    {
        $this->view('home/about', ['title' => 'Về Yumio']);
    }

    public function stores(): void
    {
        $this->view('home/stores', ['title' => 'Cửa hàng', 'stores' => self::STORES]);
    }
}
