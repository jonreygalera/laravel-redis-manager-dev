<?php

namespace Jonreyg\LaravelRedisManager\Http\Controllers;

use Illuminate\Support\Facades\Redis;

class RedisController extends Controller 
{
    public function ping()
    {
        try{
            $redisConnection = Redis::connection('default');
            dd('yes');
        }catch(\Throwable $e){
            return response('error connection redis');
        }
    }

    public function dashboard()
    {
        return view('redis-manager::dashboard.index');
    }
}