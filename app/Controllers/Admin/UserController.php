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
}
