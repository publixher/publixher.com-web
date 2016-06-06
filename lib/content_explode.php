<?php
function content_explode($body_text,$uid)
{
    $manager=new MongoDB\Driver\Manager("mongodb://DongGyun:Sk763118!@localhost:27017/publixher");
    $words = preg_split("/[^a-zA-Z0-9_ㄱ-ㅎㅏ-ㅣ가-힣]/", $body_text);
    foreach($words as $key=>$word ){
        if(mb_strlen($word,"UTF-8")<2) unset($words[$key]);
    }
    $words = array_count_values($words);
    arsort($words);
    $pieces=array_slice($words,0,4);

    $bulk=new MongoDB\Driver\BulkWrite;

    $document=['id'=>$uid,'pieces'=>$pieces];
    $bulk->insert($document);
    try {
        $manager->executeBulkWrite('publixher.contents', $bulk);
    }catch(MongoDB\Driver\Exception\BulkWriteException $e){
        $msg=$e->getWriteResult();
    }catch(MongoDB\Driver\Exception\Exception $e){
        $msg=$e->getMessage();
    }
}