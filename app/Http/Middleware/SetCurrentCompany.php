<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $session = $request->session();

        $cachedCompany = $session->get('cached_company');
        $cachedBranch = $session->get('cached_branch');
        $companyId = (int) $session->get('current_company_id', 0);
        $cacheCheckedAt = (int) $session->get('company_cache_checked_at', 0);

        $cacheValid = $cachedCompany
            && (int) $cachedCompany['id'] === $companyId
            && $companyId > 0
            && $cacheCheckedAt >= now()->subMinutes(5)->timestamp;

        if ($cacheValid) {
            $company = new Company($cachedCompany);
            $company->exists = true;
            $company->id = $cachedCompany['id'];

            $branch = null;
            if ($cachedBranch) {
                $branch = new Branch($cachedBranch);
                $branch->exists = true;
                $branch->id = $cachedBranch['id'];
            }
        } else {
            $companyQuery = fn () => $user->companies()
                ->wherePivot('is_active', true)
                ->where('companies.status', 'active');

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

            $session->put('current_company_id', $company->id);
            $session->put('cached_company', $company->toArray());
            $session->put('cached_branch', $branch?->toArray());
            $session->put('company_cache_checked_at', now()->timestamp);

            if ($branch) {
                $session->put('current_branch_id', $branch->id);
            } else {
                $session->forget('current_branch_id');
            }
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
