<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ServiceOrder;
use App\Models\Vehicle;
use App\Models\Warranty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class WarrantyController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $status = $request->get('status');

        $warranties = Warranty::query()
            ->where('company_id', $companyId)
            ->with(['customer:id,name', 'vehicle:id,plate'])
            ->when($search, function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->where('number', $search);
                } else {
                    $query->whereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                }
            })
            ->when(
                in_array($status, ['active', 'claimed', 'expired', 'voided'], true),
                function ($query) use ($status) {
                    $query->where('status', $status);
                }
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Warranties/Index', [
            'warranties' => $warranties,
            'filters' => ['q' => $search, 'status' => $status],
        ]);
    }

    public function create(): Response
    {
        $companyId = app('currentCompany')->id;

        $customers = Customer::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $vehicles = Vehicle::where('company_id', $companyId)
            ->with(['brand:id,name', 'model:id,name'])
            ->orderBy('plate')
            ->get(['id', 'customer_id', 'plate', 'vehicle_brand_id', 'vehicle_model_id']);

        $serviceOrders = ServiceOrder::where('company_id', $companyId)
            ->orderByDesc('number')
            ->get(['id', 'number', 'customer_id']);

        return Inertia::render('Warranties/Create', [
            'customers' => $customers,
            'vehicles' => $vehicles,
            'serviceOrders' => $serviceOrders,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'customer_id' => ['required', Rule::exists('customers', 'id')->where('company_id', $companyId)],
            'vehicle_id' => ['nullable', Rule::exists('vehicles', 'id')->where('company_id', $companyId)],
            'service_order_id' => ['nullable', Rule::exists('service_orders', 'id')->where('company_id', $companyId)],
            'description' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'terms' => ['nullable', 'string'],
        ]);

        $lastNumber = Warranty::where('company_id', $companyId)->max('number') ?? 0;

        $warranty = Warranty::create([
            'company_id' => $companyId,
            'customer_id' => $data['customer_id'],
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'service_order_id' => $data['service_order_id'] ?? null,
            'number' => $lastNumber + 1,
            'description' => $data['description'],
            'status' => 'active',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'terms' => $data['terms'] ?? null,
        ]);

        return redirect()
            ->route('warranties.show', $warranty)
            ->with('success', 'Garantia #' . $warranty->number . ' criada com sucesso.');
    }

    public function show(Warranty $warranty): Response
    {
        abort_unless((int) $warranty->company_id === app('currentCompany')->id, 404);

        $warranty->load(['customer', 'vehicle', 'serviceOrder']);

        return Inertia::render('Warranties/Show', [
            'warranty' => $warranty,
        ]);
    }
}
