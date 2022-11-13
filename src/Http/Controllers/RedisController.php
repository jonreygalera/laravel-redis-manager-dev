<?php

namespace Jonreyg\LaravelRedisManager\Http\Controllers;

class RedisController extends Controller 
{
    public function ping()
    {
        return response()->json(['PONG'], 200);
    }

    public function dashboard()
    {
        return view('laravel-redis-manager::dashboard.index');
    }
}