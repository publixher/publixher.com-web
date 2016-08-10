<?php
/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 8. 10.
 * Time: 오후 4:20
 */
define("GOOGLE_API_KEY",'AIzaSyDDmEVFWgAZNNwrLlAS6B9Pv4xQAw0buQo');

function send_notification (array $tokens,string $message)
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $fields = array(
        'registration_ids' => $tokens,
        'data' => array("message"=>$message)
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


//데이터베이스에 접속해서 토큰들을 가져와서 FCM에 발신요청
//include_once 'config.php';
//$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
//
//$sql = "Select Token From users";
//
//$result = mysqli_query($conn,$sql);
//$tokens = array();
//
//if(mysqli_num_rows($result) > 0 ){
//
//    while ($row = mysqli_fetch_assoc($result)) {
//        $tokens[] = $row["Token"];
//    }
//}
//
//mysqli_close($conn);
//
//if ($myMessage == ""){
//    $myMessage = "새글이 등록되었습니다.";
//}
//
//$message = array("message" => $myMessage);
//$message_status = send_notification($tokens, $message);
//echo $message_status;