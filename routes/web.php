<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionImportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MappingProfileController;

Route::get('/', function () {
    return view('welcome');
});

// API routes
Route::group(['prefix' => 'api'], function () {
    Route::get('/transactions/{transactionId}', [TransactionController::class, 'apiShow'])->name('api.transactions.show');
    Route::post('/transactions/{transaction}/comment', [TransactionController::class, 'updateComment']);
});

Route::group(['prefix' => 'transactions'], function () {
    Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/mass-update', [TransactionController::class, 'massUpdate'])->name('transactions.mass-update');

    Route::get('/import', [TransactionImportController::class, 'index'])->name('transactions.import');
    Route::post('/import', [TransactionImportController::class, 'import'])->name('transactions.import.store');

    Route::get('/monthly-summary', [TransactionController::class, 'monthlySummary'])->name('transactions.monthly-summary');
    Route::patch('/{transaction}/category', [TransactionController::class, 'updateCategory'])->name('transactions.update.category');

    Route::get('/recategorize', [TransactionController::class, 'recategorize'])->name('transactions.recategorize');

    Route::get('/counterparty', [TransactionController::class, 'counterparty'])->name('transactions.counterparty');

    Route::get('/year-summary', [TransactionController::class, 'yearSummary'])->name('transactions.year-summary');
});

// Category routes
Route::resource('categories', CategoryController::class);
Route::get('/categories/{category}/keywords', [CategoryController::class, 'editKeywords'])->name('categories.keywords');
Route::post('/categories/{category}/keywords', [CategoryController::class, 'updateKeywords'])->name('categories.keywords.update');

// Mapping Profile routes
Route::resource('mapping-profiles', MappingProfileController::class);
