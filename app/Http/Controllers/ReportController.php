<?php

namespace App\Http\Controllers;

use App\Exports\PortfolioExport;
use App\Exports\ProfitExport;
use App\Exports\TransactionsExport;
use App\Services\FamilyMemberService;
use App\Services\ReportService;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private FamilyMemberService $familyMemberService,
    ) {}

    public function portfolio()
    {
        return Inertia::render('Reports/Portfolio', [
            'portfolio' => $this->reportService->memberPortfolio(request('member_id')),
            'members' => $this->familyMemberService->getActiveMembers(),
        ]);
    }

    public function exportPortfolio()
    {
        return (new PortfolioExport(request('member_id')))->download('laporan-portfolio.xlsx');
    }

    public function transactions()
    {
        return Inertia::render('Reports/Transactions', [
            'transactions' => $this->reportService->transactionHistory(request()->only(['from', 'to', 'member_id', 'type'])),
            'members' => $this->familyMemberService->getActiveMembers(),
        ]);
    }

    public function exportTransactions()
    {
        return (new TransactionsExport(request()->only(['from', 'to', 'member_id', 'type'])))->download('laporan-transaksi.xlsx');
    }

    public function prices()
    {
        return Inertia::render('Reports/Prices', [
            'prices' => $this->reportService->goldPriceHistory(request()->only(['type', 'from', 'to'])),
        ]);
    }

    public function profit()
    {
        return Inertia::render('Reports/Profit', [
            'data' => $this->reportService->profitReport(),
        ]);
    }

    public function exportProfit()
    {
        return (new ProfitExport)->download('laporan-laba-rugi.xlsx');
    }
}
