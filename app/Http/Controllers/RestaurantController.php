<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    /**
     * Search restaurant by using Google Place API
     */
    public function search(Request $request) {

        # Validate keyword input
        $validator = Validator::make($request->all(), [
            'keyword' => 'required|max:255',
        ]);
        if($validator->errors()->count()){
            return response([
                'status'=> 400, 
                'errorMsg'=>"Keyword is required", 
                'errors'=>$validator->errors()
            ], 400);
        }

        # Generate Cache key by convert keyword to slug format to avoid key broken by special characters
        $cacheKey = Str::slug($request->keyword);

        # If cache is exist for this key then return cached result.
        if (Cache::has($cacheKey)) {
            return [
                'status'=> 200,
                'source'=>"cached",
                'data'=> Cache::get($cacheKey)
            ];
        }

        try {
            # Set up Google API URL
            $apiURL = "https://maps.googleapis.com/maps/api/place/textsearch/json";
            $apiURL .= "?language=en";
            $apiURL .= "&key=". env('GOOGLE_MAP_API_KEY');
            $apiURL .= "&query=". $request->keyword;
            $apiURL .= "&type=restaurant";
            
            # Start call Google API
            $response = Http::get($apiURL);
            
            # Return expected result if request is successfully
            if ($response->successful()) {
                $data = json_decode($response->body(), true);

                if (isset($data['status']) && $data['status'] == "OK") {
                    # Store API response in Laravel Cache
                    Cache::put($cacheKey, $data, env('CACHE_EXPIRED', now()->addMinutes(10)));
                    return [
                        'status'=> 200,
                        'source'=>"live",
                        'data'=>$data
                    ];
                }
            }

            # If something went wrong return error as standard format
            return [
                'status' => 500,
                'errorMsg'=>"Oop! Something went wrong", 
                'errors' => $e->getMessage(),
            ];
        } catch (ConnectionException $e) {
            # If something went wrong return error as standard format
            return [
                'status' => 500,
                'errorMsg'=>"Oop! Something went wrong", 
                'errors' => $e->getMessage(),
            ];
        }
    }
}
