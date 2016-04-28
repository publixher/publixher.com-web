<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/passing_time.php';
$nowpage = $_GET['nowpage'] * 10;
session_start();
if (!isset($_SESSION['user'])) include_once "../../lib/loginchk.php";
$userID = $_SESSION['user']->getID();
//콘텐츠 검색임시로 그냥 다 불러오기
if ($_GET['profile']) {   //프로필에선 그사람이 쓴거,그사람이 타겟인거 시간순 노출
    //내 프로필일때는 내가쓴것내가 타겟인것 전부 가져온다
    if ($_GET['I'] == "true") {
        $sql = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG,ID_TARGET FROM publixher.TBL_CONTENT WHERE (DEL='N' AND (ID_WRITER=:ID_WRITER OR ID_TARGET=:ID_TARGET) AND REPORT<10) ORDER BY WRITE_DATE DESC LIMIT " . $nowpage . ",10;";
    } elseif ($_GET['frelation'] == "true") {
        //친구관계일때
        $sql = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG,ID_TARGET FROM publixher.TBL_CONTENT WHERE (DEL='N' AND (ID_WRITER=:ID_WRITER OR ID_TARGET=:ID_TARGET) AND EXPOSE>0 AND REPORT<10) ORDER BY WRITE_DATE DESC LIMIT " . $nowpage . ",10;";
    } else {
        //무관계일때
        $sql = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG,ID_TARGET FROM publixher.TBL_CONTENT WHERE (DEL='N' AND (ID_WRITER=:ID_WRITER OR ID_TARGET=:ID_TARGET) AND EXPOSE>1 AND REPORT<10) ORDER BY WRITE_DATE DESC LIMIT " . $nowpage . ",10;";
    }

    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_WRITER', $_GET['profile'], PDO::PARAM_STR);
    $prepare->bindValue(':ID_TARGET', $_GET['profile'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif (isset($_GET['fid'])) { //폴더에선 폴더 내용물이 시간순 노출
    //TODO:공개범위 설정 해야함
    $sql = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG FROM publixher.TBL_CONTENT WHERE (DEL='N' AND FOLDER=:FOLDER AND REPORT<10) ORDER BY WRITE_DATE DESC LIMIT " . $nowpage . ",10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':FOLDER', $_GET['fid'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif (isset($_GET['buylist'])) { //구매목록에선 구매한거 구매한 시간순(글쓴 시간순이 아님)으로 노출
    //구매리스트의 번호를 찾아온다
    $sql = "SELECT ID_CONTENT FROM publixher.TBL_BUY_LIST WHERE ID_USER=:ID_USER ORDER BY SEQ DESC LIMIT " . $nowpage . ",10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare->execute();
    $boughtlist = $prepare->fetchAll(PDO::FETCH_ASSOC);
    //찾아온 번호들로 컨텐츠 찾아오기
    $sql2 = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG FROM publixher.TBL_CONTENT WHERE (DEL='N' AND ID=:ID AND REPORT<10)";
    $prepare2 = $db->prepare($sql2);
    for ($i = 0; $i < count($boughtlist); $i++) {
        $prepare2->bindValue(':ID', $boughtlist[$i]['ID_CONTENT'], PDO::PARAM_STR);
        $prepare2->execute();
        $result[$i] = $prepare2->fetch(PDO::FETCH_ASSOC);
    }
} elseif (isset($_GET['getItem'])) {  //한개만 특정 주소로 가서 찾는것
    $sql = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG FROM publixher.TBL_CONTENT WHERE (DEL='N' AND ID=:ID AND REPORT<10)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $_GET['getItem'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif (isset($_GET['tag'])) {
    $sql = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG FROM publixher.TBL_CONTENT WHERE (DEL='N' AND MATCH(TAG) AGAINST('".$_GET['tag']."') AND REPORT<10) ORDER BY WRITE_DATE DESC LIMIT " . $nowpage . ",10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif(isset($_GET['body'])){
    $sql="SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG FROM publixher.TBL_CONTENT WHERE (DEL='N' AND MATCH(BODY_TEXT) AGAINST('*".$_GET['body']."*' IN BOOLEAN MODE) AND REPORT<10) ORDER BY WRITE_DATE DESC LIMIT ".$nowpage.",10";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
}else {  //메인화면에서 노출시켜줄 순

    $sql = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG FROM publixher.TBL_CONTENT WHERE (DEL='N' AND ID_TARGET IS NULL AND EXPOSE>1 AND REPORT<10) ORDER BY WRITE_DATE DESC LIMIT :NOWPAGE,10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':NOWPAGE', $nowpage);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
}


for ($i = 0; $i < count($result); $i++) {
    $sql = "SELECT USER_NAME,REPLACE(PIC,'profile','crop50') AS PIC FROM publixher.TBL_USER WHERE ID=:ID";
    $key = 'USER_NAME';
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $result[$i]['ID_WRITER'], PDO::PARAM_STR);
    $prepare->execute();
    $fetch = $prepare->fetch(PDO::FETCH_ASSOC);
    $val = $fetch['USER_NAME'];
    $result[$i][${key}] = $val;
    $result[$i]['WRITE_DATE'] = passing_time($result[$i]['WRITE_DATE']);
    $result[$i]['PIC'] = $fetch['PIC'];

    //폴더이름 가져오기
    if ($result[$i]['FOLDER']) {
        $fsql = "SELECT DIR FROM publixher.TBL_FOLDER WHERE ID=:ID";
        $fprepare = $db->prepare($fsql);
        $fprepare->bindValue(':ID', $result[$i]['FOLDER'], PDO::PARAM_INT);
        $fprepare->execute();
        $foldername = $fprepare->fetch(PDO::FETCH_ASSOC);
        $result[$i]['FOLDER_NAME'] = $foldername['DIR'];
    }
    //타겟 가져오기
    if ($result[$i]['ID_TARGET']) {
        $t = "SELECT USER_NAME FROM publixher.TBL_USER WHERE ID=:ID";
        $tp = $db->prepare($t);
        $tp->bindValue(':ID', $result[$i]['ID_TARGET']);
        $tp->execute();
        $result[$i]['TARGET_NAME'] = $tp->fetchColumn();
    }
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
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>