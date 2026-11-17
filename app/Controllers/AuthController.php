<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (auth()) {
            $this->redirect('/');
        }
        $this->view('auth/login', ['title' => 'Đăng nhập']);
    }

    public function login(): void
    {
        $data = $this->validate(
            ['email' => 'required|email', 'password' => 'required'],
            ['email' => 'Email', 'password' => 'Mật khẩu'],
            '/login'
        );

        $user = User::findByEmail($data['email']);

        if ($user === null || !password_verify($data['password'], $user['password'])) {
            $_SESSION['_old'] = ['email' => $data['email']];
            flash('error', 'Email hoặc mật khẩu không đúng.');
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;

        $intended = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);

        flash('success', 'Chào mừng trở lại, ' . $user['name'] . '!');
        $this->redirect($intended ?? match ($user['role']) { 'admin' => '/admin', 'shipper' => '/shipper', default => '/' });
    }

    public function showRegister(): void
    {
        if (auth()) {
            $this->redirect('/');
        }
        $this->view('auth/register', ['title' => 'Đăng ký']);
    }

    public function register(): void
    {
        $data = $this->validate([
            'name'                  => 'required|max:100',
            'email'                 => 'required|email|max:150',
            'phone'                 => 'required|phone',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ], [
            'name'                  => 'Họ tên',
            'email'                 => 'Email',
            'phone'                 => 'Số điện thoại',
            'password'              => 'Mật khẩu',
            'password_confirmation' => 'Mật khẩu xác nhận',
        ], '/register');

        if (User::findByEmail($data['email']) !== null) {
            $_SESSION['_errors'] = ['email' => 'Email này đã được sử dụng.'];
            $_SESSION['_old'] = ['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone']];
            $this->redirect('/register');
        }

        try {
            $id = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ]);
        } catch (\PDOException $e) {
            // UNIQUE email vẫn là lớp bảo vệ cuối khi hai request đăng ký đồng thời.
            if (($e->errorInfo[1] ?? null) !== 1062) { throw $e; }
            $_SESSION['_errors'] = ['email' => 'Email này đã được sử dụng.'];
            $_SESSION['_old'] = array_intersect_key($data, array_flip(['name', 'email', 'phone']));
            $this->redirect('/register');
        }

        // Đăng nhập luôn sau khi đăng ký
        session_regenerate_id(true);
        $user = User::find($id);
        unset($user['password']);
        $_SESSION['user'] = $user;

        flash('success', 'Tạo tài khoản thành công. Chúc bạn ngon miệng!');
        $intended = $_SESSION['intended'] ?? '/';
        unset($_SESSION['intended']);
        $this->redirect($intended);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        session_regenerate_id(true);
        flash('success', 'Bạn đã đăng xuất.');
        $this->redirect('/');
    }
}
