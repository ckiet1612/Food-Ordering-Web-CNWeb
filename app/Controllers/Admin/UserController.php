<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $this->view('admin/user/index', ['title' => 'Người dùng', 'users' => User::withOrderStats()], 'admin');
    }

    public function updateRole(string $id): void
    {
        // Chỉ cấp quyền giao hàng cho khách; không có endpoint tự cấp quyền admin.
        $user = User::find((int) $id) ?? $this->notFound();
        if ($user['role'] === 'customer' && ($_POST['role'] ?? '') === 'shipper') {
            User::update((int) $id, ['role' => 'shipper']);
            flash('success', 'Đã cấp vai trò giao hàng cho ' . $user['name'] . '.');
        } else {
            flash('error', 'Chỉ có thể cấp vai trò giao hàng cho tài khoản khách hàng.');
        }
        $this->redirect('/admin/users');
    }
}
