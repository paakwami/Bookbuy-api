<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class SmsController extends Controller
{
    private $SMS_SENDER = "Sample";
    private $RESPONSE_TYPE = 'json';
    private $SMS_USERNAME = 'paakwami019';
    private $SMS_PASSWORD = 'Your password';



    public function sendSms($sender, $receiver, $message){
    $url = "https://api.1s2u.io/bulksms?";
        $client = new Client();

        $response = $client->post($url.
            'username='.$this->SMS_USERNAME.
            '&password='.$this->SMS_PASSWORD.
            '&mt=0&fl=0&SID='.$sender.
            '&MNO='.$receiver.
            '&MSG='.$message);

        return $response = json_decode($response->getBody(), true);
    }
}
