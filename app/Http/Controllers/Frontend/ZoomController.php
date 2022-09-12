<?php

namespace App\Http\Controllers\Frontend;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use App\Diklat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\DaerahKbupaten;
use App\DaerahProvinsi;
use App\User;
use App\ZoomAccount;

class ZoomController extends Controller
{
  

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

    public function createZoomRoom($topic,$password)
    {
        
        $body = [
            "topic" => $topic,
            "type" =>  3, // 6 for webinar, 3 form meeting
            "timezone" => "Asia/Jakarta",
            "password" => $password,
            "agenda" => $topic,
            "settings" => [
                "host_video" => true,
                "panelists_video" => false,
                "hd_video" => false,
                "audio" => "both",
                "auto_recording" => "local",
                "close_registration" => true,
                "show_share_button" => true,
                "allow_multiple_devices" => false
            ],
        ];
        return $this->createZoom($body, 'meeting', 3);
    }









}