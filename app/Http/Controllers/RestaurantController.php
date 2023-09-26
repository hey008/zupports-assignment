<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class RestaurantController extends Controller
{
    public function search(Request $request) {

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

        try {
            $apiURL = "https://maps.googleapis.com/maps/api/place/textsearch/json";
            $apiURL .= "?language=en";
            $apiURL .= "&key=". env('GOOGLE_MAP_API_KEY');
            $apiURL .= "&query=". $request->keyword;
            $apiURL .= "&type=restaurant";
            //$apiURL .= "&location=7.9099044%2C98.3876533";
            //$apiURL .= "&radius=5000";
            
            $response = Http::get($apiURL);
            
            if ($response->successful()) {
                $data = json_decode($response->body(), true);

                if (isset($data['status']) && $data['status'] == "OK") {
                    return [
                        'status'=> 200,
                        'data'=>$data
                    ];
                }
            }

            return [
                'status' => 500,
                'errorMsg'=>"Keyword is required", 
                'errors' => $e->getMessage(),
            ];
        } catch (ConnectionException $e) {
            $LOG->response = "ERROR";
            $LOG->errors = $e->getMessage();
            $LOG->save();

            return [
                'status' => 500,
                'errorMsg'=>"Keyword is required", 
                'errors' => $e->getMessage(),
            ];
        }
    }
}
