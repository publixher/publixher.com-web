<?php
function content_explode($body_text, $uid, $tags)
{
    $blacklist = ['http', 'php', 'id', 'page', 'no', 'html', 'jsp', 'htm', 'www', 'https', 'com', 'net', 'co', 'kr', 'jp', 'en', ' http', ' https', '있', '한', '된', '되', '또', '없', '좋', '싫'];
    $listlen = count($blacklist);
    $manager = new MongoDB\Driver\Manager("mongodb://DongGyun:Pp999223#@localhost:27017/publixher");
    $body_text = html_entity_decode($body_text);
    $words = preg_split("/[^a-zA-Z0-9_ㄱ-ㅎㅏ-ㅣ가-힣]/", $body_text);
    foreach ($words as $key => $word) {
        if (mb_strlen($word, "UTF-8") < 2) {
            unset($words[$key]);
            continue;
        }
        for ($indi = 0; $indi < $listlen; $indi++) {
            if (mb_strpos($word, $blacklist[$indi], 0, "UTF-8") !== false) {
                unset($words[$key]);
                continue;
            }
        }
    }
    $words = array_count_values($words);
    arsort($words);
    $pieces = array_slice($words, 0, 20);

    $bulk = new MongoDB\Driver\BulkWrite;


    $document = ['id' => $uid, 'pieces' => $pieces, 'updateTime' => date('Y-m-d H:i:s')];
    if (isset($tags)) {
        $tags = json_decode($tags);
        $document['tags'] = $tags;
    }
    $bulk->insert($document);
    $manager->executeBulkWrite('publixher.contents', $bulk);
}