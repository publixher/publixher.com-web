<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/passing_time.php';
$nowpage = $_GET['nowpage'] * 10;
$userID = $_GET['userID'];
$type=$_GET['type'];
//콘텐츠 검색임시로 그냥 다 불러오기
if ($type=='profile') {   //프로필에선 그사람이 쓴거,그사람이 타겟인거 시간순 노출
    //내 프로필일때는 내가쓴것내가 타겟인것 전부 가져온다
    if ($_GET['I'] == true) {
        $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME,
  USER2.USER_NAME AS TARGET_NAME,
  USER2.ID AS TARGET_ID
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
  LEFT JOIN publixher.TBL_USER AS USER2
  ON USER2.ID=CONT.ID_TARGET
WHERE (DEL = 'N' AND (ID_WRITER = :ID_WRITER OR ID_TARGET = :ID_TARGET) AND REPORT < 10)
ORDER BY WRITE_DATE DESC
LIMIT " . $nowpage . ", 10;";
    } elseif ($_GET['frelation'] == true) {
        //친구관계일때
        $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME,
  USER2.USER_NAME AS TARGET_NAME,
  USER2.ID AS TARGET_ID
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
  LEFT JOIN publixher.TBL_USER AS USER2
  ON USER2.ID=CONT.ID_TARGET
WHERE (DEL = 'N' AND (ID_WRITER = :ID_WRITER OR ID_TARGET = :ID_TARGET) AND EXPOSE > 0 AND REPORT < 10)
ORDER BY WRITE_DATE DESC
LIMIT " . $nowpage . ", 10;";
    } else {
        //무관계일때
        $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME,
  USER2.USER_NAME AS TARGET_NAME,
  USER2.ID AS TARGET_ID
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
  LEFT JOIN publixher.TBL_USER AS USER2
  ON USER2.ID=CONT.ID_TARGET
WHERE (DEL = 'N' AND (ID_WRITER = :ID_WRITER OR ID_TARGET = :ID_TARGET) AND EXPOSE > 1 AND REPORT < 10)
ORDER BY WRITE_DATE DESC
LIMIT " . $nowpage . ", 10;";
    }

    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_WRITER', $_GET['target'], PDO::PARAM_STR);
    $prepare->bindValue(':ID_TARGET', $_GET['target'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type=='folder') { //폴더에선 폴더 내용물이 시간순 노출
    //TODO:공개범위 설정 해야함
    $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE (DEL = 'N' AND FOLDER = :FOLDER AND REPORT < 10)
ORDER BY WRITE_DATE DESC
LIMIT " . $nowpage . ", 10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':FOLDER', $_GET['fid'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type=='buyList') { //구매목록에선 구매한거 구매한 시간순(글쓴 시간순이 아님)으로 노출
    //구매리스트의 번호를 찾아온다
    $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_BUY_LIST AS BUY_LIST
  INNER JOIN publixher.TBL_CONTENT AS CONT
  ON BUY_LIST.ID_CONTENT=CONT.ID
  INNER JOIN publixher.TBL_USER AS USER
  ON CONT.ID_WRITER=USER.ID
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE BUY_LIST.ID_USER = :ID_USER
  AND CONT.DEL='N' AND REPORT<10
ORDER BY BUY_LIST.SEQ DESC
LIMIT " . $nowpage . ", 10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type=='one') {  //한개만 특정 주소로 가서 찾는것
    $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE (DEL = 'N' AND CONT.ID = :ID AND REPORT < 10)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $_GET['getItem'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type=='tag') {
    $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE (DEL = 'N' AND MATCH(TAG) AGAINST('" . $_GET['tag'] . "') AND REPORT < 10)
ORDER BY WRITE_DATE DESC
LIMIT " . $nowpage . ", 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type=='bodySearch') {
    $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE (DEL = 'N' AND MATCH(BODY_TEXT) AGAINST('*" . $_GET['body'] . "*' IN BOOLEAN MODE) AND REPORT < 10)
ORDER BY WRITE_DATE DESC
LIMIT " . $nowpage . ", 10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif($type=='main') {  //메인화면에서 노출시켜줄 순

    $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE (DEL = 'N' AND ID_TARGET IS NULL AND EXPOSE > 1 AND REPORT < 10)
ORDER BY WRITE_DATE DESC
LIMIT :NOWPAGE, 10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':NOWPAGE', $nowpage);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
}elseif($type=='top'){
    $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE (DEL = 'N' AND ID_TARGET IS NULL AND EXPOSE > 1 AND REPORT < 10 AND USER.ID=:USER_ID AND CONT.ID=USER.TOP_CONTENT)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':USER_ID', $_GET['target']);
    $prepare->execute();
    $result=$prepare->fetch(PDO::FETCH_ASSOC);
}


for ($i = 0; $i < count($result); $i++) {
    $result[$i]['WRITE_DATE'] = passing_time($result[$i]['WRITE_DATE']);
    //유료일경우 구매한건지 안구매한건지 확인하는것
    if ($result[$i]['FOR_SALE'] == 'Y') {
        if ($result[$i]['ID_WRITER'] == $userID) {
            $result[$i]['BOUGHT'] = $result[$i]['WRITE_DATE'];
        } else {
            $sql = "SELECT BUY_DATE FROM publixher.TBL_BUY_LIST WHERE ID_USER=:ID_USER AND ID_CONTENT=:ID_CONTENT";
            $prepare = $db->prepare($sql);
            $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
            $prepare->bindValue(':ID_CONTENT', $result[$i]['ID'], PDO::PARAM_STR);
            $prepare->execute();
            $bought = $prepare->fetch(PDO::FETCH_ASSOC);
            $result[$i]['BOUGHT'] = $bought['BUY_DATE'];
        }
    }
}
if(!$result) {
    echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
    exit;
}
echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
?>