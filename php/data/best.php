<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$action = $_GET['act'];
if ($action == 'now') {
    $sql = "SELECT SQL_CACHE ID_CONTENT,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_NOW_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'daily') {
    $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_DAILY_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'weekly') {
    $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_WEEKLY_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'category') {
    $category = implode('\',\'', $_GET['category']);
    $sub_category = implode('\',\'', $_GET['sub_category']);
    $sql = "SELECT 
  TITLE,
  ID,
  IMG
FROM publixher.TBL_CONTENT
WHERE
WRITE_DATE > :INTERVAL
AND DEL = 'N'
AND EXPOSE > 1 
AND FOR_SALE = 'Y'";
    if($category!==null) {
        " AND CATEGORY IN ('" . $category . "')";
    }
    if($sub_category!==null){
        $sql.=" AND SUB_CATEGORY IN ('".$sub_category."')";
    }
    $sql.=" ORDER BY KNOCK + (COMMENT * 0.2)
DESC
LIMIT 10";

    $today = mktime();
    $interval = array(date("Y-m-d H:i:s",$today-(15*60)),date("Y-m-d H:i:s",$today-(60*60*24)),date("Y-m-d H:i:s",$today-(60*60*24*7)));
    $section = array('now', 'daily', 'weekly');
    $best = array();
    $prepare = $db->prepare($sql);
    for ($i = 0; $i < 3; $i++) {
        $prepare->execute(array('INTERVAL' => $interval[$i]));
        $best[$section[$i]] = $prepare->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($best, JSON_UNESCAPED_UNICODE);
}
?>