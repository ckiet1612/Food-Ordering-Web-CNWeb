<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Upload;
use App\Models\Category;
use App\Models\Food;

class FoodController extends Controller
{
    private const RULES = [
        'name'        => 'required|max:150',
        'category_id' => 'required|numeric',
        'price'       => 'required|numeric',
        'description' => 'max:1000',
        'bg_color'    => 'required',
    ];

    private const LABELS = [
        'name'        => 'Tên món',
        'category_id' => 'Danh mục',
        'price'       => 'Giá',
        'description' => 'Mô tả',
        'bg_color'    => 'Màu nền',
    ];

    public function index(): void
    {
        $this->view('admin/food/index', ['title' => 'Món ăn', 'items' => Food::allWithCategory()], 'admin');
    }

    public function create(): void
    {
        $this->view('admin/food/create', ['title' => 'Thêm món ăn', 'categories' => Category::ordered()], 'admin');
    }

    public function store(): void
    {
        $data = $this->validate(self::RULES, self::LABELS, '/admin/foods/create');
        $image = $this->uploadOrBack('/admin/foods/create');

        Food::create($this->payload($data) + ['image' => $image, 'slug' => $this->uniqueSlug($data['name'])]);

        flash('success', 'Đã thêm món "' . $data['name'] . '".');
        $this->redirect('/admin/foods');
    }

    public function edit(string $id): void
    {
        $item = Food::find((int) $id) ?? $this->notFound();

        $this->view('admin/food/edit', [
            'title'      => 'Sửa món ăn',
            'item'       => $item,
            'categories' => Category::ordered(),
        ], 'admin');
    }

    public function update(string $id): void
    {
        $item = Food::find((int) $id) ?? $this->notFound();
        $back = '/admin/foods/' . $item['id'] . '/edit';

        $data = $this->validate(self::RULES, self::LABELS, $back);
        $payload = $this->payload($data);

        if ($data['name'] !== $item['name']) {
            $payload['slug'] = $this->uniqueSlug($data['name'], (int) $item['id']);
        }

        if ($image = $this->uploadOrBack($back)) {
            Upload::delete($item['image']);
            $payload['image'] = $image;
        }

        Food::update((int) $item['id'], $payload);

        flash('success', 'Đã cập nhật món "' . $data['name'] . '".');
        $this->redirect('/admin/foods');
    }

    public function destroy(string $id): void
    {
        $item = Food::find((int) $id) ?? $this->notFound();

        try {
            Food::delete((int) $item['id']);
            Upload::delete($item['image']);
            flash('success', 'Đã xoá món "' . $item['name'] . '".');
        } catch (\PDOException) {
            // Món đã nằm trong đơn hàng (khoá ngoại RESTRICT) -> chỉ ẩn đi
            Food::update((int) $item['id'], ['is_available' => 0]);
            flash('error', 'Món "' . $item['name'] . '" đã có trong đơn hàng nên không thể xoá. Đã chuyển sang trạng thái ngừng bán.');
        }

        $this->redirect('/admin/foods');
    }

    private function payload(array $data): array
    {
        return [
            'name'         => $data['name'],
            'category_id'  => (int) $data['category_id'],
            'price'        => max(0, (int) $data['price']),
            'description'  => $data['description'] ?? '',
            'bg_color'     => preg_match('/^#[0-9a-f]{6}$/i', $data['bg_color']) ? $data['bg_color'] : '#f9c94d',
            'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ];
    }

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = slugify($name) ?: 'mon-an';
        $slug = $base;

        for ($i = 2; Food::slugExists($slug, $exceptId); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    private function uploadOrBack(string $back): ?string
    {
        try {
            return Upload::image('image', 'foods');
        } catch (\RuntimeException $e) {
            $_SESSION['_errors'] = ['image' => $e->getMessage()];
            $_SESSION['_old'] = $_POST;
            $this->redirect($back);
        }
    }
}
