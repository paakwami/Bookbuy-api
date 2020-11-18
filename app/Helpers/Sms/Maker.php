<?php


namespace App\Helpers\Sms;
use Ixudra\Curl\Facades\Curl;

class Maker
{
    public static function sendSms($sender, $to, $message){
        $username = 'paakwami019';
        $password = 'web90816';
        $url = "https://sms.arkesel.com/sms/api?action=send-sms&api_key=OlBybVM0N3RFRzNJOGw2Tnk=&to=";
        $ch = curl_init();

        $ptn = "/^0/";  // Regex
        $rpltxt = "+233";  // Replacement string
        $to = preg_replace($ptn, $rpltxt, $to);

        $res =$url.
            $to.
            '&from='.$sender.
            '&sms='.curl_escape($ch, $message);


        $response = Curl::to($res)->get();
        return $response;
    }
}
