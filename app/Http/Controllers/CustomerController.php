<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = session('current_company_id');

        $search = trim((string) $request->get('q'));
        $status = $request->get('status');

        $customers = Customer::query()
            ->where('company_id', $companyId)
            ->withCount('vehicles')
            ->when($search, function ($query) use ($search) {
                $digits = preg_replace('/\D+/', '', $search);

                $query->where(function ($sub) use ($search, $digits) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('trade_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('whatsapp', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                    if ($digits !== '') {
                        $sub->orWhere('document', 'like', "%{$digits}%");
                    }
                });
            })
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => [
                'q' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = session('current_company_id');

        $data = $this->validatedData($request, $companyId);

        $data['company_id'] = $companyId;
        $data['document'] = $this->digitsOrNull($data['document'] ?? null);
        $data['postal_code'] = $this->digitsOrNull($data['postal_code'] ?? null);
        $data['phone'] = $this->cleanText($data['phone'] ?? null);
        $data['whatsapp'] = $this->cleanText($data['whatsapp'] ?? null);
        $data['email'] = !empty($data['email'])
            ? mb_strtolower(trim($data['email']))
            : null;

        $customer = Customer::create($data);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Customer $customer): Response
    {
        $this->ensureCompany($customer);

        $customer->load([
            'vehicles' => function ($query) {
                $query->with(['brand', 'model'])->latest();
            },
        ]);

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }

    public function edit(Customer $customer): Response
    {
        $this->ensureCompany($customer);

        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $this->ensureCompany($customer);

        $companyId = session('current_company_id');

        $data = $this->validatedData($request, $companyId, $customer->id);

        $data['document'] = $this->digitsOrNull($data['document'] ?? null);
        $data['postal_code'] = $this->digitsOrNull($data['postal_code'] ?? null);
        $data['phone'] = $this->cleanText($data['phone'] ?? null);
        $data['whatsapp'] = $this->cleanText($data['whatsapp'] ?? null);
        $data['email'] = !empty($data['email'])
            ? mb_strtolower(trim($data['email']))
            : null;

        $customer->update($data);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    private function validatedData(Request $request, int $companyId, ?int $ignoreId = null): array
    {
        $documentRule = Rule::unique('customers', 'document')
            ->where(fn ($query) => $query->where('company_id', $companyId));

        if ($ignoreId) {
            $documentRule->ignore($ignoreId);
        }

        return $request->validate([
            'type' => ['required', Rule::in(['pf', 'pj'])],
            'name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:20', $documentRule],
            'state_registration' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
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

    private function ensureCompany(Customer $customer): void
    {
        abort_unless(
            (int) $customer->company_id === (int) session('current_company_id'),
            404
        );
    }

    private function digitsOrNull(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value);

        return $digits !== '' ? $digits : null;
    }

    private function cleanText(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
