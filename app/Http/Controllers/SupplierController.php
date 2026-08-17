<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));

        $suppliers = Supplier::query()
            ->where('company_id', $companyId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('trade_name', 'like', "%{$search}%")
                        ->orWhere('document', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => ['q' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Suppliers/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $this->validatedData($request);
        $data['company_id'] = $companyId;

        Supplier::create($data);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fornecedor cadastrado com sucesso.');
    }

    public function show(Supplier $supplier): Response
    {
        abort_unless((int) $supplier->company_id === app('currentCompany')->id, 404);

        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    public function edit(Supplier $supplier): Response
    {
        abort_unless((int) $supplier->company_id === app('currentCompany')->id, 404);

        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        abort_unless((int) $supplier->company_id === app('currentCompany')->id, 404);

        $data = $this->validatedData($request);
        $supplier->update($data);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Fornecedor atualizado com sucesso.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['pf', 'pj'])],
            'name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:20'],
            'state_registration' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:12'],
            'street' => ['nullable', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'size:2'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}
