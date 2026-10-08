<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InspectionController;

/*
|--------------------------------------------------------------------------
| Webルート
|--------------------------------------------------------------------------
|
| アプリケーションのWebルートをここに定義します。
| RouteServiceProviderにより、webミドルウェアグループ内で読み込まれます。
|
*/

// 点検入力画面と新しい点検記録の登録。
Route::get('/', [InspectionController::class, 'create'])->name('inspections.create');
Route::post('/inspections', [InspectionController::class, 'store'])->name('inspections.store');

// 閲覧専用の点検履歴、CSV出力、記録詳細。
Route::get('/inspections', [InspectionController::class, 'history'])->name('inspections.index');
Route::get('/inspections/export', [InspectionController::class, 'export'])->name('inspections.export');
Route::get('/inspections/{inspection}', [InspectionController::class, 'show'])->name('inspections.show');

// 編集画面を経由した場合に限り、点検記録の変更を許可する。
Route::get('/inspections/{inspection}/edit', [InspectionController::class, 'edit'])->name('inspections.edit');
Route::put('/inspections/{inspection}', [InspectionController::class, 'update'])->name('inspections.update');
