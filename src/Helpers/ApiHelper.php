<?php

namespace Jonreyg\LaravelRedisManager\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class ApiHelper extends Response {

    const UNAUTHORIZED = [ 'message' => 'Unauthorized: Intruder alert!!! 🚨', 'code' => 401 ];
    const NOT_FOUND = [ 'message' => 'Not Found: Ooopsy! There is nothing in here. 🙄', 'code' => 404 ];
    const UNPROCESSABLE_ENTITY = [ 'message' => 'Cannot process your request.', 'code' => 422 ];
    const NO_RECORD = [ 'message' => 'Empty record.', 'code' => 422 ];

    public static function unauthorized()
    {
        return self::responseData(self::UNAUTHORIZED, TRUE, Response::HTTP_UNAUTHORIZED);
    }
    
    public static function ping()
    {
        return self::responseData(['message' => 'PONG'], TRUE, Response::HTTP_OK);
    }

    public static function responseError($message_data, $to_array = false)
    {
        $parse = self::isJson($message_data) ? json_decode($message_data, true) : [ 'message' => $message_data, 'code' => 500 ];
        $parse['error'] = true;
        return self::response($parse, $parse['code'] ?? Response::HTTP_INTERNAL_SERVER_ERROR, $to_array);
    }

    public static function isJson(string $string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function responseMessage($message, int $code = Response::HTTP_OK, $to_array = true)
    {   
        $data = ($to_array) ? [ 'message' => $message ] : $message;
        return self::response($data, $code, $to_array);
    }

    public static function responseData($data, $message = '', int $code = Response::HTTP_OK)
    {   
        $to_array = (is_bool($message) && $message === TRUE) ? FALSE : TRUE;
        
        if ($to_array) {
            $result['data'] = $data;
            $result['message'] = $message;
        } else {
            $result = $data;
        }
        return self::response($result, $code, $to_array);
    }

    public static function responseSuccess(string $message = 'Success', int $code = Response::HTTP_OK, $to_array = TRUE)
    {
        return static::responseMessage($to_array ? [ 'message' => $message ] : $message, $code, FALSE);
    }

    public static function response($data, int $code = Response::HTTP_OK, $to_array = true)
    {
        if ($to_array) {
            $result = [
                'message' => '',
                'data' => null,
                'error' => true,
                'code' => $code
            ];

            if (is_array($data)) {
                if (array_key_exists('message', $data)) $result['message'] = $data['message'];
                unset($data['message']);
            }

            $result['data'] = $data;

            if (is_array($data)) {
                if (array_key_exists('data', $data)) $result['data'] = $data['data'];
                unset($data['data']);
            }


    
            if ($code >= Response::HTTP_OK && $code <= 299) $result['error'] = false;
        } else $result = $data;

        
        return response()->json($result, $code);
    }

    public static function onQueued()
    {
        return static::responseSuccess('Queued.');
    }

    /**
     * Wrap it to try-catch
     */
    public static function validateRequest(array $data, array $rules, array $rename = [])
    {
        $result = [];
        $validator = Validator::make($data, $rules, $rename);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                $result = [
                    'field_error' => true,
                    'message' => $message,
                    'code' => Response::HTTP_BAD_REQUEST
                ];

                throw new Exception(json_encode($result));
            }
        }
        return true;
    }
}
