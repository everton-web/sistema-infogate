<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $companyQuery = fn () => $user->companies()
            ->wherePivot('is_active', true)
            ->where('companies.status', 'active');

        $companyId = (int) $request->session()->get('current_company_id', 0);

        $company = null;

        if ($companyId) {
            $company = $companyQuery()
                ->where('companies.id', $companyId)
                ->first();
        }

        $company ??= $companyQuery()
            ->orderBy('companies.id')
            ->first();

        abort_if(
            ! $company,
            403,
            'Este usuário não possui empresa ativa vinculada.'
        );

        $branch = $user->branches()
            ->wherePivot('is_active', true)
            ->where('branches.company_id', $company->id)
            ->where('branches.status', 'active')
            ->orderByDesc('branches.is_main')
            ->first();

        $request->session()->put('current_company_id', $company->id);

        if ($branch) {
            $request->session()->put('current_branch_id', $branch->id);
        }

        app()->instance('currentCompany', $company);
        app()->instance('currentBranch', $branch);

        View::share([
            'currentCompany' => $company,
            'currentBranch' => $branch,
        ]);

        return $next($request);
    }
}
