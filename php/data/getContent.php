<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/passing_time.php';
$nowpage = $_GET['nowpage'] * 10;
session_start();
if(!isset($_SESSION['user'])) include_once "../../lib/loginchk.php";
$userseq = $_SESSION['user']->getSEQ();
//콘텐츠 검색임시로 그냥 다 불러오기
if ($_GET['profile']) {   //프로필에선 그사람이 쓴거,그사람이 타겟인거 시간순 노출
    //내 프로필일때는 내가쓴것내가 타겟인것 전부 가져온다
    if($_GET['I']=="true"){
        $sql = "SELECT * FROM publixher.TBL_CONTENT WHERE (DEL='N' AND (SEQ_WRITER=:SEQ_WRITER OR SEQ_TARGET=:SEQ_TARGET)) ORDER BY SEQ DESC LIMIT " . $nowpage . ",10;";
    }elseif($_GET['frelation']=="true"){
        //친구관계일때
        $sql = "SELECT * FROM publixher.TBL_CONTENT WHERE (DEL='N' AND (SEQ_WRITER=:SEQ_WRITER OR SEQ_TARGET=:SEQ_TARGET) AND EXPOSE>0) ORDER BY SEQ DESC LIMIT " . $nowpage . ",10;";
    }else{
        //무관계일때
        $sql = "SELECT * FROM publixher.TBL_CONTENT WHERE (DEL='N' AND (SEQ_WRITER=:SEQ_WRITER OR SEQ_TARGET=:SEQ_TARGET) AND EXPOSE>1) ORDER BY SEQ DESC LIMIT " . $nowpage . ",10;";
    }

    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ_WRITER', $_GET['profile'], PDO::PARAM_STR);
    $prepare->bindValue(':SEQ_TARGET', $_GET['profile'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif ($_GET['fid']) { //폴더에선 폴더 내용물이 시간순 노출
    $sql = "SELECT * FROM publixher.TBL_CONTENT WHERE (DEL='N' AND FOLDER=:FOLDER) ORDER BY SEQ DESC LIMIT " . $nowpage . ",10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':FOLDER', $_GET['fid'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} elseif ($_GET['buylist']) { //구매목록에선 구매한거 구매한 시간순(글쓴 시간순이 아님)으로 노출
    //구매리스트의 번호를 찾아온다
    $sql = "SELECT SEQ_CONTENT FROM publixher.TBL_BUY_LIST WHERE SEQ_USER=:SEQ_USER ORDER BY SEQ DESC LIMIT " . $nowpage . ",10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
    $prepare->execute();
    $boughtlist = $prepare->fetchAll(PDO::FETCH_ASSOC);
    //찾아온 번호들로 컨텐츠 찾아오기
    $sql2 = "SELECT * FROM publixher.TBL_CONTENT WHERE (DEL='N' AND SEQ=:SEQ)";
    $prepare2 = $db->prepare($sql2);
    for ($i = 0; $i < count($boughtlist); $i++) {
        $prepare2->bindValue(':SEQ', $boughtlist[$i]['SEQ_CONTENT'], PDO::PARAM_STR);
        $prepare2->execute();
        $result[$i] = $prepare2->fetch(PDO::FETCH_ASSOC);
    }
} elseif ($_GET['getItem']) {  //한개만 특정 주소로 가서 찾는것
    $sql = "SELECT * FROM publixher.TBL_CONTENT WHERE (DEL='N' AND SEQ=:SEQ)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ', $_GET['getItem'], PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
} else {  //메인화면에서 노출시켜줄 순
    $sql = "SELECT * FROM publixher.TBL_CONTENT WHERE (SEQ_WRITER = :SEQ_USER0 AND DEL='N' AND SEQ_TARGET IS NULL)"  //내거찾기

        . "UNION SELECT CONT1.* FROM publixher.TBL_CONTENT CONT1 INNER JOIN ("
        . "SELECT NOTI1.SEQ_CONTENT SEQ_ACT1 ,COUNT(DISTINCT NOTI1.SEQ_ACTOR) AS FCOUNT FROM "
        . "publixher.TBL_CONTENT_NOTI NOTI1 INNER JOIN publixher.TBL_FRIENDS FRIEND1 ON FRIEND1.SEQ_FRIEND = NOTI1.SEQ_ACTOR "
        . "WHERE FRIEND1.SEQ_USER = :SEQ_USER1 GROUP BY NOTI1.SEQ_CONTENT"
        . ") AS FRIEND_NOTI1 ON FRIEND_NOTI1.SEQ_ACT1 = CONT1.SEQ "
        . "WHERE (CONT1.DEL = 'N' AND CONT1.EXPOSE > 1 AND CASE WHEN FRIEND_NOTI1.FCOUNT>2 THEN CONT1.COMMENT + CONT1.KNOCK > 30 "    //내 친구중 1명~2명이 액션하고 50개이상 액션, 3명이상 액션하고 30개이상 액
        . "WHEN FRIEND_NOTI1.FCOUNT<=2 THEN CONT1.COMMENT + CONT1.KNOCK > 50 END AND CONT1.SEQ_TARGET IS NULL)"

        . "UNION SELECT DISTINCT CONT2.* FROM publixher.TBL_CONTENT CONT2 "
        . "INNER JOIN publixher.TBL_FRIENDS FRIEND2 ON FRIEND2.SEQ_FRIEND = CONT2.SEQ_WRITER "
        . "WHERE (FRIEND2.SEQ_USER = :SEQ_USER2 AND CONT2.DEL = 'N' AND CONT2.EXPOSE > 0 AND CONT2.SEQ_TARGET IS NULL)"   //내 친구가 쓰고 공개대상이 친구 이상인것
        . " ORDER BY SEQ DESC LIMIT "
        . ":NOWPAGE, 10";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ_USER0', $userseq);
    $prepare->bindValue(':SEQ_USER1', $userseq);
    $prepare->bindValue(':SEQ_USER2', $userseq);
    $prepare->bindValue(':NOWPAGE', $nowpage);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
}


for ($i = 0; $i < count($result); $i++) {
    $sql = "SELECT USER_NAME,PIC FROM publixher.TBL_USER WHERE SEQ=:SEQ";
    $key = 'USER_NAME';
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ', $result[$i]['SEQ_WRITER'], PDO::PARAM_STR);
    $prepare->execute();
    $fetch = $prepare->fetch(PDO::FETCH_ASSOC);
    $val = $fetch['USER_NAME'];
    $result[$i][${key}] = $val;
    $result[$i]['WRITE_DATE'] = passing_time($result[$i]['WRITE_DATE']);
    $result[$i]['PIC'] = $fetch['PIC'];

    //폴더이름 가져오기
    if ($result[$i]['FOLDER']) {
        $fsql = "SELECT DIR FROM publixher.TBL_FORDER WHERE SEQ=:SEQ";
        $fprepare = $db->prepare($fsql);
        $fprepare->bindValue(':SEQ', $result[$i]['FOLDER'], PDO::PARAM_INT);
        $fprepare->execute();
        $foldername = $fprepare->fetch(PDO::FETCH_ASSOC);
        $result[$i]['FOLDER_NAME'] = $foldername['DIR'];
    }
    //타겟 가져오기
    if ($result[$i]['SEQ_TARGET']) {
        $t = "SELECT USER_NAME FROM publixher.TBL_USER WHERE SEQ=:SEQ";
        $tp = $db->prepare($t);
        $tp->bindValue(':SEQ', $result[$i]['SEQ_TARGET']);
        $tp->execute();
        $result[$i]['TARGET_NAME'] = $tp->fetchColumn();
    }
    //유료일경우 구매한건지 안구매한건지 확인하는것
    if ($result[$i]['FOR_SALE'] == 'Y') {
        if ($result[$i]['SEQ_WRITER'] == $userseq) {
            $result[$i]['BOUGHT'] = $result[$i]['WRITE_DATE'];
        } else {
            $sql = "SELECT BUY_DATE FROM publixher.TBL_BUY_LIST WHERE SEQ_USER=:SEQ_USER AND SEQ_CONTENT=:SEQ_CONTENT";
            $prepare = $db->prepare($sql);
            $prepare->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
            $prepare->bindValue(':SEQ_CONTENT', $result[$i]['SEQ'], PDO::PARAM_STR);
            $prepare->execute();
            $bought = $prepare->fetch(PDO::FETCH_ASSOC);
            $result[$i]['BOUGHT'] = $bought['BUY_DATE'];
        }
    }
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>