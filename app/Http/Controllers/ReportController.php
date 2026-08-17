<?php

namespace App\Http\Controllers;

use App\Models\FinancialEntry;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $salesTotal = Sale::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $purchasesTotal = Purchase::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $receivables = FinancialEntry::where('company_id', $companyId)
            ->where('type', 'receivable')
            ->whereIn('status', ['pending', 'overdue'])
            ->whereBetween('due_date', [$startDate, $endDate])
            ->sum('amount');

        $payables = FinancialEntry::where('company_id', $companyId)
            ->where('type', 'payable')
            ->whereIn('status', ['pending', 'overdue'])
            ->whereBetween('due_date', [$startDate, $endDate])
            ->sum('amount');

        $topProducts = SaleItem::query()
            ->select('sale_items.description', DB::raw('SUM(sale_items.quantity) as quantity'), DB::raw('SUM(sale_items.total) as total'))
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.company_id', $companyId)
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('sale_items.description')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topCustomers = Sale::query()
            ->select('customer_id', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(total) as total'))
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereNotNull('customer_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('customer:id,name')
            ->groupBy('customer_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return Inertia::render('Reports/Index', [
            'salesTotal' => $salesTotal,
            'purchasesTotal' => $purchasesTotal,
            'receivables' => $receivables,
            'payables' => $payables,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
            'filters' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
        ]);
    }
}
