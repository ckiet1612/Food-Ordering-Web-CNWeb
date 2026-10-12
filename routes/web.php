<?php

use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\FoodController as AdminFoodController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\FoodController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Middlewares\AdminMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\CsrfMiddleware;

/** @var App\Core\Router $router */

$auth  = [AuthMiddleware::class];
$admin = [AuthMiddleware::class, AdminMiddleware::class];
$csrf  = [CsrfMiddleware::class];

// ---------- Khách hàng ----------
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/stores', [HomeController::class, 'stores']);
$router->get('/menu', [FoodController::class, 'index']);
$router->get('/menu/{slug}', [FoodController::class, 'category']);
$router->get('/foods/{slug}', [FoodController::class, 'show']);
$router->get('/search', [FoodController::class, 'search']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login'], $csrf);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register'], $csrf);
$router->post('/logout', [AuthController::class, 'logout'], $csrf);

$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add'], $csrf);
$router->post('/cart/update', [CartController::class, 'update'], $csrf);
$router->post('/cart/remove', [CartController::class, 'remove'], $csrf);

$router->get('/checkout', [OrderController::class, 'checkout'], $auth);
$router->post('/checkout', [OrderController::class, 'store'], [...$auth, ...$csrf]);
$router->get('/orders', [OrderController::class, 'index'], $auth);
$router->get('/orders/{id}', [OrderController::class, 'show'], $auth);
$router->post('/orders/{id}/cancel', [OrderController::class, 'cancel'], [...$auth, ...$csrf]);

// ---------- Quản trị ----------
$router->get('/admin', [DashboardController::class, 'index'], $admin);

$router->get('/admin/foods', [AdminFoodController::class, 'index'], $admin);
$router->get('/admin/foods/create', [AdminFoodController::class, 'create'], $admin);
$router->post('/admin/foods', [AdminFoodController::class, 'store'], [...$admin, ...$csrf]);
$router->get('/admin/foods/{id}/edit', [AdminFoodController::class, 'edit'], $admin);
$router->post('/admin/foods/{id}', [AdminFoodController::class, 'update'], [...$admin, ...$csrf]);
$router->post('/admin/foods/{id}/delete', [AdminFoodController::class, 'destroy'], [...$admin, ...$csrf]);

$router->get('/admin/categories', [AdminCategoryController::class, 'index'], $admin);
$router->get('/admin/categories/create', [AdminCategoryController::class, 'create'], $admin);
$router->post('/admin/categories', [AdminCategoryController::class, 'store'], [...$admin, ...$csrf]);
$router->get('/admin/categories/{id}/edit', [AdminCategoryController::class, 'edit'], $admin);
$router->post('/admin/categories/{id}', [AdminCategoryController::class, 'update'], [...$admin, ...$csrf]);
$router->post('/admin/categories/{id}/delete', [AdminCategoryController::class, 'destroy'], [...$admin, ...$csrf]);

$router->get('/admin/orders', [AdminOrderController::class, 'index'], $admin);
$router->get('/admin/orders/{id}', [AdminOrderController::class, 'show'], $admin);
$router->post('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus'], [...$admin, ...$csrf]);

$router->get('/admin/users', [AdminUserController::class, 'index'], $admin);
