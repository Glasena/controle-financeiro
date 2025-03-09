<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CreditCardTransactionController;

Route::get('/', function () {
    return view('import.index');
})->name('import.index');

// Rotas para importação de transações bancárias (normal)
Route::get('/import/transactions', [TransactionController::class, 'importForm'])->name('import.transactions.form');
Route::post('/import/transactions', [TransactionController::class, 'import'])->name('transactions.import');
Route::get('/transactions/exist', [TransactionController::class, 'checkExistence'])->name('transactions.exist');

// Rotas para importação de transações de cartão de crédito
Route::get('/import/credit-card', [CreditCardTransactionController::class, 'importForm'])->name('import.credit_card.form');
Route::post('/import/credit-card', [CreditCardTransactionController::class, 'import'])->name('import.credit_card');
Route::get('/credit-card-transactions/exist', [CreditCardTransactionController::class, 'checkExistence'])->name('credit_card_transactions.exist');
