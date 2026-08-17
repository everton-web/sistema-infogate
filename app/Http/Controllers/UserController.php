<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $query = User::whereHas('companies', fn ($q) => $q->where('company_id', $companyId));

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')
            ->paginate(20)
            ->through(function ($user) use ($companyId) {
                $pivot = $user->companies->firstWhere('id', $companyId)?->pivot;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_super_admin' => $user->is_super_admin,
                    'role' => $pivot?->role ?? 'user',
                    'is_active' => (bool) ($pivot?->is_active ?? false),
                    'created_at' => $user->created_at,
                ];
            })
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => ['q' => $request->input('q', '')],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->companies()->attach(app('currentCompany')->id, [
            'role' => $data['role'],
            'is_active' => true,
        ]);

        $branch = app()->bound('currentBranch') ? app('currentBranch') : null;
        if ($branch) {
            $user->branches()->attach($branch->id, ['is_active' => true]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user): Response
    {
        $companyId = app('currentCompany')->id;
        abort_unless($user->companies()->where('company_id', $companyId)->exists(), 404);

        $pivot = $user->companies->firstWhere('id', $companyId)?->pivot;

        return Inertia::render('Users/Edit', [
            'editUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_super_admin' => $user->is_super_admin,
                'role' => $pivot?->role ?? 'user',
                'is_active' => (bool) ($pivot?->is_active ?? false),
            ],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $companyId = app('currentCompany')->id;
        abort_unless($user->companies()->where('company_id', $companyId)->exists(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
            'is_active' => ['boolean'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            ...(filled($data['password'] ?? null) ? ['password' => Hash::make($data['password'])] : []),
        ]);

        $user->companies()->updateExistingPivot($companyId, [
            'role' => $data['role'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }
}
