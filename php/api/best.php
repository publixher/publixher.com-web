<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$action = $_REQUEST['act'];
if ($action == 'now') {
    if (isset($_REQUEST['category'])) {
        if (isset($_REQUEST['sub_category'])) {
            $sql = "SELECT SQL_CACHE
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'crop','crop80') AS WRITER_PIC
FROM publixher.TBL_NOW_HOT AS NOW
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=NOW.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
  AND CONT.SUB_CATEGORY=:SUB_CATEGORY
ORDER BY SEQ DESC
LIMIT 10";
        }else
        $sql = "SELECT SQL_CACHE
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'crop','crop80') AS WRITER_PIC
FROM publixher.TBL_NOW_HOT AS NOW
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=NOW.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
ORDER BY SEQ DESC
LIMIT 10";
    }else $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_NOW_HOT ORDER BY SEQ DESC LIMIT 10";

    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(array('result' => $result, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
} elseif ($action == 'daily') {
    if (isset($_REQUEST['category'])) {
        if (isset($_REQUEST['sub_category'])) {
            $sql = "SELECT SQL_CACHE
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'crop','crop80') AS WRITER_PIC
FROM publixher.TBL_DAILY_HOT AS DAILY
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=DAILY.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
  AND CONT.SUB_CATEGORY=:SUB_CATEGORY
ORDER BY SEQ DESC
LIMIT 10";
        }else 
        $sql = "SELECT SQL_CACHE
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'crop','crop80') AS WRITER_PIC
FROM publixher.TBL_DAILY_HOT AS DAILY
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=DAILY.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
ORDER BY SEQ DESC
LIMIT 10";
    }else $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_DAILY_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(array('result' => $result, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
} elseif ($action == 'weekly') {
    if (isset($_REQUEST['category'])) {
        if (isset($_REQUEST['sub_category'])) {
            $sql = "SELECT SQL_CACHE
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'crop','crop80') AS WRITER_PIC
FROM publixher.TBL_WEEKLY_HOT AS WEEK
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=WEEK.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
  AND CONT.SUB_CATEGORY=:SUB_CATEGORY
ORDER BY SEQ DESC
LIMIT 10";
        }else 
        $sql = "SELECT SQL_CACHE
  COMMENT,
  KNOCK,
  ID_CONTENT,
  ID_WRITER,
  USER_NAME,
  BODY,
  REPLACE(WRITER_PIC, 'crop','crop80') AS WRITER_PIC
FROM publixher.TBL_WEEKLY_HOT AS WEEK
  INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=WEEK.ID_CONTENT
  WHERE CONT.CATEGORY=:CATEGORY
ORDER BY SEQ DESC
LIMIT 10";
    }else $sql = "SELECT SQL_CACHE COMMENT,KNOCK,ID_CONTENT,ID_WRITER,USER_NAME,BODY,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_WEEKLY_HOT ORDER BY SEQ DESC LIMIT 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(array('result' => $result, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
} elseif ($action == 'category') {
    $category = implode('\',\'', $_REQUEST['category']);
    $sub_category = implode('\',\'', $_REQUEST['sub_category']);
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