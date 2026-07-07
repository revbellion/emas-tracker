<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\GoldTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('family-members')->group(function () {
        Route::get('/', [FamilyMemberController::class, 'index'])->name('family-members.index');
        Route::post('/', [FamilyMemberController::class, 'store'])->name('family-members.store');
        Route::get('/{familyMember}', [FamilyMemberController::class, 'show'])->name('family-members.show');
        Route::patch('/{familyMember}', [FamilyMemberController::class, 'update'])->name('family-members.update');
        Route::delete('/{familyMember}', [FamilyMemberController::class, 'deactivate'])->name('family-members.deactivate');
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [GoldTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/create', [GoldTransactionController::class, 'create'])->name('transactions.create');
        Route::post('/buy', [GoldTransactionController::class, 'buy'])->name('transactions.buy');
        Route::post('/sell', [GoldTransactionController::class, 'sell'])->name('transactions.sell');
        Route::post('/transfer', [GoldTransactionController::class, 'transfer'])->name('transactions.transfer');
        Route::post('/adjustment', [GoldTransactionController::class, 'adjustment'])->name('transactions.adjustment');
        Route::delete('/{goldTransaction}', [GoldTransactionController::class, 'void'])->name('transactions.void');
    });

    Route::prefix('reports')->group(function () {
        Route::get('/portfolio', [ReportController::class, 'portfolio'])->name('reports.portfolio');
        Route::get('/portfolio/export', [ReportController::class, 'exportPortfolio'])->name('reports.portfolio.export');
        Route::get('/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
        Route::get('/transactions/export', [ReportController::class, 'exportTransactions'])->name('reports.transactions.export');
        Route::get('/prices', [ReportController::class, 'prices'])->name('reports.prices');
        Route::get('/profit', [ReportController::class, 'profit'])->name('reports.profit');
        Route::get('/profit/export', [ReportController::class, 'exportProfit'])->name('reports.profit.export');
    });

    Route::prefix('activity-logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/sync-price/{provider}', [SettingController::class, 'syncPrice'])->name('settings.sync-price');
        Route::post('/manual-price', [SettingController::class, 'setManualPrice'])->name('settings.manual-price');
    });

    Route::prefix('backups')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/download', [BackupController::class, 'download'])->name('backups.download');
        Route::post('/restore', [BackupController::class, 'restore'])->name('backups.restore');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
