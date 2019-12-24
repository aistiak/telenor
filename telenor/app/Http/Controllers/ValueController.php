<?php

namespace App\Http\Controllers;

use Cache;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    //
    public $TTL = 300;
    public function index(Request $request)
    {

        $res = [];

        if ($request->has('keys')) { // for some values
            $key_arr = explode(",", $request->input('keys'));
            // check for `keys`
            if (in_array("keys", $key_arr)) {
                $idx = array_search("keys", $key_arr);
                $key_arr[$idx] = "keys%25";
            }

            $cache_keys = Cache::get('keys'); // todo could be empty so check
            if ($cache_keys != false) {
                foreach ($key_arr as $key) {
                    // if exist
                    if (in_array($key, $cache_keys)) {
                        $val = Cache::get($key);
                        if ($val != null) {
                            $res[$key] = $val;
                            // reset TTL
                            Cache::set($key, $val, $this->TTL); // reset TTL on every get request 
                        }
                    }
                }
            }
            if(count($res) == 0){
                return response()->json(['error'=>'The requested resource was not found'],404);
            }

        } else { // for all

            $key_arr = Cache::get('keys');
            // return $key_arr;
            if ($key_arr != false) {
                foreach ($key_arr as $key) {
                    // if exist
                    if (Cache::has($key)) {
                        $val = Cache::get($key);
                        $res[$key] = $val;
                        Cache::set($key, $val, $this->TTL); // reset TTL on every get request 
                    }
                }
            }

        }
        // check for `keys_`
        if (array_key_exists("keys%25", $res)) {
            $res["keys"] = $res["keys%25"];
            unset($res["keys%25"]);
        }

        // return $res;
        return response()->json($res, 200);

    }



    public function store(Request $request)
    {

        $key_arr = [];

        // for key named as keys
        //
        if ($request->has("keys")) {
            $request["keys%25"] = $request["keys"];
            unset($request["keys"]);
            // return $request["keys_"];
        }

        foreach ($request->all() as $key => $val) {
            array_push($key_arr, $key);
            //todo
            // the key it self could be named as keys
            Cache::set($key, $val, $this->TTL); // del in 5 min / 300sec
        }

        if (Cache::has('keys')) {

            $temp_keys = Cache::get('keys');
            $temp_keys = array_unique(array_merge($temp_keys, $key_arr));
            Cache::set('keys', $temp_keys);

        } else {

            Cache::set('keys', $key_arr);

        }

        if($request->method() == "POST"){
            return response()->json(['success'=>'value stored'],201);
        }else if($request->method() == "PATCH"){
            return response()->json(['success'=>'value updated'],200);
        }
        // return response()->json(['success' => Cache::get('keys')], 201);

    }

    public function others(Request $request)
    {
        return response()->json(['error' => 'method not allowed'],405);
    }

    public function others2(Request $request)
    {
        return $request->sth;
        return response()->json(['error' => 'method not allowed'],405);
    }
    
}
