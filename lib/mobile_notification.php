<?php
/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 8. 10.
 * Time: 오후 4:20
 */
define("GOOGLE_API_KEY",'AIzaSyDDmEVFWgAZNNwrLlAS6B9Pv4xQAw0buQo');

function send_notification (PDO $db,array $ids,string $message,$title=null)
{
    $tokens=array();
    $ids_string=implode('\',\'',$ids);
    $sql= "SELECT DEVICE_TOKEN FROM publixher.TBL_DEVICES WHERE USER_ID IN ('$ids_string')";
    $prepare=$db->prepare($sql);
    $prepare->execute();
    $fetched_tokens=$prepare->fetchALL(PDO::FETCH_ASSOC);
    foreach($fetched_tokens as $fetched_token){
        $tokens[]=$fetched_token['DEVICE_TOKEN'];
    }
//    $url="https://fcm.googleapis.com/fcm/send"; fcm용
    $url = 'https://android.googleapis.com/gcm/send';
    $fields = array(
        'registration_ids' => $tokens,
        'data' => array("message"=>$message),
        'content_available'=>true
    );
    if($title!==null){
        $fields['data']['title']=$title;
    }

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