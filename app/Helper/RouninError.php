<?php

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use TCG\Voyager\Models\Category;
use App\Slider;
use App\Menu;
use App\ZoomAccount;
use Illuminate\Support\Facades\Log;

if (!function_exists('integerToRoman')) {
    function integerToRoman($integer)
    {
        // Convert the integer into an integer (just to make sure)
        $integer = intval($integer);
        $result = '';

        // Create a lookup array that contains all of the Roman numerals.
        $lookup = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        );

        foreach($lookup as $roman => $value){
            // Determine the number of matches
            $matches = intval($integer/$value);

            // Add the same number of characters to the string
            $result .= str_repeat($roman,$matches);

            // Set the integer to be the remainder of the integer and the value
            $integer = $integer % $value;
        }

        // The Roman numeral should be built, return it
        return $result;
    }
}

if (!function_exists('getWistyaHashedId')) {
    function getWistyaHashedId($title)
    {
        $http = new Client;

        $response = $http->request('POST', 'https://api.wistia.com/v1/projects/0q0jdg5e3y.json', [
            'debug' => true,
            'form_params' => [
                'name' => $title,
                'adminEmail' => 'babastudio.projects@gmail.com',
                'api_password' => config('services.wistia.token')
            ]
        ]);

        $wistia_project = json_decode($response->getBody(), true);
        return $wistia_project['hashedId'];
    }
}

if (!function_exists('delWistyaHashedId')) {
    function delWistyaHashedId($id)
    {
        try {
            $http = new Client;
            $response = $http->request('DELETE', "https://api.wistia.com/v1/projects/{$id}.json", [
                'debug' => false,
                'form_params' => [
                    'adminEmail' => 'babastudio.projects@gmail.com',
                    'api_password' => config('services.wistia.token')
                ]
            ]);

            $res = json_decode($response->getStatusCode(), true);
            return $res;
        } catch (\Throwable $th) {
            // Log::alert($th);
            // return json_decode(200, true);
            return 'ok';
        }
    }
}

if (!function_exists('createZoom')) {
    function createZoom(array $body, $type, int $zoomAcountId)
    {
        $zoom = ZoomAccount::findOrFail($zoomAcountId);
        $token = $zoom->jwt_token;
        $user = $zoom->email;
        $http = new Client();
        $response = $http->request('POST', "https://api.zoom.us/v2/users/{$user}/{$type}s", [
            'debug' => false,
            RequestOptions::JSON => $body,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ],
        ]);
        $resCode = json_decode($response->getStatusCode(), true);
        $resBody = json_decode($response->getBody(), true);
        $res = [
            'status' => $resCode,
            'detail' => $resBody
        ];

        return $res;
    }
}

if (!function_exists('deleteZoom')) {
    function deleteZoom(string $zoom_id, string $type, $zoomAcountId)
    {
        $zoom = ZoomAccount::findOrFail($zoomAcountId);
        $token = $zoom->jwt_token;
        $http = new Client();
        $response = $http->request('DELETE', "https://api.zoom.us/v2/{$type}s/{$zoom_id}", [
            'debug' => true,
            'headers' => [
                'Authorization' => "Bearer {$token}",
            ],
        ]);
        $resCode = json_decode($response->getStatusCode(), true);
        return $resCode;
    }
}

if (!function_exists('getCategory')) {
    function getCategory()
    {
        return Category::orderBy('order')->get();
    }
}

if (!function_exists('getSlider')) {
    function getSlider()
    {
        return Slider::orderByDesc('id')->get();
    }
}

if (!function_exists('menuItem')) {
    function menuItem($slug)
    {
        return Menu::with('getItem')->whereName("{$slug}")->first();
    }
}

if (!function_exists('menuItem')) {
    function getSlider()
    {
        return Slider::orderByget();
    }
}

if (!function_exists('jsonFile')) {
    function jsonFile($data)
    {
        $a = json_decode($data)[0]->download_link;
        return str_replace('\\', '/', $a);
    }
}

if (!function_exists('exploded')) {
    function exploded($data)
    {
        $arr = explode(';', $data);
        $arr_fill = array_filter($arr);
        return end($arr_fill);
    }
}

if (!function_exists('imagetobase')) {
    function imagetobase(string $path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}

if (!function_exists('unique_multidim_array')) {
    function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();
        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    } 
}

if (!function_exists('remove_banksoal_unused')) {
    function remove_banksoal_unused($collection, array $ruleBankSoal, int $mataDiklatId) {
        foreach ($collection as $key => $value) {
            if (!in_array($key, $ruleBankSoal)) {
                $collection->forget($key);
            }
            if ($key !== 'pretest_postest') {
                foreach ($value as $k => $val) {
                    if ($val->mata_diklat_id !== $mataDiklatId) {
                        if (isset($collection[$key])) unset($collection[$key][$k]);
                    }
                }
            }
        }
        return $collection;
    } 
}