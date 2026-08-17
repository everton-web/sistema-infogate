<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        return Inertia::render('Dashboard', [
            'totalCustomers' => Customer::where('company_id', $companyId)->count(),
            'activeCustomers' => Customer::where('company_id', $companyId)->where('active', true)->count(),
            'totalVehicles' => Vehicle::where('company_id', $companyId)->count(),
            'recentCustomers' => Customer::where('company_id', $companyId)
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'document', 'phone']),
            'recentVehicles' => Vehicle::where('company_id', $companyId)
                ->with(['brand:id,name', 'vehicleModel:id,name'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($v) => [
                    'id' => $v->id,
                    'plate' => $v->plate,
                    'brand_name' => $v->brand?->name,
                    'model_name' => $v->vehicleModel?->name,
                    'year' => $v->year,
                ]),
        ]);
    }
}
