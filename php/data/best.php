<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$action=$_GET['act'];
if($action=='now') {
    $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY FROM publixher.TBL_NOW_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}elseif($action=='daily'){
    $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY FROM publixher.TBL_DAILY_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}elseif($action=='weekly'){
    $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY FROM publixher.TBL_WEEKLY_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>