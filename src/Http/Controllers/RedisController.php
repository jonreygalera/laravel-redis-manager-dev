<?php

namespace Jonreyg\LaravelRedisManager\Http\Controllers;

use Jonreyg\LaravelRedisManager\Redis;
use Jonreyg\LaravelRedisManager\Bridge\RedisFolder;
use Jonreyg\LaravelRedisManager\Helpers\ApiHelper;
use Illuminate\Http\Request;
use Throwable;
use Exception;

class RedisController extends Controller 
{
    public function dashboard()
    {
        return view('redis-manager::dashboard');
    }

    public function ping()
    {
        try{
            Redis::checkRedisConnection();
            if (!Redis::isUp()) throw new Exception("No Redis Connnection.");
            return ApiHelper::ping();
        }catch(Throwable $e){
            return ApiHelper::responseError($e->getMessage());
        }
    }

    public function allFolder()
    {
        try{
            Redis::checkRedisConnection();
            if (!Redis::isUp()) throw new Exception("No Redis Connnection.");
            $data = RedisFolder::all();
            return ApiHelper::responseData($data, 'OK');
        }catch(Throwable $e){
            return ApiHelper::responseError($e->getMessage());
        }
    }

    public function folderColumn(Request $request, $folder_name)
    {
        try{
            Redis::checkRedisConnection();
            if (!Redis::isUp()) throw new Exception("No Redis Connnection.");

            $data = RedisFolder::getFolder($folder_name);

            $class = $data[1] ?? null;
            if (is_null($class)) throw new Exception("Folder data is empty.");
            $object = new $class;
            return ApiHelper::responseData($object->getColumnList() ?? [], TRUE);
        }catch(Throwable $e){
            return ApiHelper::responseError($e->getMessage());
        }
    }

    public function folderData(Request $request, $folder_name)
    {
        try{
            Redis::checkRedisConnection();
            if (!Redis::isUp()) throw new Exception("No Redis Connnection.");
            $data = RedisFolder::getFolder($folder_name);

            $class = $data[1] ?? null;
            if (is_null($class)) throw new Exception("Folder data is empty.");
            $object = new $class;
            $data = $object->all();
            return ApiHelper::responseData($data ?? [], TRUE);
        }catch(Throwable $e){
            return ApiHelper::responseError($e->getMessage());
        }
    }

    public function flushFolder(Request $request, $folder_name)
    {
        try{
            Redis::checkRedisConnection();
            if (!Redis::isUp()) throw new Exception("No Redis Connnection.");
            RedisFolder::flushFolder($request->folder_name);
            return ApiHelper::response([], 204);
        }catch(Throwable $e){
            return ApiHelper::responseError($e->getMessage());
        }
    }

    public function flushAll(Request $request)
    {
        try{
            Redis::checkRedisConnection();
            if (!Redis::isUp()) throw new Exception("No Redis Connnection.");
            RedisFolder::flushDB();
            return ApiHelper::response([], 204);
        }catch(Throwable $e){
            return ApiHelper::responseError($e->getMessage());
        }
    }
}