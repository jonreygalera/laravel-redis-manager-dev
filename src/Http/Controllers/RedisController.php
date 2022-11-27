<?php

namespace Jonreyg\LaravelRedisManager\Http\Controllers;

use Jonreyg\LaravelRedisManager\Redis;
use Jonreyg\LaravelRedisManager\Bridge\RedisFolder;
use Throwable;
use Exception;

class RedisController extends Controller 
{
    public function ping()
    {
        try{
            Redis::checkRedisConnection();
            if (!Redis::isUp()) throw new Exception("No Redis Connnection.");
            return response()->json(["message" => "PONG"], 200);
        }catch(Throwable $e){
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function dashboard()
    {
        return view('redis-manager::dashboard.index');
    }

    public function allFolder()
    {
        try{
            Redis::checkRedisConnection();
            if (!Redis::isUp()) throw new Exception("No Redis Connnection.");
            $data = RedisFolder::all();
            return response()->json([
                "message" => "ok",
                "data" => $data
            ], 200);
        }catch(Throwable $e){
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}