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
        // Tái sử dụng Model hiện có: hai truy vấn, không query lặp theo từng danh mục.
        $byCategory = [];
        foreach (Food::where('is_available', 1) as $food) {
            $byCategory[(int) $food['category_id']][] = $food;
        }
        $sections = [];
        foreach (Category::ordered() as $category) {
            $foods = $byCategory[(int) $category['id']] ?? [];
            if (!$foods) continue;
            usort($foods, fn ($a, $b) => (int) $a['id'] <=> (int) $b['id']);
            $sections[] = $category + ['foods' => $foods];
        }
        $this->view('food/index', ['title' => 'Thực đơn', 'sections' => $sections]);
    }

    // GET /menu/{slug}
    public function category(string $slug): void
    {
        $category = Category::findBySlug($slug) ?? $this->notFound();

        // Giữ URL cũ hoạt động, dùng chung một giao diện thực đơn liên tục.
        $anchor = Food::byCategory((int) $category['id']) ? '#menu-category-' . $category['id'] : '';
        $this->redirect('/menu' . $anchor);
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
