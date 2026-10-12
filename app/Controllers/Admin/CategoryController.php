<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Upload;
use App\Models\Category;

class CategoryController extends Controller
{
    private const RULES = [
        'name'        => 'required|max:100',
        'description' => 'max:500',
        'color'       => 'required',
        'sort_order'  => 'numeric',
    ];

    private const LABELS = [
        'name'        => 'Tên danh mục',
        'description' => 'Mô tả',
        'color'       => 'Màu nền',
        'sort_order'  => 'Thứ tự',
    ];

    public function index(): void
    {
        $this->view('admin/category/index', ['title' => 'Danh mục', 'items' => Category::withFoodCount()], 'admin');
    }

    public function create(): void
    {
        $this->view('admin/category/create', ['title' => 'Thêm danh mục'], 'admin');
    }

    public function store(): void
    {
        $data = $this->validate(self::RULES, self::LABELS, '/admin/categories/create');
        $image = $this->uploadOrBack('/admin/categories/create');

        Category::create($this->payload($data) + ['image' => $image, 'slug' => $this->uniqueSlug($data['name'])]);

        flash('success', 'Đã thêm danh mục "' . $data['name'] . '".');
        $this->redirect('/admin/categories');
    }

    public function edit(string $id): void
    {
        $item = Category::find((int) $id) ?? $this->notFound();

        $this->view('admin/category/edit', ['title' => 'Sửa danh mục', 'item' => $item], 'admin');
    }

    public function update(string $id): void
    {
        $item = Category::find((int) $id) ?? $this->notFound();
        $back = '/admin/categories/' . $item['id'] . '/edit';

        $data = $this->validate(self::RULES, self::LABELS, $back);
        $payload = $this->payload($data);

        if ($data['name'] !== $item['name']) {
            $payload['slug'] = $this->uniqueSlug($data['name'], (int) $item['id']);
        }

        if ($image = $this->uploadOrBack($back)) {
            Upload::delete($item['image']);
            $payload['image'] = $image;
        }

        Category::update((int) $item['id'], $payload);

        flash('success', 'Đã cập nhật danh mục "' . $data['name'] . '".');
        $this->redirect('/admin/categories');
    }

    public function destroy(string $id): void
    {
        $item = Category::find((int) $id) ?? $this->notFound();

        try {
            Category::delete((int) $item['id']);
            Upload::delete($item['image']);
            flash('success', 'Đã xoá danh mục "' . $item['name'] . '".');
        } catch (\PDOException) {
            flash('error', 'Danh mục "' . $item['name'] . '" vẫn còn món ăn, hãy chuyển hoặc xoá các món trước.');
        }

        $this->redirect('/admin/categories');
    }

    private function payload(array $data): array
    {
        return [
            'name'        => $data['name'],
            'description' => $data['description'] ?? '',
            'color'       => preg_match('/^#[0-9a-f]{6}$/i', $data['color']) ? $data['color'] : '#f9c94d',
            'sort_order'  => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = slugify($name) ?: 'danh-muc';
        $slug = $base;

        for ($i = 2; ($found = Category::findBySlug($slug)) && (int) $found['id'] !== $exceptId; $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    private function uploadOrBack(string $back): ?string
    {
        try {
            return Upload::image('image', 'categories');
        } catch (\RuntimeException $e) {
            $_SESSION['_errors'] = ['image' => $e->getMessage()];
            $_SESSION['_old'] = $_POST;
            $this->redirect($back);
        }
    }
}
