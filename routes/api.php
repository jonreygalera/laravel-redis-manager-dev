<?php

use Illuminate\Support\Facades\Route;
use Jonreyg\LaravelRedisManager\Http\Controllers\RedisController;

Route::get('ping', [RedisController::class , 'ping'])->name('redis.ping');
Route::get('all-folder', [RedisController::class , 'allFolder'])->name('redis.allFolder');