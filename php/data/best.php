<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$action=$_GET['act'];
if($action=='now') {
    if (isset($_GET['category'])) {
        if (isset($_GET['sub_category'])) {
            $sql = "SELECT 
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'profile', 'crop50') AS WRITER_PIC
FROM publixher.TBL_NOW_HOT AS NOW
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=NOW.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
  AND CONT.SUB_CATEGORY=:SUB_CATEGORY
ORDER BY SEQ DESC
LIMIT 5";
        }else 
        $sql = "SELECT 
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'profile', 'crop50') AS WRITER_PIC
FROM publixher.TBL_NOW_HOT AS NOW
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=NOW.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
ORDER BY SEQ DESC
LIMIT 5";
    }else $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_NOW_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}elseif($action=='daily'){
    if (isset($_GET['category'])) {
        if (isset($_GET['sub_category'])) {
            $sql = "SELECT 
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'profile', 'crop50') AS WRITER_PIC
FROM publixher.TBL_DAILY_HOT AS DAILY
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=DAILY.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
  AND CONT.SUB_CATEGORY=:SUB_CATEGORY
ORDER BY SEQ DESC
LIMIT 5";
        }else 
        $sql = "SELECT 
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'profile', 'crop50') AS WRITER_PIC
FROM publixher.TBL_DAILY_HOT AS DAILY
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=DAILY.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
ORDER BY SEQ DESC
LIMIT 5";
    }else $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_DAILY_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}elseif($action=='weekly'){
    if (isset($_GET['category'])) {
        if (isset($_GET['sub_category'])) {
            $sql = "SELECT SQL_CACHE
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'profile', 'crop50') AS WRITER_PIC
FROM publixher.TBL_WEEKLY_HOT AS WEEK
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=WEEK.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
  AND CONT.SUB_CATEGORY=:SUB_CATEGORY
ORDER BY SEQ DESC
LIMIT 5";
        }else 
        $sql = "SELECT SQL_CACHE
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'profile', 'crop50') AS WRITER_PIC
FROM publixher.TBL_WEEKLY_HOT AS WEEK
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=WEEK.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
ORDER BY SEQ DESC
LIMIT 5";
    }else $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_WEEKLY_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>