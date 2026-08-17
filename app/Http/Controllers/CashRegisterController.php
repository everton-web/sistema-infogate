<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CashRegisterController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $registers = CashRegister::query()
            ->where('company_id', $companyId)
            ->with(['user:id,name'])
            ->latest('opened_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('CashRegister/Index', [
            'registers' => $registers,
        ]);
    }

    public function open(): Response
    {
        return Inertia::render('CashRegister/Open');
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $register = CashRegister::create([
            'company_id' => $companyId,
            'branch_id' => app()->bound('currentBranch') && app('currentBranch') ? app('currentBranch')->id : null,
            'user_id' => $request->user()->id,
            'opening_balance' => $data['opening_balance'],
            'status' => 'open',
            'opened_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('cash-register.show', $register)
            ->with('success', 'Caixa aberto com sucesso.');
    }

    public function show(CashRegister $cashRegister): Response
    {
        abort_unless((int) $cashRegister->company_id === app('currentCompany')->id, 404);

        $cashRegister->load(['user', 'transactions']);

        return Inertia::render('CashRegister/Show', [
            'cashRegister' => $cashRegister,
        ]);
    }

    public function close(Request $request, CashRegister $cashRegister): RedirectResponse
    {
        abort_unless((int) $cashRegister->company_id === app('currentCompany')->id, 404);

        $data = $request->validate([
            'closing_balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $cashRegister->update([
            'closing_balance' => $data['closing_balance'],
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $data['notes'] ?? $cashRegister->notes,
        ]);

        return redirect()
            ->route('cash-register.show', $cashRegister)
            ->with('success', 'Caixa fechado com sucesso.');
    }
}
