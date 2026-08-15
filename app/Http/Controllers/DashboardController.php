<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = app('currentCompany')->id;

        $customerMetrics = Customer::where('company_id', $companyId)
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active")
            ->first();

        $totalCustomers = (int) $customerMetrics->total;
        $activeCustomers = (int) $customerMetrics->active;
        $totalVehicles = Vehicle::where('company_id', $companyId)->count();

        $recentCustomers = Customer::where('company_id', $companyId)
            ->select([
                'id', 'name', 'type', 'phone', 'city', 'state', 'status', 'created_at',
            ])
            ->latest()
            ->limit(5)
            ->get();

        $recentVehicles = Vehicle::with([
            'customer:id,name',
            'brand:id,name',
            'model:id,name',
        ])
            ->where('company_id', $companyId)
            ->select([
                'id', 'customer_id', 'vehicle_brand_id', 'vehicle_model_id',
                'plate', 'year_manufacture', 'status', 'created_at',
            ])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'user' => $request->user(),
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'totalVehicles' => $totalVehicles,
            'recentCustomers' => $recentCustomers,
            'recentVehicles' => $recentVehicles,
        ]);
    }
}
