<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'auth' => fn () => $request->user() ? [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'is_super_admin' => $request->user()->is_super_admin,
                ],
            ] : null,

            'currentCompany' => fn () => app()->bound('currentCompany')
                ? [
                    'id' => app('currentCompany')->id,
                    'name' => app('currentCompany')->name,
                    'trade_name' => app('currentCompany')->trade_name,
                ]
                : null,

            'currentBranch' => fn () => app()->bound('currentBranch') && app('currentBranch')
                ? [
                    'id' => app('currentBranch')->id,
                    'name' => app('currentBranch')->name,
                ]
                : null,

            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
