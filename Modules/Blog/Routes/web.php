<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Blog\Http\Controllers\BlogCategoryController;
use Modules\Blog\Http\Controllers\BlogController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['web', 'auth', 'locale', 'permission'],
], function () {
    // Category
    Route::get('blog/category/list', [BlogCategoryController::class, 'index'])->name('blog.category.index');
    Route::post('blog/category/store', [BlogCategoryController::class, 'store'])->name('blog.category.store');
    Route::post('blog/category/update', [BlogCategoryController::class, 'update'])->name('blog.category.update');
    Route::post('blog/category/delete/{id}', [BlogCategoryController::class, 'delete'])
        ->middleware(['checkForDemoMode'])
        ->name('blog.category.delete');
    // Blog
    Route::get('blogs', [BlogController::class, 'index'])->name('blog.index');
    Route::get('blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('blog/store', [BlogController::class, 'store'])->name('blog.store');
    Route::get('blog/edit/{id}', [BlogController::class, 'edit'])->name('blog.edit');
    Route::post('blog/update/{id}', [BlogController::class, 'update'])->name('blog.update');
    Route::post('blog/delete/{id}', [BlogController::class, 'delete'])
        ->middleware(['checkForDemoMode'])
        ->name('blog.delete');
});
