<?php
header("Content-Type:application/json");
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) OR $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    exit('부정한 호출입니다.');
}
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
//토큰검사
session_start();
//CSRF검사
if (!isset($_POST['token']) AND !isset($_GET['token'])) {
    exit('부정한 조작이 감지되었습니다. case1 \n$_POST["token"] :' . $_POST['token'] . ' \n $_GET["token"] :' . $_GET['token'] . '$_SESSION :' . $_SESSION);
} elseif ($_POST['token'] != $_SESSION['token'] AND $_GET['token'] != $_SESSION['token']) {
    exit('부정한 조작이 감지되었습니다. case2 \n$_POST["token"] :' . $_POST['token'] . ' \n $_GET["token"] :' . $_GET['token'] . '$_SESSION :' . $_SESSION);
}

$act = $_POST['action'];
if (!$act) {
    $act = $_GET['action'];
}
if ($act == 'knock') {
    $ID = $_POST['ID'];
    $userID = $_POST['userID'];
    $sql = "SELECT SEQ FROM publixher.TBL_KNOCK_LIST WHERE (ID_USER=:ID_USER AND ID_CONTENT=:ID_CONTENT) LIMIT 1 ";
    $prepare = $db->prepare($sql);
    $prepare->bindValue('ID_USER', $userID, PDO::PARAM_STR);
    $prepare->bindValue('ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare->execute();
    $knocked = $prepare->fetch(PDO::FETCH_ASSOC);
    if (!$knocked) {
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
        echo json_encode($result, JSON_UNESCAPED_UNICODE);;
        //알람처리
        $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR) VALUES(:ID_CONTENT,:ID_TARGET,4,:ID_ACTOR)";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
        $prepare4->bindValue(':ID_TARGET', $result['ID_WRITER'], PDO::PARAM_STR);
        $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
        $prepare4->execute();

        $sql5 = "UPDATE publixher.TBL_PIN_LIST SET KNOCK=KNOCK+1,LAST_UPDATE=NOW() WHERE ID_CONTENT=:ID_CONTENT";
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
    } else {
        $prepare = $db->prepare("DELETE FROM publixher.TBL_KNOCK_LIST WHERE ID_USER=:ID_USER AND ID_CONTENT=:ID_CONTENT");
        $prepare->execute(array('ID_USER' => $userID, 'ID_CONTENT' => $ID));
        $prepare = $db->prepare("UPDATE publixher.TBL_CONTENT SET KNOCK=KNOCK-1 WHERE ID=:ID");
        $prepare->execute(array('ID' => $ID));
        $prepare = $db->prepare("DELETE FROM publixher.TBL_CONTENT_NOTI WHERE ID_CONTENT=:ID_CONTENT AND ID_ACTOR=:ID_ACTOR AND ACT=4");
        $prepare->execute(array('ID_CONTENT' => $ID, 'ID_ACTOR' => $userID));
        $prepare = $db->prepare("UPDATE publixher.TBL_PIN_LIST SET KNOCK=IF(KNOCK>0,KNOCK-1,0) WHERE ID_CONTENT=:ID_CONTENT");
        $prepare->execute(array('ID_CONTENT' => $ID));
        $prepare = $db->prepare("SELECT KNOCK FROM publixher.TBL_CONTENT WHERE ID=:ID");
        $prepare->execute(array('ID' => $ID));
        echo '{"result":"N","reason":"already","KNOCK":' . $prepare->fetchColumn() . '}';
    }
} elseif ($act == 'comment' OR $act == 'more_comment') {  //처음 불러오는거나 이상 불러오는거 둘다 이 분기로 들어가기
    require_once '../../lib/passing_time.php';
    $ID = $_GET['ID'];
    $userID = $_GET['userID'];
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
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }

    if ($act == 'comment') {
        $sort = $_GET['sort'];
        if ($sort == 'first') {
            //베댓이 없으면 자동으로 시간순 정렬된 댓글이 보여
            $result = getBest($db, $ID, 0);
            if (!$result) {
                $result = getTime($db, $ID, 0);
                if (!$result) {
                    echo '{"result":"NO"}';
                }
            }
        } elseif ($sort == 'best') { //처음으로 로딩한게 아니라 각 탭을 보는거면 sort로 구분한다
            $result = getBest($db, $ID, 0);
            if (!$result) {
                echo '{"result":"NO"}';
            }
        } elseif ($sort == 'time') {
            $result = getTime($db, $ID, 0);
            if (!$result) {
                echo '{"result":"NO"}';
            }
        } elseif ($sort == 'frie') {
            $result = getFrie($db, $ID, $userID, 0);
            if (!$result) {
                echo '{"result":"NO"}';
            }
        }
    } elseif ($act == 'more_comment') {
        $sort = $_GET['sort'];
        $index = $_GET['index'];
        if ($sort == 'best') {
            $result = getBest($db, $ID, $index);
            if (!$result) echo '{"result":"NO"}';
        } elseif ($sort == 'time') {
            $result = getTime($db, $ID, $index);
            if (!$result) echo '{"result":"NO"}';
        } elseif ($sort == 'frie') {
            $result = getFrie($db, $ID, $userID, $index);
            if (!$result) echo '{"result":"NO"}';
        }
    }
} elseif ($act == 'commentreg') {
    require_once '../../lib/banchk.php';
    banCheck($_POST['userID'], $db, -9);
    require_once '../../lib/random_64.php';
    require_once '../../lib/HTMLPurifier.php';
    $br = "/((\<div\>)?(\<br\>)(\<\/div\>)?){3,}/i";
    $userID = $_POST['userID'];
    $ID = $_POST['ID'];
    $comment = preg_replace($br, '<br><br>', $_POST['comment']);
    $comment = $purifier->purify($comment);
    $uid = uniqueid($db, 'reply');
    $taglist = array_unique($_POST['taglist'], SORT_STRING);
    $taglist_len = count($taglist);

    //후원기능 시작
    if (isset($_POST['donatelist'])) {
        $point = 0;
        foreach ($_POST['donatelist'] as $val) {
            $point = $point + $val;
        }
        $sql = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE ID_ANONY=:ID_ANONY OR ID_USER=:ID_USER";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID_ANONY', $userID);
        $prepare->bindValue(':ID_USER', $userID);
        $prepare->execute();
        $cash = $prepare->fetchColumn();
        if ($cash > $point) {
            try {
                $db->beginTransaction();
                //내 포인트 깎기
                $dsql = "UPDATE publixher.TBL_CONNECTOR SET CASH_POINT=CASH_POINT-:POINT WHERE ID_USER=:ID_USER OR ID_ANONY=:ID_ANONY";
                $dprepare = $db->prepare($dsql);
                $dprepare->bindValue(':POINT', $point);
                $dprepare->bindValue(':ID_ANONY', $userID);
                $dprepare->bindValue(':ID_USER', $userID);
                $dprepare->execute();
                //콘텐츠에 기부 올리기
                $upsql = "UPDATE publixher.TBL_CONTENT SET DONATE=DONATE+:POINT WHERE ID=:ID";
                $upprepare = $db->prepare($upsql);
                $upprepare->bindValue(':ID', $ID);
                $upprepare->bindValue(':POINT', $point);
                $upprepare->execute();
                //판매자의 포인트 올리기
                $psql = "UPDATE publixher.TBL_CONNECTOR AS CONN
  INNER JOIN publixher.TBL_CONTENT AS CONT
  ON CONT.ID_WRITER =CONN.ID_USER OR CONT.ID_WRITER=CONN.ID_ANONY
  SET CONN.CASH_POINT_EX=CONN.CASH_POINT_EX+:POINT WHERE CONT.ID=:ID";
                $pprepare = $db->prepare($psql);
                $pprepare->bindValue(':POINT', $point);
                $pprepare->bindValue(':ID', $ID);
                $pprepare->execute();
                //기부 테이블에 추가
                $isql = "INSERT INTO publixher.TBL_CONTENT_DONATE(ID_USER, ID_CONTENT,POINT) VALUES(:ID_USER,:ID_CONTENT,:POINT)";
                $iprepare = $db->prepare($isql);
                $iprepare->bindValue(':ID_USER', $userID);
                $iprepare->bindValue(':ID_CONTENT', $ID);
                $iprepare->bindValue(':POINT', $point);
                $iprepare->execute();
                $db->commit();
            } catch (PDOException $e) {
                $db->rollBack();
                echo '{"status":-1}';
                exit;
            }
        } else {
            echo '{"status":-2}';
            exit;
        }
    }
    $sql1 = "INSERT INTO publixher.TBL_CONTENT_REPLY(ID,ID_USER,ID_CONTENT,REPLY,REPLY_TEXT) VALUES(:ID,:ID_USER,:ID_CONTENT,:REPLY,:REPLY_TEXT);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE ID=:ID;";
    $sql3 = "SELECT COMMENT,ID_WRITER FROM publixher.TBL_CONTENT WHERE ID=:ID;";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID', $uid, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_USER', $_POST['userID'], PDO::PARAM_STR);
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
    $sql5 = "UPDATE publixher.TBL_PIN_LIST SET REPLY=REPLY+1,LAST_UPDATE=NOW() WHERE ID_CONTENT=:ID_CONTENT";
    $prepare5 = $db->prepare($sql5);
    $prepare5->bindValue(':ID_CONTENT', $ID);
    $prepare5->execute();

    $result = json_encode(array('COMMENT' => $result['COMMENT']), JSON_UNESCAPED_UNICODE);
    echo $result;


} elseif ($act == 'share') {

} elseif ($act == 'buy') {
    require_once '../../conf/User.php';
    session_start();
    $userinfo = $_SESSION['user'];
    $userbirth = $userinfo->getBIRTH();
    $isnick = $userinfo->getISNICK();
    $ID = $_POST['ID'];
    $userID = $_POST['userID'];
    //가격은 db의 데이터로 정해져야함
    $sql7 = "SELECT PRICE,ID_WRITER,AGE FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $prepare7 = $db->prepare($sql7);
    $prepare7->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare7->execute();
    $result = $prepare7->fetch(PDO::FETCH_ASSOC);
    $price = $result['PRICE'];
    $writer = $result['ID_WRITER'];
    //유저 id로 커넥터에 접속해서 캐쉬정보 가져오기
    $sql6 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE ID_USER=:ID_USER OR ID_ANONY=:ID_ANONY";
    $prepare6 = $db->prepare($sql6);
    $prepare6->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_ANONY', $userID, PDO::PARAM_STR);

    $prepare6->execute();
    $usercash = $prepare6->fetchColumn();
    //나이구하기
    $birthday = date("Y", strtotime($userbirth)); //생년월일
    $nowday = date('Y'); //현재날짜
    $age = floor($nowday - $birthday); //만나이

    if ($price > $usercash) {
        echo '{"buy":"f","reason":"not enough cash"}';
        exit;
    } else if ($age < 19 && $result['AGE']=='Y') {
        echo '{"buy":"f","reason":"age registration"}';
        exit;
    }
    //샀는지 확인
    $sql1 = "SELECT BUY_DATE FROM publixher.TBL_BUY_LIST WHERE (ID_USER=:ID_USER AND ID_CONTENT=:ID_CONTENT)";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare1->execute();
    $result = $prepare1->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        //안샀으면
        try {
            $db->beginTransaction();
            $sql2 = "INSERT INTO publixher.TBL_BUY_LIST(ID_USER,ID_CONTENT,PRICE) VALUES(:ID_USER,:ID_CONTENT,:PRICE);";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
            $prepare2->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
            $prepare2->bindValue(':PRICE', $price, PDO::PARAM_STR);
            $prepare2->execute();
            
            $sql="INSERT INTO publixher.TBL_SELL_LIST(ID_USER,ID_CONTENT) VALUES(:ID_USER,:ID_CONTENT)";
            $prepare=$db->prepare($sql);
            $prepare->execute(array('ID_USER'=>$userID,'ID_CONTENT'=>$ID));

            $sql3 = "UPDATE publixher.TBL_CONTENT SET SALE=SALE+1 WHERE ID=:ID;";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue(':ID', $ID, PDO::PARAM_STR);
            $prepare3->execute();

            $sql4 = "UPDATE publixher.TBL_CONNECTOR SET CASH_POINT=CASH_POINT-:POINT WHERE ID_USER=:ID_USER OR ID_ANONY=:ID_ANONY";
            $prepare4 = $db->prepare($sql4);
            $prepare4->execute(array('POINT' => $price, 'ID_USER' => $userID, 'ID_ANONY' => $userID));

            //판매자 포인트 늘림
            $sql="UPDATE publixher.TBL_CONNECTOR SET CASH_POINT_EX=CASH_POINT_EX+:POINT WHERE ID_USER=:ID_USER OR ID_ANONY=:ID_ANONY";
            $prepare=$db->prepare($sql);
            $prepare->execute(array('POINT'=>$price,'ID_USER'=>$writer,'ID_ANONY'=>$writer));
            //알람처리
            $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR) VALUES(:ID_CONTENT,:ID_TARGET,1,:ID_ACTOR)";
            $prepare4 = $db->prepare($sql4);
            $prepare4->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
            $prepare4->bindValue(':ID_TARGET', $writer, PDO::PARAM_STR);
            $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
            $prepare4->execute();
            echo '{"buy":"t"}';
//TODO:흥미처리해야함
//        //흥미 처리
//        $sql6 = "SELECT ID_WRITER,TAG,SUB_CATEGORY FROM publixher.TBL_CONTENT WHERE ID=:ID;";
//        $intp=$db->prepare($sql6);
//        $intp->bindValue(':ID',$ID);
//        $intp->execute();
//        $result=$intp->fetch(PDO::FETCH_ASSOC);
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
            $db->commit();
            exit;
        } catch (PDOException $e) {
            $db->rollBack();
            echo '{"status":-1}';
            exit;
        }
    }
    echo '{"buy":"f","reason":"already bought"}';
    exit;
} elseif ($act == 'more') {
    //링크를 타고 온게 아니라 진짜로 샀는지 확인
    $ID = $_GET['ID'];
    $userID = $_GET['userID'];
    //유료글인지 무료글인지 확인
    $sql = "SELECT FOR_SALE,ID_WRITER FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetch(PDO::FETCH_ASSOC);
    //유료글일때
    if ($result['FOR_SALE'] == "Y") {
        //자기 글일땐 허용
        if ($result['ID_WRITER'] == $userID) {
            $sql2 = "SELECT BODY FROM publixher.TBL_CONTENT WHERE ID=:ID";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
            $prepare2->execute();
            $result = $prepare2->fetch(PDO::FETCH_ASSOC);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {
            //남의글일땐 샀는지 확인
            $sql1 = "SELECT BUY_DATE FROM publixher.TBL_BUY_LIST WHERE (ID_USER=:ID_USER AND ID_CONTENT=:ID_CONTENT)";
            $prepare1 = $db->prepare($sql1);
            $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
            $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
            $prepare1->execute();
            $result = $prepare1->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $sql2 = "SELECT BODY FROM publixher.TBL_CONTENT WHERE ID=:ID";
                $prepare2 = $db->prepare($sql2);
                $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
                $prepare2->execute();
                $result = $prepare2->fetch(PDO::FETCH_ASSOC);
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            } else {
                echo '{"result":"not bought"}';
            }
        }
    } else {
        //무료글일때
        $sql2 = "SELECT BODY FROM publixher.TBL_CONTENT WHERE ID=:ID";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
        $prepare2->execute();
        $result = $prepare2->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
} elseif ($act == 'del') {
    $userinfo = $_SESSION['user'];
    $ID = $_POST['ID'];
    $userID = $userinfo->getID();
    $sql1 = "SELECT ID_WRITER,ID_TARGET FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare1->execute();
    $result1 = $prepare1->fetch(PDO::FETCH_ASSOC);
    if ($result1['ID_WRITER'] == $userID OR $userinfo->getLEVEL() >= 99 || $result1['ID_TARGET']==$userID) {
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
        if($folderid!=null) {
            //폴더에서 삭제
            $sql3 = "UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM-1 WHERE ID=:ID";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue(':ID', $folderid['FOLDER'], PDO::PARAM_STR);
            $prepare3->execute();
        }
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
        echo '{"result":"Y"}';
    } else {
        echo '{"result":"N","reason":"user not writer"}';
    }
} elseif ($act == 'top') {
    //한번 확인해주고
    $sql1 = "UPDATE publixher.TBL_USER SET TOP_CONTENT=:TOP_CONTENT WHERE ID=:ID";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':TOP_CONTENT', $_POST['ID'], PDO::PARAM_STR);
    $prepare1->bindValue(':ID', $_POST['mid'], PDO::PARAM_STR);
    $prepare1->execute();
    echo '{"result":"Y"}';
} elseif ($act == 'repknock') {
    //댓글에 노크처리
    $userID = $_POST['mid'];
    $ID = $_POST['ID'];
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
        $prepare1->bindValue(':ID_CONTENT', $_POST['thisitemID'], PDO::PARAM_STR);
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
        echo '{"KNOCK":' . $target['KNOCK'] . '}';
    } else {
        echo '{"result":"N","reason":"already"}';
    }
} elseif ($act == 'sub_comment' or $act == 'more_sub_comment') {
    require_once '../../lib/passing_time.php';
    $ID = $_GET['ID'];
    $userID = $_GET['userID'];
    $repID = $_GET['repID'];
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
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }

    if ($act == 'sub_comment') {
        $result = getTime($db, $repID, 0);
        if (!$result) {
            echo '{"result":"NO"}';
        }
    } else {
        $result = getTime($db, $repID, $_GET['index']);
        if (!$result) {
            echo '{"result":"NO"}';
        }
    }

} elseif ($act == 'commentreg_sub') {
    require_once '../../lib/banchk.php';
    banCheck($_POST['userID'], $db, -9);
    require_once '../../lib/random_64.php';
    require_once '../../lib/HTMLPurifier.php';
    $br = "/((\<div\>)?(\<br\>)(\<\/div\>)?){3,}/i";
    $userID = $_POST['userID'];
    $ID = $_POST['ID'];
    $comment = preg_replace($br, '<br><br>', $_POST['comment']);
    $comment = $purifier->purify($comment);
    $repID = $_POST['repID'];
    $uid = uniqueid($db, 'sub_reply');
    $taglist = $_POST['taglist'];
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
    echo $result;
    $sql5 = "UPDATE publixher.TBL_PIN_LIST SET REPLY=REPLY+1,LAST_UPDATE=NOW() WHERE ID_CONTENT=:ID_CONTENT";
    $prepare5 = $db->prepare($sql5);
    $prepare5->bindValue(':ID_CONTENT', $ID);
    $prepare5->execute();
    exit;
} elseif ($act == 'addPin' OR $act == 'delPin') {
    $userID = $_POST['userID'];
    $ID = $_POST['ID'];
    try {
        $db->beginTransaction();
        if ($act == 'addPin') {
            $sql1 = "INSERT publixher.TBL_PIN_LIST (ID_CONTENT, ID_USER, ID_WRITER, BODY, WRITER_PIC)
  SELECT
    CONT.ID,
    :ID_USER,
    CONT.ID_WRITER,
    IF(CONT.TITLE IS NOT NULL, LEFT(CONT.TITLE,20), LEFT(CONT.BODY_TEXT, 20)),
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
            $sql2= "UPDATE publixher.TBL_USER SET PIN=(SELECT REPLACE(PIN,:PIN_CONT,'') FROM (SELECT * FROM publixher.TBL_USER) AS publixher WHERE ID=:SUBQUERY_ID) WHERE ID=:ID";
            $prepare2=$db->prepare($sql2);
            $prepare2->execute(array('PIN_CONT'=>$ID,'ID'=>$userID,'SUBQUERY_ID'=>$userID));
        }
        $db->commit();
    } catch (PDOException $e) {
        $db->rollBack();
        echo '{"result":"N"}';
        exit;
    }
    echo '{"result":"Y"}';
} elseif ($act == 'rep_del') {
    $id = $_POST['ID'];
    $userID = $_SESSION['user']->getID();
    $type = $_POST['type'];
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
    $prepare->execute(array('ID'=>$id));
    $ids=$prepare->fetch(PDO::FETCH_ASSOC);

    if ($userID==$ids['CONTENT_WRITER'] || $userID==$ids['REPLY_WRITER'] || $userID==$ids['SUB_REPLY_WRITER']) {
        if ($type == 0) {
            $sql1 = "UPDATE publixher.TBL_CONTENT_REPLY SET DEL=1 WHERE ID=:ID";
            $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT-1 WHERE ID=(SELECT ID_CONTENT FROM publixher.TBL_CONTENT_REPLY WHERE ID=:ID)";
        } else {
            $sql1 = "UPDATE publixher.TBL_CONTENT_SUB_REPLY SET DEL=1 WHERE ID=:ID";
            $sql2 = "UPDATE publixher.TBL_CONTENT_REPLY SET SUB_REPLY=SUB_REPLY-1 WHERE ID=(SELECT ID_REPLY FROM publixher.TBL_CONTENT_SUB_REPLY WHERE ID=:ID)";
            $sql3 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT-1 WHERE ID=(SELECT ID_CONTENT FROM publixher.TBL_CONTENT_SUB_REPLY WHERE ID=:ID)";
        }

        $prepare = $db->prepare($sql1);
        $prepare->bindValue(':ID', $id);
        $prepare->execute();
        $prepare = $db->prepare($sql2);
        $prepare->execute(array('ID' => $id));
        if ($type != 0) {
            $prepare = $db->prepare($sql3);
            $prepare->execute(array('ID' => $id));
        }
        echo '{"result":"Y"}';
    }
} elseif ($act == 'report') {
    $id = $_POST['ID'];
    $userID = $_POST['userID'];
    $sql = "INSERT INTO publixher.TBL_CONTENT_REPORT(USER_ID,CONTENT_ID) VALUES(:USER_ID,:CONTENT_ID)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':USER_ID', $userID);
    $prepare->bindValue(':CONTENT_ID', $id);
    try {
        $prepare->execute();
        echo '{"result":"Y"}';
        exit;
    } catch (PDOException $e) {
        echo '{"reason":"already"}';
        exit;
    }
}
?>