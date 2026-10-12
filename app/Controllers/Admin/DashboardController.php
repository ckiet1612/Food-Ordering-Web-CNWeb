<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Food;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin/dashboard/index', [
            'title'       => 'Tổng quan',
            'stats'       => Order::stats(),
            'customers'   => User::countCustomers(),
            'foodCount'   => count(Food::all()),
            'recent'      => Order::recent(),
            'bestSellers' => Food::bestSellers(),
        ], 'admin');
    }
}
