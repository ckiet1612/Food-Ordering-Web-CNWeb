<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Food;

class FoodController extends Controller
{
    // GET /menu
    public function index(): void
    {
        $this->view('food/index', ['title' => 'Thực đơn', 'categories' => Category::ordered()]);
    }

    // GET /menu/{slug}
    public function category(string $slug): void
    {
        $category = Category::findBySlug($slug) ?? $this->notFound();

        $this->view('food/category', [
            'title'      => $category['name'],
            'category'   => $category,
            'categories' => Category::ordered(),
            'foods'      => Food::byCategory((int) $category['id']),
        ]);
    }

    // GET /foods/{slug}
    public function show(string $slug): void
    {
        $food = Food::findBySlug($slug) ?? $this->notFound();

        $this->view('food/detail', [
            'title'   => $food['name'],
            'food'    => $food,
            'related' => Food::related($food),
        ]);
    }

    // GET /search?q=
    public function search(): void
    {
        $keyword = (string) $this->input('q', '');

        $this->view('food/search', [
            'title'   => 'Tìm kiếm',
            'keyword' => $keyword,
            'foods'   => $keyword === '' ? [] : Food::search($keyword),
        ]);
    }
}
