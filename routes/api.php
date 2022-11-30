<?php

use Illuminate\Support\Facades\Route;
use Jonreyg\LaravelRedisManager\Http\Controllers\RedisController;

Route::get('ping', [RedisController::class , 'ping'])->name('redis.ping');
Route::get('all-folder', [RedisController::class , 'allFolder'])->name('redis.allFolder');
Route::get('folder-column/{folder_name}', [RedisController::class , 'folderColumn'])->name('redis.folderColumn');
Route::get('folder-data/{folder_name}', [RedisController::class , 'folderData'])->name('redis.folderData');
Route::get('folder-data-list/{folder_name}', [RedisController::class , 'folderDataList'])->name('redis.folderDataList');
Route::delete('flush-folder/{folder_name}', [RedisController::class , 'flushFolder'])->name('redis.flushFolder');
Route::delete('flush-all', [RedisController::class , 'flushAll'])->name('redis.flushAll');