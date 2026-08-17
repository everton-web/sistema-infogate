<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function index(): Response
    {
        $company = app('currentCompany');

        $vehicles = Vehicle::query()
            ->with(['customer', 'brand', 'model'])
            ->where('company_id', $company->id)
            ->latest()
            ->paginate(20);

        return Inertia::render('Vehicles/Index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function create(): Response
    {
        $company = app('currentCompany');

        $customers = Customer::query()
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $brands = VehicleBrand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Vehicles/Create', [
            'customers' => $customers,
            'brands' => $brands,
        ]);
    }

    public function models(VehicleBrand $brand): JsonResponse
    {
        $models = VehicleModel::query()
            ->where('vehicle_brand_id', $brand->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($models);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = app('currentCompany');

        $plate = strtoupper(
            preg_replace('/\s+/', '', trim((string) $request->input('plate')))
        );

        if (preg_match('/^[A-Z]{3}[0-9]{4}$/', $plate)) {
            $plate = substr($plate, 0, 3) . '-' . substr($plate, 3);
        }

        if (
            ! preg_match('/^[A-Z]{3}-[0-9]{4}$/', $plate)
            && ! preg_match('/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', $plate)
        ) {
            return back()
                ->withErrors(['plate' => 'Informe uma placa brasileira válida: ABC-1234 ou ABC1D23.'])
                ->withInput();
        }

        $request->merge(['plate' => $plate]);

        $validated = $request->validate([
            'customer_id' => [
                'nullable',
                Rule::exists('customers', 'id')
                    ->where(fn ($query) => $query->where('company_id', $company->id)),
            ],
            'customer_name' => [
                Rule::requiredIf(! $request->filled('customer_id')),
                'nullable', 'string', 'max:255',
            ],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'vehicle_brand_id' => ['required', 'integer', 'exists:vehicle_brands,id'],
            'vehicle_model_id' => ['required', 'integer', 'exists:vehicle_models,id'],
            'plate' => [
                'required', 'string', 'max:8',
                Rule::unique('vehicles', 'plate')
                    ->where(fn ($query) => $query->where('company_id', $company->id)),
            ],
            'version' => ['nullable', 'string', 'max:120'],
            'year_manufacture' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'year_model' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 2)],
            'color' => ['nullable', 'string', 'max:50'],
            'chassis' => ['nullable', 'string', 'max:30'],
            'odometer' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $modelIsValid = VehicleModel::query()
            ->where('id', $validated['vehicle_model_id'])
            ->where('vehicle_brand_id', $validated['vehicle_brand_id'])
            ->exists();

        if (! $modelIsValid) {
            return back()
                ->withErrors(['vehicle_model_id' => 'O modelo selecionado não pertence à marca informada.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $company) {
            if (! empty($validated['customer_id'])) {
                $customer = Customer::query()
                    ->where('company_id', $company->id)
                    ->findOrFail($validated['customer_id']);
            } else {
                $customer = Customer::create([
                    'company_id' => $company->id,
                    'type' => 'pf',
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'] ?? null,
                    'whatsapp' => $validated['customer_phone'] ?? null,
                    'status' => 'active',
                ]);
            }

            Vehicle::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'vehicle_brand_id' => $validated['vehicle_brand_id'],
                'vehicle_model_id' => $validated['vehicle_model_id'],
                'plate' => $validated['plate'],
                'version' => $validated['version'] ?? null,
                'year_manufacture' => $validated['year_manufacture'] ?? null,
                'year_model' => $validated['year_model'] ?? null,
                'color' => $validated['color'] ?? null,
                'chassis' => $validated['chassis'] ?? null,
                'odometer' => $validated['odometer'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'active',
            ]);
        });

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Veículo cadastrado com sucesso.');
    }
}
