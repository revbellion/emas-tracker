<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoldTransactionRequest;
use App\Models\GoldTransaction;
use App\Services\FamilyMemberService;
use App\Services\GoldPriceService;
use App\Services\GoldTransactionService;
use Inertia\Inertia;

class GoldTransactionController extends Controller
{
    public function __construct(
        private GoldTransactionService $goldTransactionService,
        private FamilyMemberService $familyMemberService,
        private GoldPriceService $goldPriceService,
    ) {}

    public function index()
    {
        return Inertia::render('Transactions/Index', [
            'transactions' => $this->goldTransactionService->getAll(request()->only(['member_id', 'type', 'from', 'to', 'search'])),
            'members' => $this->familyMemberService->getActiveMembers(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Transactions/Create', [
            'members' => $this->familyMemberService->getActiveMembers(),
            'latestBuyPrice' => $this->goldPriceService->getLatestBuyPrice()?->price ?? 0,
            'latestSellPrice' => $this->goldPriceService->getLatestSellPrice()?->price ?? 0,
        ]);
    }

    public function buy(StoreGoldTransactionRequest $request)
    {
        $this->goldTransactionService->buy($request->validated());
        return redirect()->route('transactions.index')->with('success', 'Transaksi pembelian berhasil disimpan');
    }

    public function sell(StoreGoldTransactionRequest $request)
    {
        try {
            $this->goldTransactionService->sell($request->validated());
            return redirect()->route('transactions.index')->with('success', 'Transaksi penjualan berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function transfer(StoreGoldTransactionRequest $request)
    {
        try {
            $this->goldTransactionService->transfer($request->validated());
            return redirect()->route('transactions.index')->with('success', 'Transaksi transfer berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function adjustment(StoreGoldTransactionRequest $request)
    {
        $this->goldTransactionService->adjustment($request->validated());
        return redirect()->route('transactions.index')->with('success', 'Transaksi penyesuaian berhasil disimpan');
    }

    public function void(GoldTransaction $goldTransaction)
    {
        $this->goldTransactionService->void($goldTransaction);
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dibatalkan');
    }
}
