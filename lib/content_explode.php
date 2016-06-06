<?php
function content_explode($body_text, $uid,$tags)
{
    $blacklist=['http','php','id','page','no','html','jsp','htm','www','https','com','net','co','kr','jp','en',' http',' https'];
    $manager = new MongoDB\Driver\Manager("mongodb://DongGyun:Pp999223#@localhost:27017/publixher");
    $body_text=html_entity_decode($body_text);
    $words = preg_split("/[^a-zA-Z0-9_ㄱ-ㅎㅏ-ㅣ가-힣]/", $body_text);
    foreach ($words as $key => $word) {
        if (mb_strlen($word, "UTF-8") < 2) {
            unset($words[$key]);
            continue;
        }
        if(in_array($word,$blacklist)){
            unset($words[$key]);
            continue;
        }
    }
    $words = array_count_values($words);
    arsort($words);
    $pieces = array_slice($words, 0, 4);

    $bulk = new MongoDB\Driver\BulkWrite;


    $document = ['id' => $uid, 'pieces' => $pieces,'updateTime'=>date('Y-m-d H:i:s')];
    if($tags){
        $tags = json_decode($tags);
        $document['tags']=$tags;
    }
//    $query=['$or'=>[]];
//    $query=new MongoDB\Driver\Query(,['id'=>1]);
    $bulk->insert($document);
    $manager->executeBulkWrite('publixher.contents', $bulk);
}