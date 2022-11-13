<?php

use Illuminate\Support\Facades\Route;
use Jonreyg\LaravelRedisManager\Http\Controllers\RedisController;

Route::get('dashboard', [RedisController::class , 'dashboard'])->name('redis.dashboard');