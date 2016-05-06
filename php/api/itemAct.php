<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';

$act = $_POST['action'];
if (!$act) {
    $act = $_GET['action'];
}
//액션에 따라 동작이 달라짐 knock,comment,commentreg,share,buy
if ($act == 'knock') {
    $ID = $_POST['contID'];
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
        if(!$result) {
            echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);

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
        echo '{"status":-3}';
    }
} elseif ($act == 'comment') {  //처음 불러오는거나 이상 불러오는거 둘다 이 분기로 들어가기
    require_once '../../lib/passing_time.php';
    $ID = $_GET['contID'];
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
  REPLACE(USER.PIC,'profile','crop34') AS PIC
FROM
  publixher.TBL_CONTENT_REPLY REPLY
  INNER JOIN publixher.TBL_USER AS USER ON REPLY.ID_USER=USER.ID
WHERE
  KNOCK + SUB_REPLY >= 10
  AND ID_CONTENT = :ID_CONTENT
ORDER BY
  KNOCK + SUB_REPLY DESC
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
            if(!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
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
  REPLACE(USER.PIC,'profile','crop34') AS PIC
FROM publixher.TBL_CONTENT_REPLY AS REPLY
  INNER JOIN publixher.TBL_USER AS USER ON REPLY.ID_USER=USER.ID
WHERE ID_CONTENT = :ID_CONTENT
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
            if(!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
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
  REPLACE(USER.PIC,'profile','crop34') AS PIC
FROM
  publixher.TBL_CONTENT_REPLY REPLY
  INNER JOIN publixher.TBL_FRIENDS FRIEND ON REPLY.ID_USER = FRIEND.ID_FRIEND
  INNER JOIN publixher.TBL_USER AS USER ON REPLY.ID_USER=USER.ID
WHERE
  FRIEND.ID_USER = :ID_USER
  AND REPLY.ID_CONTENT = :ID_CONTENT
ORDER BY
  REPLY.ID DESC
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
            if(!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }

    $sort = $_GET['sort'];
    $index = $_GET['index'];
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
    require_once '../../lib/random_64.php';
    require_once '../../lib/HTMLPurifier.php';
    $br = "/(\<br\>){3,}/i";
    $userID = $_POST['userID'];
    $ID = $_POST['contID'];
    $comment = preg_replace($br, '<br><br>', $_POST['comment']);
    $comment = $purifier->purify($comment);
    $uid = uniqueid($db, 'reply');
    $taglist = $_POST['taglist'];
    $taglist_len = count($taglist);
    $sql1 = "INSERT INTO publixher.TBL_CONTENT_REPLY(ID,ID_USER,ID_CONTENT,REPLY) VALUES(:ID,:ID_USER,:ID_CONTENT,:REPLY);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE ID=:ID;";
    $sql3 = "SELECT COMMENT,ID_WRITER FROM publixher.TBL_CONTENT WHERE ID=:ID;";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID', $uid, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY', $comment, PDO::PARAM_STR);
    $prepare1->execute();

    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare2->execute();

    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare3->execute();
    $result = $prepare3->fetch(PDO::FETCH_ASSOC);

    //알람처리
    $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR) VALUES(:ID_CONTENT,:ID_TARGET,3,:ID_ACTOR)";
    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_TARGET', $result['ID_WRITER'], PDO::PARAM_STR);
    $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare4->execute();
    //댓글에 태그된 사람도 알림처리
    $sql6 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR,ID_REPLY) VALUES(:ID_CONTENT,:ID_TARGET,9,:ID_ACTOR,:ID_REPLY)";
    $prepare6 = $db->prepare($sql6);
    $prepare6->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_REPLY', $uid, PDO::PARAM_STR);
    for ($i = 0; $i < $taglist_len; $i++) {
        $prepare6->bindValue(':ID_TARGET', $taglist[$i], PDO::PARAM_STR);
        $prepare6->execute();
    }
    $result = json_encode(array('status'=>1,'result'=>$result['COMMENT']), JSON_UNESCAPED_UNICODE);
    echo $result;

    $sql5 = "UPDATE publixher.TBL_PIN_LIST SET REPLY=REPLY+1,LAST_UPDATE=NOW() WHERE ID_CONTENT=:ID_CONTENT";
    $prepare5 = $db->prepare($sql5);
    $prepare5->bindValue(':ID_CONTENT', $ID);
    $prepare5->execute();
} elseif ($act == 'share') {

} elseif ($act == 'buy') {
    //TODO:userbirth와 isNick 정의해야함
    $userbirth =
    $isnick =
    $ID = $_POST['contID'];
    $userID = $_POST['userID'];
    //가격은 db의 데이터로 정해져야함
    $sql7 = "SELECT PRICE,ID_WRITER FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $prepare7 = $db->prepare($sql7);
    $prepare7->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare7->execute();
    $result = $prepare7->fetch(PDO::FETCH_ASSOC);
    $price = $result['PRICE'];
    $writer = $result['ID_WRITER'];
    //유저 id로 커넥터에 접속해서 캐쉬정보 가져오기
    if ($isnick == false) {
        $sql6 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE ID_USER=:ID_USER";
        $prepare6 = $db->prepare($sql6);
        $prepare6->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    } else {
        $sql6 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE ID_ANONY=:ID_ANONY";
        $prepare6 = $db->prepare($sql6);
        $prepare6->bindValue(':ID_ANONY', $userID, PDO::PARAM_STR);
    }
    $prepare6->execute();
    $usercash = $prepare6->fetch(PDO::FETCH_ASSOC);
    $usercash = $usercash['CASH_POINT'];
    //나이구하기
    $birthday = date("Y", strtotime($userbirth)); //생년월일
    $nowday = date('Y'); //현재날짜
    $age = floor($nowday - $birthday); //만나이

    if ($price > $usercash) {
        echo '{"status":-4}';
        exit;
    } else if ($age < 19) {
        echo '{"status":-5}';
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
            $sql2 = "INSERT INTO publixher.TBL_BUY_LIST(ID_USER,ID_CONTENT) VALUES(:ID_USER,:ID_CONTENT);";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
            $prepare2->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
            $prepare2->execute();

            $sql3 = "UPDATE publixher.TBL_CONTENT SET SALE=SALE+1 WHERE ID=:ID;";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue(':ID', $ID, PDO::PARAM_STR);
            $prepare3->execute();

            //알람처리
            $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR) VALUES(:ID_CONTENT,:ID_TARGET,1,:ID_ACTOR)";
            $prepare4 = $db->prepare($sql4);
            $prepare4->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
            $prepare4->bindValue(':ID_TARGET', $writer, PDO::PARAM_STR);
            $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
            $prepare4->execute();
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
            echo '{status":1}';
            exit;
        }catch(PDOException $e){
            $db->rollBack();
            echo '{"status":-1}';
            exit;
        }
    }
    echo '{"status":-6}';
    exit;
} elseif ($act == 'more') {
    //링크를 타고 온게 아니라 진짜로 샀는지 확인
    $ID = $_GET['contID'];
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
                echo json_encode(array('status'=>1,'result'=>$result), JSON_UNESCAPED_UNICODE);
            } else {
                echo '{"status":-7}';
            }
        }
    } else {
        //무료글일때
        $sql2 = "SELECT BODY FROM publixher.TBL_CONTENT WHERE ID=:ID";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID', $ID, PDO::PARAM_STR);
        $prepare2->execute();
        $result = $prepare2->fetch(PDO::FETCH_ASSOC);
        echo json_encode(array('status'=>1,'result'=>$result), JSON_UNESCAPED_UNICODE);
    }
} elseif ($act == 'del') {
    $ID = $_POST['contID'];
    $userID = $_POST['userID'];
    $sql1 = "SELECT ID_WRITER FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare1->execute();
    $result1 = $prepare1->fetch(PDO::FETCH_ASSOC);
    if ($result1['ID_WRITER'] == $userID) {
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
        //폴더에서 삭제
        $sql3 = "UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM-1 WHERE ID=:ID";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':ID', $folderid['FOLDER'], PDO::PARAM_STR);
        $prepare3->execute();
        //판매목록에서 삭제
        $sql5 = "DELETE FROM publixher.TBL_SELL_LIST WHERE ID_CONTENT=:ID_CONTENT";
        $prepare5 = $db->prepare($sql5);
        $prepare5->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
        $prepare5->execute();
        echo '{"status":1}';
    } else {
        echo '{"status":-8}';
    }
} elseif ($act == 'top') {
    //한번 확인해주고
    $sql1 = "UPDATE publixher.TBL_USER SET TOP_CONTENT=:TOP_CONTENT WHERE ID=:ID";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':TOP_CONTENT', $_POST['contID'], PDO::PARAM_STR);
    $prepare1->bindValue(':ID', $_POST['userID'], PDO::PARAM_STR);
    $prepare1->execute();
    echo '{"status":"1"}';
} elseif ($act == 'repknock') {
    //댓글에 노크처리
    $userID = $_POST['userID'];
    $ID = $_POST['repID'];
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
        $prepare1->bindValue(':ID_CONTENT', $_POST['contID'], PDO::PARAM_STR);
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
        echo json_encode(array('status'=>1,'result'=>array('KNOCK'=>$target['KNOCK'])),JSON_UNESCAPED_UNICODE);
    } else {
        echo '{"status":-3}';
    }
} elseif ($act == 'sub_comment') {
    require_once '../../lib/passing_time.php';
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
  IF(SUB_REP.DEL = 0, SUB_REP.REPLY, '해당 댓글은 삭제되었습니다.') AS REP_BODY,
  SUB_REP.DEL,
  SUB_REP.ID_USER,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop34') AS PIC
FROM publixher.TBL_CONTENT_SUB_REPLY AS SUB_REP
  INNER JOIN publixher.TBL_USER AS USER ON SUB_REP.ID_USER=USER.ID
WHERE SUB_REP.ID_REPLY = :ID_REPLY
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
            if(!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }

    $result = getTime($db, $repID, $_GET['index']);
    if (!$result) {
        echo '{"status":0}';
    }

} elseif ($act == 'commentreg_sub') {
    require_once '../../lib/random_64.php';
    require_once '../../lib/HTMLPurifier.php';
    $br = "/(\<br\>){3,}/i";
    $userID = $_POST['userID'];
    $ID = $_POST['contID'];
    $comment = preg_replace($br, '<br><br>', $_POST['comment']);
    $comment = $purifier->purify($comment);
    $repID = $_POST['repID'];
    $uid = uniqueid($db, 'sub_reply');
    $taglist = $_POST['taglist'];
    $taglist_len = count($taglist);
    $sql1 = "INSERT INTO publixher.TBL_CONTENT_SUB_REPLY(ID,ID_USER,ID_CONTENT,REPLY,ID_REPLY) VALUES(:ID,:ID_USER,:ID_CONTENT,:REPLY,:ID_REPLY);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE ID=:ID;";
    $sql3 = "SELECT SUB_REPLY,ID_USER FROM publixher.TBL_CONTENT_REPLY WHERE ID=:ID;";
    $sql4 = "UPDATE publixher.TBL_CONTENT_REPLY SET SUB_REPLY=SUB_REPLY+1 WHERE ID=:ID;";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID', $uid, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY', $comment, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_REPLY', $repID, PDO::PARAM_STR);
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
    $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR,ID_REPLY) VALUES(:ID_CONTENT,:ID_TARGET,7,:ID_ACTOR,:ID_REPLY)";
    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_TARGET', $result['ID_USER'], PDO::PARAM_STR);
    $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_REPLY', $repID, PDO::PARAM_STR);
    $prepare4->execute();
    //댓글에 태그된 사람도 알림처리
    $sql6 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR,ID_REPLY) VALUES(:ID_CONTENT,:ID_TARGET,9,:ID_ACTOR,:ID_REPLY)";
    $prepare6 = $db->prepare($sql6);
    $prepare6->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare6->bindValue(':ID_REPLY', $uid, PDO::PARAM_STR);
    for ($i = 0; $i < $taglist_len; $i++) {
        $prepare6->bindValue(':ID_TARGET', $taglist[$i], PDO::PARAM_STR);
        $prepare6->execute();
    }
    $result = '{"SUB_REPLY":'.$result['SUB_REPLY'].'}';
    echo json_encode(array('status' => 1, 'result' => array('SUB_REPLY' => $result['SUB_REPLY'])), JSON_UNESCAPED_UNICODE);
    $sql5 = "UPDATE publixher.TBL_PIN_LIST SET REPLY=REPLY+1,LAST_UPDATE=NOW() WHERE ID_CONTENT=:ID_CONTENT";
    $prepare5 = $db->prepare($sql5);
    $prepare5->bindValue(':ID_CONTENT', $ID);
    $prepare5->execute();
    exit;
} elseif ($act == 'addPin' OR $act == 'delPin') {
    $userID = $_POST['userID'];
    $ID = $_POST['contID'];
    try {
        $db->beginTransaction();
        if ($act == 'addPin') {
            $sql1 = "INSERT publixher.TBL_PIN_LIST (ID_CONTENT, ID_USER, ID_WRITER, BODY, WRITER_PIC)
  SELECT
    CONT.ID,
    :ID_USER,
    CONT.ID_WRITER,
    IF(CONT.TITLE IS NOT NULL, CONT.TITLE, LEFT(CONT.BODY_TEXT, 20)),
    USER.PIC
  FROM publixher.TBL_CONTENT AS CONT INNER JOIN publixher.TBL_USER AS USER ON CONT.ID_WRITER = USER.ID
  WHERE CONT.ID = :ID_CONTENT";
        } elseif ($act == 'delPin') {
            $sql1 = "DELETE FROM publixher.TBL_PIN_LIST WHERE ID_CONTENT=:ID_CONTENT AND ID_USER=:ID_USER";
        }
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
        $db->commit();
        echo '{"status":1}';
    } catch (PDOException $e) {
        $db->rollBack();
        echo '{"status":-1}';
        exit;
    }
} elseif ($act == 'rep_del') {
    $id = $_POST['repID'];
    $userID = $_POST['userID'];
    $type = $_POST['type'];
    if ($_POST['userID'] == $userID) {
        if ($type == true) $sql = "UPDATE publixher.TBL_CONTENT_REPLY SET DEL=1 WHERE ID=:ID";
        else $sql = "UPDATE publixher.TBL_CONTENT_SUB_REPLY SET DEL=1 WHERE ID=:ID";

        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID', $id);
        $prepare->execute();
        echo '{"status":1}';
    }
} elseif ($act == 'report') {
    $id = $_POST['contID'];
    $userID = $_POST['userID'];
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