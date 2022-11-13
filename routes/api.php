<?php

use Illuminate\Support\Facades\Route;
use Jonreyg\LaravelRedisManager\Http\Controllers\RedisController;

Route::get('ping', [RedisController::class , 'ping'])->name('redis.ping');