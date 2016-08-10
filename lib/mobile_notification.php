<?php
/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 8. 10.
 * Time: 오후 4:20
 */
define("GOOGLE_API_KEY",'AIzaSyDDmEVFWgAZNNwrLlAS6B9Pv4xQAw0buQo');

function send_notification (array $tokens,string $message,string $title)
{
    $url = 'https://android.googleapis.com/gcm/send';
    $fields = array(
        'registration_ids' => $tokens,
        'data' => array("message"=>$message,"title"=>$title),
        'content_available'=>1
    );

    $headers = array(
        'Authorization:key =' . GOOGLE_API_KEY,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
}