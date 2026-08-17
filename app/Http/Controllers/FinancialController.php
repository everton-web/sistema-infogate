<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FinancialEntry;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FinancialController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $type = $request->get('type');
        $status = $request->get('status');

        $entries = FinancialEntry::query()
            ->where('company_id', $companyId)
            ->with(['customer:id,name', 'supplier:id,name'])
            ->when($search, function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%");
            })
            ->when(in_array($type, ['receivable', 'payable'], true), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when(
                in_array($status, ['pending', 'paid', 'overdue', 'cancelled'], true),
                function ($query) use ($status) {
                    $query->where('status', $status);
                }
            )
            ->orderBy('due_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Financial/Index', [
            'entries' => $entries,
            'filters' => ['q' => $search, 'type' => $type, 'status' => $status],
        ]);
    }

    public function create(): Response
    {
        $companyId = app('currentCompany')->id;

        $customers = Customer::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $suppliers = Supplier::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Financial/Create', [
            'customers' => $customers,
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'type' => ['required', Rule::in(['receivable', 'payable'])],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['required', 'date'],
            'customer_id' => ['nullable', Rule::exists('customers', 'id')->where('company_id', $companyId)],
            'supplier_id' => ['nullable', Rule::exists('suppliers', 'id')->where('company_id', $companyId)],
            'notes' => ['nullable', 'string'],
        ]);

        $entry = FinancialEntry::create([
            'company_id' => $companyId,
            'type' => $data['type'],
            'status' => 'pending',
            'description' => $data['description'],
            'category' => $data['category'] ?? null,
            'amount' => $data['amount'],
            'paid_amount' => 0,
            'due_date' => $data['due_date'],
            'customer_id' => $data['customer_id'] ?? null,
            'supplier_id' => $data['supplier_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('financial.show', $entry)
            ->with('success', 'Lançamento financeiro criado com sucesso.');
    }

    public function show(FinancialEntry $entry): Response
    {
        abort_unless((int) $entry->company_id === app('currentCompany')->id, 404);

        $entry->load(['customer', 'supplier']);

        return Inertia::render('Financial/Show', [
            'entry' => $entry,
        ]);
    }

    public function pay(Request $request, FinancialEntry $entry): RedirectResponse
    {
        abort_unless((int) $entry->company_id === app('currentCompany')->id, 404);

        $data = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0.01'],
            'paid_date' => ['nullable', 'date'],
            'payment_method' => [
                'required',
                Rule::in(['cash', 'credit_card', 'debit_card', 'pix', 'boleto', 'transfer', 'other']),
            ],
        ]);

        $entry->update([
            'status' => 'paid',
            'paid_amount' => $data['paid_amount'],
            'paid_date' => $data['paid_date'] ?? now()->toDateString(),
            'payment_method' => $data['payment_method'],
        ]);

        return redirect()->back()->with('success', 'Lançamento marcado como pago.');
    }
}
