<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderParticipant;
use App\Models\UmrahPackage;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total_packages' => UmrahPackage::count(),
                'packages_approved' => UmrahPackage::where('status', 'approved')->count(),
                'packages_pending' => UmrahPackage::where('status', 'pending')->count(),
                'packages_regular' => UmrahPackage::where('type', 'regular')->count(),
                'packages_vip' => UmrahPackage::where('type', 'vip')->count(),
                'packages_promo' => UmrahPackage::where('type', 'promo')->count(),
                'total_orders' => Order::count(),
                'orders_waiting_payment' => Order::where('status', 'waiting_payment')->count(),
                'orders_waiting_confirmation' => Order::where('status', 'waiting_confirmation')->count(),
                'orders_complete' => Order::where('status', 'complete')->count(),
                'total_jamaah' => OrderParticipant::count(),
                'total_merchants' => User::role('merchants')->count(),
                'merchants_pending' => User::role('merchants')->where('status', 'pending')->count(),
                'total_customers' => User::role('user')->count(),
            ],
        ]);
    }
}
