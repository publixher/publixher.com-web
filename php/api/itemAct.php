<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/mobile_notification.php';

$act = $_REQUEST['action'];
if (!$act) {
    $act = $_REQUEST['action'];
}
//액션에 따라 동작이 달라짐
if ($act == 'knock') {
    $ID = $_REQUEST['ID'];
    $userID = $_REQUEST['userID'];

    //노크처리
    $sql1 = "INSERT INTO publixher.TBL_KNOCK_LIST(ID_USER,ID_CONTENT) VALUES(:ID_USER,:ID_CONTENT);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET KNOCK=KNOCK+1 WHERE ID=:ID;";
    $sql3 = "SELECT KNOCK,ID_WRITER,TAG,SUB_CATEGORY FROM publixher.TBL_CONTENT WHERE ID=:ID;";
    //insert문
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare1->execute();
    //update문
    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare2->execute();
    //select문
    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare3->execute();
    $result = $prepare3->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(array('result' => $result, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
    

    $sql5 = "UPDATE publixher.TBL_USER SET PIN=(SELECT REPLACE(PIN,:PIN_CONT,'') FROM (SELECT * FROM publixher.TBL_USER) AS publixher WHERE ID=:SUBQUERY_ID) WHERE ID=:ID";
    $prepare5 = $db->prepare($sql5);
    $prepare5->bindValue(':ID_CONTENT', $ID);
    $prepare5->execute();

    //TODO:흥미처리 해야함
//        //흥미 처리
//        $sql5="INSERT INTO publixher.TBL_USER_INTEREST(ID_USER,TYPE,INTEREST) VALUES(:ID_USER,:TYPE,:INTEREST)";
//        $ip=$db->prepare($sql5);
//        $ip->bindValue(':ID_USER',$userID);
//        //작성자를 추가
//        $ip->bindValue(':TYPE',1);
//        $ip->bindValue(':INTEREST', $result['ID_WRITER']);
//        $ip->execute();
//        //서브 카테고리를 추가
//        if($result['SUB_CATEGORY']){
//            $ip->bindValue(':TYPE',2);
//            $ip->bindValue(':INTEREST',$result['SUB_CATEGORY']);
//            $ip->execute();
//        }
//
//        //게시물의 태그를 추가
//        if($result['TAG']){
//            $ip->bindValue(':TYPE',0);
//            $tags=explode(' ',$result['TAG']);
//            $tagnum=count($tags);
//            for($i=0;$i<$tagnum;$i++){
//                $ip->bindValue(':INTEREST',$tags[$i]);
//                $ip->execute();
//            }
//        }
    
} elseif ($act == 'comment') {  //처음 불러오는거나 이상 불러오는거 둘다 이 분기로 들어가기
    require_once '../../lib/passing_time.php';
    $ID = $_REQUEST['contID'];
    $userID = $_REQUEST['userID'];
    function getWriter($result, $db)
    {
        for ($i = 0; $i < count($result); $i++) {   //각 댓글별로 쓴사람과 사진 가져오기
            $result[$i]['REPLY_DATE'] = passing_time($result[$i]['REPLY_DATE']);
        }
        return $result;
    }

    function getBest($db, $ID, $index)
    {
        //베스트댓글5개
        $bestrep_sql = "SELECT
  REPLY.ID,
  REPLY.REPLY_DATE,
  IF(REPLY.DEL = 0, REPLY.REPLY, '해당 댓글은 삭제되었습니다.') AS REP_BODY,
  REPLY.ID_USER,
  REPLY.KNOCK,
  REPLY.SUB_REPLY,
  REPLY.DEL,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop34') AS PIC,
  CONT.ID_WRITER AS CONTENT_WRITER
FROM
  publixher.TBL_CONTENT_REPLY REPLY
  INNER JOIN publixher.TBL_USER AS USER ON REPLY.ID_USER=USER.ID
  INNER JOIN publixher.TBL_CONTENT AS CONT ON REPLY.ID_CONTENT=CONT.ID
WHERE
  REPLY.KNOCK + SUB_REPLY >= 10
  AND ID_CONTENT = :ID_CONTENT
  AND ((REPLY.DEL=1 AND REPLY.SUB_REPLY>0) OR REPLY.DEL=0)
ORDER BY
  REPLY.KNOCK + SUB_REPLY DESC
LIMIT
  :INDEX, 6";
        $prepare1 = $db->prepare($bestrep_sql);
        $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
        $prepare1->bindValue(':INDEX', $index, PDO::PARAM_STR);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {    //베스트 있을때
            $result = getWriter($result, $db);
            $result['sort'] = "best";
            if ($result[5]) {
                $result['more'] = true;
                unset($result[5]);
            } else {
                $result['more'] = false;
            }
            if (!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result' => $result, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }

    function getTime($db, $ID, $index)
    {
        $timerep_sql = "SELECT
  REPLY.ID,
  REPLY.REPLY_DATE,
  IF(REPLY.DEL = 0, REPLY.REPLY, '해당 댓글은 삭제되었습니다.') AS REP_BODY,
  REPLY.ID_USER,
  REPLY.KNOCK,
  REPLY.SUB_REPLY,
  REPLY.DEL,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop34') AS PIC,
  CONT.ID_WRITER AS CONTENT_WRITER
FROM publixher.TBL_CONTENT_REPLY AS REPLY
  INNER JOIN publixher.TBL_USER AS USER ON REPLY.ID_USER=USER.ID
  INNER JOIN publixher.TBL_CONTENT AS CONT ON REPLY.ID_CONTENT=CONT.ID
WHERE ID_CONTENT = :ID_CONTENT
AND ((REPLY.DEL=1 AND REPLY.SUB_REPLY>0) OR REPLY.DEL=0)
ORDER BY REPLY.SEQ DESC
LIMIT :INDEX, 6";
        $prepare1 = $db->prepare($timerep_sql);
        $prepare1->bindValue(':ID_CONTENT', $ID);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $result = getWriter($result, $db);
            $result['sort'] = "time";
            if ($result[5]) {
                $result['more'] = true;
                unset($result[5]);
            } else {
                $result['more'] = false;
            }
            if (!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result' => $result, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }

    function getFrie($db, $ID, $userID, $index)
    {
        $friend_sql = "SELECT
  REPLY.ID,
  REPLY.REPLY_DATE,
  IF(REPLY.DEL = 0, REPLY.REPLY, '해당 댓글은 삭제되었습니다.') AS REP_BODY,
  REPLY.ID_USER,
  REPLY.KNOCK,
  REPLY.SUB_REPLY,
  REPLY.DEL,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop34') AS PIC,
  CONT.ID_WRITER AS CONTENT_WRITER
FROM
  publixher.TBL_CONTENT_REPLY REPLY
  INNER JOIN publixher.TBL_FRIENDS FRIEND ON REPLY.ID_USER = FRIEND.ID_FRIEND
  INNER JOIN publixher.TBL_USER AS USER ON REPLY.ID_USER=USER.ID
  INNER JOIN publixher.TBL_CONTENT AS CONT ON REPLY.ID_CONTENT=CONT.ID
WHERE
  FRIEND.ID_USER = :ID_USER
  AND REPLY.ID_CONTENT = :ID_CONTENT
  AND ((REPLY.DEL=1 AND REPLY.SUB_REPLY>0) OR REPLY.DEL=0)
ORDER BY
  REPLY.SEQ DESC
LIMIT
  :INDEX, 6";
        $prepare1 = $db->prepare($friend_sql);
        $prepare1->bindValue(':ID_CONTENT', $ID);
        $prepare1->bindValue(':ID_USER', $userID);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $result = getWriter($result, $db);
            $result['sort'] = "friend";
            if ($result[5]) {
                $result['more'] = true;
                unset($result[5]);
            } else {
                $result['more'] = false;
            }
            if (!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result' => $result, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }

    $sort = $_REQUEST['sort'];
    $index = $_REQUEST['index'];
    if ($sort == 'first') {
        //베댓이 없으면 자동으로 시간순 정렬된 댓글이 보여
        $result = getBest($db, $ID, $index);
        if (!$result) {
            $result = getTime($db, $ID, $index);
            if (!$result) {
                echo '{"status":0}';
            }
        }
    } elseif ($sort == 'best') { //처음으로 로딩한게 아니라 각 탭을 보는거면 sort로 구분한다
        $result = getBest($db, $ID, $index);
        if (!$result) {
            echo '{"status":0}';
        }
    } elseif ($sort == 'time') {
        $result = getTime($db, $ID, $index);
        if (!$result) {
            echo '{"status":0}';
        }
    } elseif ($sort == 'frie') {
        $result = getFrie($db, $ID, $userID, $index);
        if (!$result) {
            echo '{"status":0}';
        }
    }
} elseif ($act == 'commentreg') {
    require_once '../../lib/banchk.php';
    banCheck($_REQUEST['userID'], $db, -9);
    require_once '../../lib/random_64.php';
    require_once '../../lib/HTMLPurifier.php';
    $br = "/((\<div\>)?(\<br\>)(\<\/div\>)?){3,}/i";
    $userID = $_REQUEST['userID'];
    $ID = $_REQUEST['contID'];
    $comment = preg_replace($br, '<br><br>', $_REQUEST['comment']);
    $comment = $purifier->purify($comment);
    $uid = uniqueid($db, 'reply');
    $taglist = array_unique($_REQUEST['taglist'], SORT_STRING);
    $taglist_len = count($taglist);
    

    $sql1 = "INSERT INTO publixher.TBL_CONTENT_REPLY(ID,ID_USER,ID_CONTENT,REPLY,REPLY_TEXT) VALUES(:ID,:ID_USER,:ID_CONTENT,:REPLY,:REPLY_TEXT);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE ID=:ID;";
    $sql3 = "SELECT COMMENT,ID_WRITER FROM publixher.TBL_CONTENT WHERE ID=:ID;";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID', $uid, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY', $comment, PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY_TEXT', strip_tags($comment), PDO::PARAM_STR);
    $prepare1->execute();

    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare2->execute();

    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare3->execute();
    $result = $prepare3->fetch(PDO::FETCH_ASSOC);

    //알람처리
    $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR,ID_REPLY) VALUES(:ID_CONTENT,:ID_TARGET,3,:ID_ACTOR,:ID_REPLY)";
    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_TARGET', $result['ID_WRITER'], PDO::PARAM_STR);
    $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_REPLY', $uid, PDO::PARAM_STR);
    $prepare4->execute();
    //댓글에 태그된 사람도 알림처리
    $sql6 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR,ID_REPLY) VALUES(:ID_CONTENT,:ID_TARGET,8,:ID_ACTOR,:ID_REPLY)";
    $prepare6 = $db->prepare($sql6);
    $prepare6->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_REPLY', $uid, PDO::PARAM_STR);
    for ($i = 0; $i < $taglist_len; $i++) {
        $prepare6->bindValue(':ID_TARGET', $taglist[$i], PDO::PARAM_STR);
        $prepare6->execute();
    }
    $result = json_encode(array('status' => 1, 'result' => $result['COMMENT']), JSON_UNESCAPED_UNICODE);
    echo $result;

    $sql5 = "UPDATE publixher.TBL_PIN_LIST SET REPLY=REPLY+1,LAST_UPDATE=NOW() WHERE ID_CONTENT=:ID_CONTENT";
    $prepare5 = $db->prepare($sql5);
    $prepare5->bindValue(':ID_CONTENT', $ID);
    $prepare5->execute();
} elseif ($act == 'share') {

} elseif ($act == 'more') {
    //링크를 타고 온게 아니라 진짜로 샀는지 확인
    $ID = $_REQUEST['contID'];
    $userID = $_REQUEST['userID'];

        $sql2 = "SELECT BODY FROM publixher.TBL_CONTENT WHERE ID=:ID";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
        $prepare2->execute();
        $result = $prepare2->fetch(PDO::FETCH_ASSOC);
        echo json_encode(array('status' => 1, 'result' => $result), JSON_UNESCAPED_UNICODE);
    
} elseif ($act == 'del') {
    $ID = $_REQUEST['contID'];
    $userID = $_REQUEST['userID'];
    $prepare = $db->prepare("SELECT LEVEL FROM publixher.TBL_USER WHERE ID=:ID");
    $prepare->execute(array('ID' => $userID));
    $userLv = $prepare->fetchColumn(PDO::PARAM_INT);
    $sql1 = "SELECT ID_WRITER,ID_TARGET FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare1->execute();
    $result1 = $prepare1->fetch(PDO::FETCH_ASSOC);
    if ($result1['ID_WRITER'] == $userID || $result1['ID_TARGET'] == $userID || $userLv == 99) {
        //폴더 시퀀스 찾기
        $sql4 = "SELECT FOLDER FROM publixher.TBL_CONTENT WHERE ID=:ID";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':ID', $ID, PDO::PARAM_STR);
        $prepare4->execute();
        $folderid = $prepare4->fetch(PDO::FETCH_ASSOC);
        //컨텐츠에서 지워진걸로 처리
        $sql2 = "UPDATE publixher.TBL_CONTENT SET DEL='Y',FOLDER=NULL WHERE ID=:ID";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
        $prepare2->execute();
        if ($folderid != null) {
            //폴더에서 삭제
            $sql3 = "UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM-1 WHERE ID=:ID";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue(':ID', $folderid['FOLDER'], PDO::PARAM_STR);
            $prepare3->execute();
        }
        //판매목록에서 삭제
        $sql5 = "DELETE FROM publixher.TBL_SELL_LIST WHERE ID_CONTENT=:ID_CONTENT";
        $prepare5 = $db->prepare($sql5);
        $prepare5->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
        $prepare5->execute();
        //베스트에서 삭제
        $sql6 = "DELETE FROM publixher.TBL_NOW_HOT WHERE ID_CONTENT=:ID_CONTENT";
        $prepare = $db->prepare($sql6);
        $prepare->execute(array('ID_CONTENT' => $ID));
        $sql6 = "DELETE FROM publixher.TBL_DAILY_HOT WHERE ID_CONTENT=:ID_CONTENT";
        $prepare = $db->prepare($sql6);
        $prepare->execute(array('ID_CONTENT' => $ID));
        $sql6 = "DELETE FROM publixher.TBL_WEEKLY_HOT WHERE ID_CONTENT=:ID_CONTENT";
        $prepare = $db->prepare($sql6);
        $prepare->execute(array('ID_CONTENT' => $ID));

        echo '{"status":1}';
    } else {
        echo '{"status":-8}';
    }
} elseif ($act == 'top') {
    $sql1 = "UPDATE publixher.TBL_USER SET TOP_CONTENT=:TOP_CONTENT WHERE ID=:ID";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':TOP_CONTENT', $_REQUEST['contID'], PDO::PARAM_STR);
    $prepare1->bindValue(':ID', $_REQUEST['userID'], PDO::PARAM_STR);
    $prepare1->execute();
    echo '{"status":"1"}';
} elseif($act=="top-fall"){
    $sql="UPDATE publixher.TBL_USER SET TOP_CONTENT='' WHERE ID=:ID";
    $prepare=$db->prepare($sql);
    $prepare->execute(array('ID'=>$_REQUEST['mid']));
    echo '{"result":"1"}';
}elseif ($act == 'repknock') {
    //댓글에 노크처리
    $userID = $_REQUEST['userID'];
    $ID = $_REQUEST['repID'];
    $sql = "SELECT SEQ FROM publixher.TBL_CONTENT_REPLY_KNOCK WHERE (ID_USER=:ID_USER AND ID_REPLY=:ID_REPLY) LIMIT 1 ";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare->bindValue(':ID_REPLY', $ID, PDO::PARAM_STR);
    $prepare->execute();
    $knocked = $prepare->fetchColumn();
    if (!$knocked) {
        //노크처리
        $sql1 = "INSERT INTO publixher.TBL_CONTENT_REPLY_KNOCK(ID_USER,ID_REPLY,ID_CONTENT) VALUES(:ID_USER,:ID_REPLY,:ID_CONTENT);";
        $sql2 = "UPDATE publixher.TBL_CONTENT_REPLY SET KNOCK=KNOCK+1 WHERE ID=:ID;";
        $sql3 = "SELECT ID_USER,KNOCK,ID_CONTENT FROM publixher.TBL_CONTENT_REPLY WHERE ID=:ID;";
        //insert문
        $prepare1 = $db->prepare($sql1);
        $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
        $prepare1->bindValue(':ID_REPLY', $ID, PDO::PARAM_STR);
        $prepare1->bindValue(':ID_CONTENT', $_REQUEST['contID'], PDO::PARAM_STR);
        $prepare1->execute();
        //update문
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
        $prepare2->execute();
        //select문
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':ID', $ID, PDO::PARAM_STR);
        $prepare3->execute();
        $target = $prepare3->fetch(PDO::FETCH_ASSOC);

        //알람처리
        $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_REPLY,ID_TARGET,ACT,ID_ACTOR,ID_CONTENT) VALUES(:ID_REPLY,:ID_TARGET,6,:ID_ACTOR,:ID_CONTENT)";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':ID_REPLY', $ID, PDO::PARAM_STR);
        $prepare4->bindValue(':ID_TARGET', $target['ID_USER'], PDO::PARAM_STR);
        $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
        $prepare4->bindValue(':ID_CONTENT', $target['ID_CONTENT'], PDO::PARAM_STR);
        $prepare4->execute();
        echo json_encode(array('status' => 1, 'result' => array('KNOCK' => $target['KNOCK'])), JSON_UNESCAPED_UNICODE);
    } else {
        echo '{"status":-3}';
    }
} elseif ($act == 'sub_comment') {
    require_once '../../lib/passing_time.php';
    $userID = $_REQUEST['userID'];
    $repID = $_REQUEST['repID'];
    function getWriter($result, $db)
    {
        for ($i = 0; $i < count($result); $i++) {   //각 댓글별로 쓴사람과 사진 가져오기
            $result[$i]['REPLY_DATE'] = passing_time($result[$i]['REPLY_DATE']);
        }
        return $result;
    }

    function getTime($db, $repID, $index)
    {
        $timerep_sql = "SELECT
  SUB_REP.ID,
  SUB_REP.REPLY_DATE,
  SUB_REP.REPLY AS REP_BODY,
  SUB_REP.DEL,
  SUB_REP.ID_USER,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop34') AS PIC,
  CONT.ID_WRITER AS CONTENT_WRITER,
  REPLY.ID_USER AS REPLY_WRITER
FROM publixher.TBL_CONTENT_SUB_REPLY AS SUB_REP
  INNER JOIN publixher.TBL_USER AS USER ON SUB_REP.ID_USER=USER.ID
  INNER JOIN publixher.TBL_CONTENT AS CONT ON SUB_REP.ID_CONTENT=CONT.ID
  INNER JOIN publixher.TBL_CONTENT_REPLY AS REPLY ON SUB_REP.ID_REPLY=REPLY.ID
WHERE SUB_REP.ID_REPLY = :ID_REPLY AND SUB_REP.DEL=0
ORDER BY SUB_REP.SEQ DESC
LIMIT :INDEX, 6";
        $prepare1 = $db->prepare($timerep_sql);
        $prepare1->bindValue(':ID_REPLY', $repID);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $result = getWriter($result, $db);
            $result['sort'] = "time";
            if ($result[5]) {
                $result['more'] = true;
                unset($result[5]);
            } else {
                $result['more'] = false;
            }
            if (!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result' => $result, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }

    $result = getTime($db, $repID, $_REQUEST['index']);
    if (!$result) {
        echo '{"status":0}';
    }

} elseif ($act == 'commentreg_sub') {
    require_once '../../lib/banchk.php';
    banCheck($_REQUEST['userID'], $db, -9);
    require_once '../../lib/random_64.php';
    require_once '../../lib/HTMLPurifier.php';
    $br = "/((\<div\>)?(\<br\>)(\<\/div\>)?){3,}/i";
    $userID = $_REQUEST['userID'];
    $ID = $_REQUEST['contID'];
    $comment = preg_replace($br, '<br><br>', $_REQUEST['comment']);
    $comment = $purifier->purify($comment);
    $repID = $_REQUEST['repID'];
    $uid = uniqueid($db, 'sub_reply');
    $taglist = $_REQUEST['taglist'];
    $taglist_len = count($taglist);
    $sql1 = "INSERT INTO publixher.TBL_CONTENT_SUB_REPLY(ID,ID_USER,ID_CONTENT,REPLY,ID_REPLY,REPLY_TEXT) VALUES(:ID,:ID_USER,:ID_CONTENT,:REPLY,:ID_REPLY,:REPLY_TEXT);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE ID=:ID;";
    $sql3 = "SELECT SUB_REPLY,ID_USER FROM publixher.TBL_CONTENT_REPLY WHERE ID=:ID;";
    $sql4 = "UPDATE publixher.TBL_CONTENT_REPLY SET SUB_REPLY=SUB_REPLY+1 WHERE ID=:ID;";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID', $uid, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY', $comment, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_REPLY', $repID, PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY_TEXT', strip_tags($comment), PDO::PARAM_STR);
    $prepare1->execute();

    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare2->execute();

    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':ID', $repID, PDO::PARAM_STR);
    $prepare4->execute();

    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID', $repID, PDO::PARAM_STR);
    $prepare3->execute();
    $result = $prepare3->fetch(PDO::FETCH_ASSOC);

    //알람처리
    $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR,ID_REPLY,ID_SUB_REPLY) VALUES(:ID_CONTENT,:ID_TARGET,7,:ID_ACTOR,:ID_REPLY,:ID_SUB_REPLY)";
    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_TARGET', $result['ID_USER'], PDO::PARAM_STR);
    $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_REPLY', $repID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_SUB_REPLY', $uid, PDO::PARAM_STR);
    $prepare4->execute();
    //댓글에 태그된 사람도 알림처리
    $sql6 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR,ID_REPLY,ID_SUB_REPLY) VALUES(:ID_CONTENT,:ID_TARGET,9,:ID_ACTOR,:ID_REPLY,:ID_SUB_REPLY)";
    $prepare6 = $db->prepare($sql6);
    $prepare6->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_SUB_REPLY', $uid, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_REPLY', $repID, PDO::PARAM_STR);
    for ($i = 0; $i < $taglist_len; $i++) {
        $prepare6->bindValue(':ID_TARGET', $taglist[$i], PDO::PARAM_STR);
        $prepare6->execute();
    }
    $result = '{"SUB_REPLY":' . $result['SUB_REPLY'] . '}';
    echo json_encode(array('status' => 1, 'result' => array('SUB_REPLY' => $result['SUB_REPLY'])), JSON_UNESCAPED_UNICODE);
    $sql5 = "UPDATE publixher.TBL_PIN_LIST SET REPLY=REPLY+1,LAST_UPDATE=NOW() WHERE ID_CONTENT=:ID_CONTENT";
    $prepare5 = $db->prepare($sql5);
    $prepare5->bindValue(':ID_CONTENT', $ID);
    $prepare5->execute();
    exit;
} elseif ($act == 'addPin' OR $act == 'delPin') {
    $userID = $_REQUEST['userID'];
    $ID = $_REQUEST['contID'];
    try {
        $db->beginTransaction();
        if ($act == 'addPin') {
            $sql1 = "INSERT publixher.TBL_PIN_LIST (ID_CONTENT, ID_USER, ID_WRITER, BODY, WRITER_PIC)
  SELECT
    CONT.ID,
    :ID_USER,
    CONT.ID_WRITER,
    IF(CONT.TITLE IS NOT NULL,LEFT(CONT.TITLE,20),LEFT(CONT.BODY_TEXT, 20)),
    USER.PIC
  FROM publixher.TBL_CONTENT AS CONT INNER JOIN publixher.TBL_USER AS USER ON CONT.ID_WRITER = USER.ID
  WHERE CONT.ID = :ID_CONTENT";
            $_SESSION['user']->setPIN($_SESSION['user']->getPIN() . ' ' . $ID);
            $prepare1 = $db->prepare($sql1);
            $prepare1->bindValue(':ID_CONTENT', $ID);
            $prepare1->bindValue(':ID_USER', $userID);
            $prepare1->execute();
            //coalesce로 PIN값이 NULL이면 빈문자열로 치환해서 넣는다
            $sql2 = "UPDATE publixher.TBL_USER SET PIN=CONCAT(COALESCE(PIN,''),' ',:PIN) WHERE ID=:ID";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':PIN', $ID);
            $prepare2->bindValue(':ID', $userID);
            $prepare2->execute();
        } elseif ($act == 'delPin') {
            $sql1 = "DELETE FROM publixher.TBL_PIN_LIST WHERE ID_CONTENT=:ID_CONTENT AND ID_USER=:ID_USER";
            $_SESSION['user']->setPIN(str_replace(' ' . $ID, '', $_SESSION['user']->getPIN()));
            $prepare1 = $db->prepare($sql1);
            $prepare1->bindValue(':ID_CONTENT', $ID);
            $prepare1->bindValue(':ID_USER', $userID);
            $prepare1->execute();
            $sql2 = "UPDATE publixher.TBL_USER SET PIN=(SELECT REPLACE(PIN,:PIN_CONT,'') FROM publixher.TBL_USER WHERE ID=:SUBQUERY_ID) WHERE ID=:ID";
            $prepare2 = $db->prepare($sql2);
            $prepare2->execute(array('PIN_CONT' => $ID, 'ID' => $userID, 'SUBQUERY_ID' => $userID));
        }
        $db->commit();
        echo '{"status":1}';
    } catch (PDOException $e) {
        $db->rollBack();
        echo '{"status":-1}';
        exit;
    }
} elseif ($act == 'rep_del') {
    $id = $_REQUEST['repID'];
    $userID = $_REQUEST['userID'];
    $type = $_REQUEST['type'];
    if ($type == 0) {
        $sql = "SELECT CONT.ID_WRITER AS CONTENT_WRITER,REPLY.ID_USER AS REPLY_WRITER FROM publixher.TBL_CONTENT_REPLY AS REPLY 
INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=REPLY.ID_CONTENT 
WHERE REPLY.ID=:ID";
    } else {
        $sql = "SELECT CONT.ID_WRITER AS CONTENT_WRITER,REPLY.ID_USER AS REPLY_WRITER,SUB_REPLY.ID_USER AS SUB_REPLY_WRITER FROM publixher.TBL_CONTENT_SUB_REPLY AS SUB_REPLY 
INNER JOIN publixher.TBL_CONTENT_REPLY AS REPLY ON REPLY.ID=SUB_REPLY.ID_REPLY
INNER JOIN publixher.TBL_CONTENT AS CONT ON CONT.ID=SUB_REPLY.ID_CONTENT
WHERE SUB_REPLY.ID=:ID";
    }
    $prepare = $db->prepare($sql);
    $prepare->execute(array('ID' => $id));
    $ids = $prepare->fetch(PDO::FETCH_ASSOC);

    if ($userID == $ids['CONTENT_WRITER'] || $userID == $ids['REPLY_WRITER'] || $userID == $ids['SUB_REPLY_WRITER']) {
        if ($type == 0) {
            $sql1 = "UPDATE publixher.TBL_CONTENT_REPLY SET DEL=1 WHERE ID=:ID";
            $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT-1 WHERE ID=(SELECT ID_CONTENT FROM publixher.TBL_CONTENT_REPLY WHERE ID=:ID)";
        } else {
            $sql1 = "UPDATE publixher.TBL_CONTENT_SUB_REPLY SET DEL=1 WHERE ID=:ID";
            $sql2 = "UPDATE publixher.TBL_CONTENT_REPLY SET SUB_REPLY=SUB_REPLY-1 WHERE ID=(SELECT ID_REPLY FROM publixher.TBL_CONTENT_SUB_REPLY WHERE ID=:ID)";

        }

        $prepare = $db->prepare($sql1);
        $prepare->bindValue(':ID', $id);
        $prepare->execute();
        $prepare = $db->prepare($sql2);
        $prepare->execute(array('ID' => $id));
        echo '{"status":1}';
    }
} elseif ($act == 'report') {
    $id = $_REQUEST['contID'];
    $userID = $_REQUEST['userID'];
    $sql = "INSERT INTO publixher.TBL_CONTENT_REPORT(USER_ID,CONTENT_ID) VALUES(:USER_ID,:CONTENT_ID)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':USER_ID', $userID);
    $prepare->bindValue(':CONTENT_ID', $id);
    try {
        $prepare->execute();
        echo '{"status":1}';
        exit;
    } catch (PDOException $e) {
        echo '{"status":-2}';
        exit;
    }
}
?>