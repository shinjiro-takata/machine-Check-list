<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ScheduledInspectionController;

/*
|--------------------------------------------------------------------------
| Webルート
|--------------------------------------------------------------------------
|
| アプリケーションのWebルートをここに定義します。
| RouteServiceProviderにより、webミドルウェアグループ内で読み込まれます。
|
*/

// 各点検へ移動するためのホーム画面。
Route::get('/', [HomeController::class, 'index'])->name('home');

// 週次・月次・定期点検の入力、保存、履歴、CSV出力、詳細、編集。点検種別はtypeで切り替える。
foreach (['weekly', 'monthly', 'periodic'] as $type) {
    Route::get("/{$type}", [ScheduledInspectionController::class, 'create'])->defaults('type', $type)->name("inspections.{$type}.create");
    Route::post("/{$type}/inspections", [ScheduledInspectionController::class, 'store'])->defaults('type', $type)->name("inspections.{$type}.store");
    Route::get("/{$type}/inspections", [ScheduledInspectionController::class, 'index'])->defaults('type', $type)->name("inspections.{$type}.index");
    Route::get("/{$type}/inspections/export", [ScheduledInspectionController::class, 'export'])->defaults('type', $type)->name("inspections.{$type}.export");
    Route::get("/{$type}/inspections/{inspection}", [ScheduledInspectionController::class, 'show'])->defaults('type', $type)->name("inspections.{$type}.show");
    Route::get("/{$type}/inspections/{inspection}/edit", [ScheduledInspectionController::class, 'edit'])->defaults('type', $type)->name("inspections.{$type}.edit");
    Route::put("/{$type}/inspections/{inspection}", [ScheduledInspectionController::class, 'update'])->defaults('type', $type)->name("inspections.{$type}.update");
}

// 日常点検の入力画面と新しい点検記録の登録。
Route::get('/daily', [InspectionController::class, 'create'])->name('inspections.create');
Route::post('/inspections', [InspectionController::class, 'store'])->name('inspections.store');

// 閲覧専用の点検履歴、CSV出力、記録詳細。
Route::get('/inspections', [InspectionController::class, 'history'])->name('inspections.index');
Route::get('/inspections/export', [InspectionController::class, 'export'])->name('inspections.export');
Route::get('/inspections/{inspection}', [InspectionController::class, 'show'])->name('inspections.show');

// 編集画面を経由した場合に限り、点検記録の変更を許可する。
Route::get('/inspections/{inspection}/edit', [InspectionController::class, 'edit'])->name('inspections.edit');
Route::put('/inspections/{inspection}', [InspectionController::class, 'update'])->name('inspections.update');
