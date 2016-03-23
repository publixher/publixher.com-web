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
    exit('부정한 조작이 감지되었습니다.');
} elseif ($_POST['token'] != $_SESSION['token'] AND $_GET['token'] != $_SESSION['token']) {
    exit('부정한 조작이 감지되었습니다.');
}
//세션탈취 검사
if (!isset($_POST['age']) AND !isset($_GET['age'])) {
    exit('부정한 조작이 감지되었습니다.');
} elseif ($_POST['age'] != $_SESSION['age'] AND $_GET['age'] != $_SESSION['age']) {
    exit('부정한 조작이 감지되었습니다.');
}

$act = $_POST['action'];
if (!$act) {
    $act = $_GET['action'];
}
//액션에 따라 동작이 달라짐 knock,comment,commentreg,share,buy
if ($act == 'knock') {
    $seq = $_POST['seq'];
    $userseq = $_POST['userseq'];
    $sql = "SELECT SEQ FROM publixher.TBL_KNOCK_LIST WHERE (SEQ_USER=:SEQ_USER AND SEQ_CONTENT=:SEQ_CONTENT) LIMIT 1 ";
    $prepare = $db->prepare($sql);
    $prepare->bindValue('SEQ_USER', $userseq, PDO::PARAM_STR);
    $prepare->bindValue('SEQ_CONTENT', $seq, PDO::PARAM_STR);
    $prepare->execute();
    $knocked = $prepare->fetch(PDO::FETCH_ASSOC);
    if (!$knocked) {
        //노크처리
        $sql1 = "INSERT INTO publixher.TBL_KNOCK_LIST(SEQ_USER,SEQ_CONTENT) VALUES(:SEQ_USER,:SEQ_CONTENT);";
        $sql2 = "UPDATE publixher.TBL_CONTENT SET KNOCK=KNOCK+1 WHERE SEQ=:SEQ;";
        $sql3 = "SELECT KNOCK,SEQ_WRITER FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ;";
        //insert문
        $prepare1 = $db->prepare($sql1);
        $prepare1->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
        $prepare1->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
        $prepare1->execute();
        //update문
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ', $seq, PDO::PARAM_STR);
        $prepare2->execute();
        //select문
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':SEQ', $seq, PDO::PARAM_STR);
        $prepare3->execute();
        $result = $prepare3->fetch(PDO::FETCH_ASSOC);

        //알람처리
        $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(SEQ_CONTENT,SEQ_TARGET,ACT,SEQ_ACTOR) VALUES(:SEQ_CONTENT,:SEQ_TARGET,4,:SEQ_ACTOR)";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_TARGET', $result['SEQ_WRITER'], PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_ACTOR', $userseq, PDO::PARAM_STR);
        $prepare4->execute();
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        echo $result;
    } else {
        echo '{"result":"N","reason":"already"}';
    }
} elseif ($act == 'comment' OR $act == 'more_comment') {  //처음 불러오는거나 이상 불러오는거 둘다 이 분기로 들어가기
    require_once '../../lib/passing_time.php';
    $seq = $_GET['seq'];
    $userseq=$_GET['userseq'];
    function getWriter($result, $db)
    {
        for ($i = 0; $i < count($result); $i++) {   //각 댓글별로 쓴사람과 사진 가져오기
            $sql2 = "SELECT USER_NAME,PIC FROM publixher.TBL_USER WHERE SEQ=:SEQ";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':SEQ', $result[$i]['SEQ_USER'], PDO::PARAM_STR);
            $prepare2->execute();
            $fetch = $prepare2->fetch(PDO::FETCH_ASSOC);
            $result[$i]['USER_NAME'] = $fetch['USER_NAME'];
            $result[$i]['REPLY_DATE'] = passing_time($result[$i]['REPLY_DATE']);
            $result[$i]['PIC'] = $fetch['PIC'];
        }
        return $result;
    }
    function getBest($db, $seq,$index)
    {
        $bestrep_sql = "SELECT \n"  //베스트댓글5개
            . "	* \n"
            . "FROM \n"
            . "	publixher.TBL_CONTENT_REPLY REPLY \n"
            . "WHERE \n"
            . "	KNOCK + SUB_REPLY >= 10 \n"
            . "	AND SEQ_CONTENT = :SEQ_CONTENT \n"
            . "ORDER BY \n"
            . "	KNOCK + SUB_REPLY DESC \n"
            . "LIMIT \n"
            . ":INDEX, 5";
        $prepare1 = $db->prepare($bestrep_sql);
        $prepare1->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
        $prepare1->bindValue(':INDEX', $index, PDO::PARAM_STR);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {    //베스트 있을때
            $result = getWriter($result, $db);
            $result['sort'] = "best";
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }
    function getTime($db, $seq,$index)
    {
        $timerep_sql = "SELECT * FROM publixher.TBL_CONTENT_REPLY WHERE SEQ_CONTENT=:SEQ_CONTENT ORDER BY SEQ DESC LIMIT :INDEX,5";
        $prepare1 = $db->prepare($timerep_sql);
        $prepare1->bindValue(':SEQ_CONTENT', $seq);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $result = getWriter($result, $db);
            $result['sort'] = "time";
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }
    function getFrie($db,$seq,$userseq,$index){
        $friend_sql = "SELECT \n"
            . "	REPLY.* \n"
            . "FROM \n"
            . "	publixher.TBL_CONTENT_REPLY REPLY STRAIGHT_JOIN publixher.TBL_FRIENDS FRIEND ON REPLY.SEQ_USER = FRIEND.SEQ_FRIEND \n"
            . "WHERE \n"
            . "	FRIEND.SEQ_USER = :SEQ_USER \n"
            . "	AND REPLY.SEQ_CONTENT = :SEQ_CONTENT \n"
            . "ORDER BY \n"
            . "	REPLY.SEQ DESC \n"
            . "LIMIT \n"
            . "	:INDEX, 5";
        $prepare1 = $db->prepare($friend_sql);
        $prepare1->bindValue(':SEQ_CONTENT', $seq);
        $prepare1->bindValue(':SEQ_USER', $userseq);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if($result){
            $result = getWriter($result, $db);
            $result['sort']="friend";
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        }else{
            return false;
        }
    }
    if ($act == 'comment') {
        $sort = $_GET['sort'];
        if ($sort == 'first') {
            //베댓이 없으면 자동으로 시간순 정렬된 댓글이 보여
            $result = getBest($db, $seq,0);
            if (!$result) {
                $result = getTime($db, $seq,0);
                if (!$result) {
                    echo '{"result":"NO"}';
                }
            }
        } elseif ($sort== 'best') { //처음으로 로딩한게 아니라 각 탭을 보는거면 sort로 구분한다
            $result=getBest($db,$seq,0);
            if(!$result){
                echo '{"result":"NO"}';
            }
        }elseif($sort=='time'){
            $result=getTime($db,$seq,0);
            if(!$result){
                echo '{"result":"NO"}';
            }
        }elseif($sort=='frie'){
            $result=getFrie($db,$seq,$userseq,0);
            if(!$result){
                echo '{"result":"NO"}';
            }
        }
    } elseif ($act == 'more_comment') {
        $sort = $_GET['sort'];
        $index = $_GET['index'];
        if ($sort == 'best') {
            $result=getBest($db,$seq,$index);
            if(!$result) echo '{"result":"NO"}';
        } elseif ($sort == 'time') {
            $result=getTime($db,$seq,$index);
            if(!$result) echo '{"result":"NO"}';
        } elseif ($sort == 'frie') {
            $result=getFrie($db,$seq,$userseq,$index);
            if(!$result) echo '{"result":"NO"}';
        }
    }
} elseif ($act == 'commentreg') {
    $userseq = $_POST['userseq'];
    $seq = $_POST['seq'];
    $comment = $_POST['comment'];
    $sql1 = "INSERT INTO publixher.TBL_CONTENT_REPLY(SEQ_USER,SEQ_CONTENT,REPLY) VALUES(:SEQ_USER,:SEQ_CONTENT,:REPLY);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE SEQ=:SEQ;";
    $sql3 = "SELECT COMMENT,SEQ_WRITER FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ;";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ_USER', $_POST['userseq'], PDO::PARAM_STR);
    $prepare1->bindValue(':SEQ_CONTENT', $_POST['seq'], PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY', $_POST['comment'], PDO::PARAM_STR);
    $prepare1->execute();

    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':SEQ', $_POST['seq'], PDO::PARAM_STR);
    $prepare2->execute();

    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':SEQ', $_POST['seq'], PDO::PARAM_STR);
    $prepare3->execute();
    $result = $prepare3->fetch(PDO::FETCH_ASSOC);

    //알람처리
    $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(SEQ_CONTENT,SEQ_TARGET,ACT,SEQ_ACTOR) VALUES(:SEQ_CONTENT,:SEQ_TARGET,3,:SEQ_ACTOR)";
    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
    $prepare4->bindValue(':SEQ_TARGET', $result['SEQ_WRITER'], PDO::PARAM_STR);
    $prepare4->bindValue(':SEQ_ACTOR', $userseq, PDO::PARAM_STR);
    $prepare4->execute();
    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $result;
} elseif ($act == 'share') {

} elseif ($act == 'buy') {
    require_once '../../conf/User.php';
    session_start();
    $userinfo = $_SESSION['user'];
    $userbirth = $userinfo->getBirth();
    $isnick = $userinfo->getIsNick();
    $seq = $_POST['seq'];
    $userseq = $_POST['userseq'];
    //가격은 db의 데이터로 정해져야함
    $sql7 = "SELECT PRICE,SEQ_WRITER FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ";
    $prepare7 = $db->prepare($sql7);
    $prepare7->bindValue(':SEQ', $seq, PDO::PARAM_STR);
    $prepare7->execute();
    $result = $prepare7->fetch(PDO::FETCH_ASSOC);
    $price = $result['PRICE'];
    $writer = $result['SEQ_WRITER'];
    //유저 id로 커넥터에 접속해서 캐쉬정보 가져오기
    $sql6 = '';
    if ($isnick == 'N') {
        $sql6 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE SEQ_USER=:SEQ_USER";
        $prepare6 = $db->prepare($sql6);
        $prepare6->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
    } else {
        $sql6 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE SEQ_ANONY=:SEQ_ANONY";
        $prepare6 = $db->prepare($sql6);
        $prepare6->bindValue(':SEQ_ANONY', $userseq, PDO::PARAM_STR);
    }
    $prepare6->execute();
    $usercash = $prepare6->fetch(PDO::FETCH_ASSOC);
    $usercash = $usercash['CASH_POINT'];
    //나이구하기
    $birthday = date("Y", strtotime($userbirth)); //생년월일
    $nowday = date('Y'); //현재날짜
    $age = floor($nowday - $birthday); //만나이

    if ($price > $usercash) {
        echo '{"buy":"f","reason":"not enough cash"}';
        exit;
    } else if ($age < 19) {
        echo '{"buy":"f","reason":"age registration"}';
        exit;
    }
    //샀는지 확인
    $sql1 = "SELECT BUY_DATE FROM publixher.TBL_BUY_LIST WHERE (SEQ_USER=:SEQ_USER AND SEQ_CONTENT=:SEQ_CONTENT)";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
    $prepare1->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
    $prepare1->execute();
    $result = $prepare1->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        //안샀으면
        $sql2 = "INSERT INTO publixher.TBL_BUY_LIST(SEQ_USER,SEQ_CONTENT) VALUES(:SEQ_USER,:SEQ_CONTENT);";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
        $prepare2->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
        $prepare2->execute();

        $sql3 = "UPDATE publixher.TBL_CONTENT SET SALE=SALE+1 WHERE SEQ=:SEQ;";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':SEQ', $seq, PDO::PARAM_STR);
        $prepare3->execute();

        //알람처리
        $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(SEQ_CONTENT,SEQ_TARGET,ACT,SEQ_ACTOR) VALUES(:SEQ_CONTENT,:SEQ_TARGET,1,:SEQ_ACTOR)";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_TARGET', $writer, PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_ACTOR', $userseq, PDO::PARAM_STR);
        $prepare4->execute();
        echo '{"buy":"t"}';
        exit;
    }
    echo '{"buy":"f","reason":"already bought"}';
    exit;
} elseif ($act == 'more') {
    //링크를 타고 온게 아니라 진짜로 샀는지 확인
    $seq = $_GET['seq'];
    $userseq = $_GET['userseq'];
    //유료글인지 무료글인지 확인
    $sql = "SELECT FOR_SALE,SEQ_WRITER FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ', $seq, PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetch(PDO::FETCH_ASSOC);
    //유료글일때
    if ($result['FOR_SALE'] == "Y") {
        //자기 글일땐 허용
        if ($result['SEQ_WRITER'] == $userseq) {
            $sql2 = "SELECT BODY FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':SEQ', $seq, PDO::PARAM_STR);
            $prepare2->execute();
            $result = $prepare2->fetch(PDO::FETCH_ASSOC);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {
            //남의글일땐 샀는지 확인
            $sql1 = "SELECT BUY_DATE FROM publixher.TBL_BUY_LIST WHERE (SEQ_USER=:SEQ_USER AND SEQ_CONTENT=:SEQ_CONTENT)";
            $prepare1 = $db->prepare($sql1);
            $prepare1->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
            $prepare1->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
            $prepare1->execute();
            $result = $prepare1->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $sql2 = "SELECT BODY FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ";
                $prepare2 = $db->prepare($sql2);
                $prepare2->bindValue(':SEQ', $seq, PDO::PARAM_STR);
                $prepare2->execute();
                $result = $prepare2->fetch(PDO::FETCH_ASSOC);
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            } else {
                echo '구매먼저 해주세요!';
            }
        }
    } else {
        //무료글일때
        $sql2 = "SELECT BODY FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ', $seq, PDO::PARAM_STR);
        $prepare2->execute();
        $result = $prepare2->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
} elseif ($act == 'del') {
    $userinfo = $_SESSION['user'];
    $seq = $_POST['seq'];
    $userseq = $userinfo->getSEQ();
    $sql1 = "SELECT SEQ_WRITER FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ', $seq, PDO::PARAM_STR);
    $prepare1->execute();
    $result1 = $prepare1->fetch(PDO::FETCH_ASSOC);
    if ($result1['SEQ_WRITER'] == $userseq) {
        //폴더 시퀀스 찾기
        $sql4 = "SELECT FOLDER FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':SEQ', $seq, PDO::PARAM_STR);
        $prepare4->execute();
        $folderid = $prepare4->fetch(PDO::FETCH_ASSOC);
        //컨텐츠에서 지워진걸로 처리
        $sql2 = "UPDATE publixher.TBL_CONTENT SET DEL='Y',FOLDER=NULL WHERE SEQ=:SEQ";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ', $seq, PDO::PARAM_STR);
        $prepare2->execute();
        //폴더에서 삭제
        $sql3 = "UPDATE publixher.TBL_FORDER SET CONTENT_NUM=CONTENT_NUM-1 WHERE SEQ=:SEQ";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':SEQ', $folderid['FOLDER'], PDO::PARAM_STR);
        $prepare3->execute();
        //판매목록에서 삭제
        $sql5 = "DELETE FROM publixher.TBL_SELL_LIST WHERE SEQ_CONTENT=:SEQ_CONTENT";
        $prepare5 = $db->prepare($sql5);
        $prepare5->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
        $prepare5->execute();
        echo '{"result":"Y"}';
    } else {
        echo '작성자만 삭제할 수 있습니다';
    }
} elseif ($act == 'top') {
    //한번 확인해주고
    $sql1 = "UPDATE publixher.TBL_USER SET TOP_CONTENT=:TOP_CONTENT WHERE SEQ=:SEQ";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':TOP_CONTENT', $_POST['seq'], PDO::PARAM_STR);
    $prepare1->bindValue(':SEQ', $_POST['mid'], PDO::PARAM_STR);
    $prepare1->execute();
    echo '{"result":"Y"}';
} elseif ($act == 'repknock') {
    //댓글에 노크처리
    $userseq = $_POST['mid'];
    $seq = $_POST['seq'];
    $sql = "SELECT SEQ FROM publixher.TBL_CONTENT_REPLY_KNOCK WHERE (SEQ_USER=:SEQ_USER AND SEQ_REPLY=:SEQ_REPLY) LIMIT 1 ";
        $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
    $prepare->bindValue(':SEQ_REPLY', $seq, PDO::PARAM_STR);
    $prepare->execute();
    $knocked = $prepare->fetchColumn();
    if (!$knocked) {
        //노크처리
        $sql1 = "INSERT INTO publixher.TBL_CONTENT_REPLY_KNOCK(SEQ_USER,SEQ_REPLY,SEQ_CONTENT) VALUES(:SEQ_USER,:SEQ_REPLY,:SEQ_CONTENT);";
        $sql2 = "UPDATE publixher.TBL_CONTENT_REPLY SET KNOCK=KNOCK+1 WHERE SEQ=:SEQ;";
        $sql3 = "SELECT SEQ_USER,KNOCK,SEQ_CONTENT FROM publixher.TBL_CONTENT_REPLY WHERE SEQ=:SEQ;";
        //insert문
        $prepare1 = $db->prepare($sql1);
        $prepare1->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
        $prepare1->bindValue(':SEQ_REPLY', $seq, PDO::PARAM_STR);
        $prepare1->bindValue(':SEQ_CONTENT', $_POST['thisitemID'], PDO::PARAM_STR);
        $prepare1->execute();
        //update문
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ', $seq, PDO::PARAM_STR);
        $prepare2->execute();
        //select문
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':SEQ', $seq, PDO::PARAM_STR);
        $prepare3->execute();
        $target = $prepare3->fetch(PDO::FETCH_ASSOC);

        //알람처리
        $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(SEQ_REPLY,SEQ_TARGET,ACT,SEQ_ACTOR,SEQ_CONTENT) VALUES(:SEQ_REPLY,:SEQ_TARGET,6,:SEQ_ACTOR,:SEQ_CONTENT)";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':SEQ_REPLY', $seq, PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_TARGET', $target['SEQ_USER'], PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_ACTOR', $userseq, PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_CONTENT', $target['SEQ_CONTENT'], PDO::PARAM_STR);
        $prepare4->execute();
        echo '{"knock":"' . $target['KNOCK'] . '"}';
    } else {
        echo '{"result":"N","reason":"already"}';
    }
}elseif($act=='sub_comment' or $act=='more_sub_comment'){
    require_once '../../lib/passing_time.php';
    $seq = $_GET['seq'];
    $userseq=$_GET['userseq'];
    $repseq=$_GET['repseq'];
    function getWriter($result, $db)
    {
        for ($i = 0; $i < count($result); $i++) {   //각 댓글별로 쓴사람과 사진 가져오기
            $sql2 = "SELECT USER_NAME,PIC FROM publixher.TBL_USER WHERE SEQ=:SEQ";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':SEQ', $result[$i]['SEQ_USER'], PDO::PARAM_STR);
            $prepare2->execute();
            $fetch = $prepare2->fetch(PDO::FETCH_ASSOC);
            $result[$i]['USER_NAME'] = $fetch['USER_NAME'];
            $result[$i]['REPLY_DATE'] = passing_time($result[$i]['REPLY_DATE']);
            $result[$i]['PIC'] = $fetch['PIC'];
        }
        return $result;
    }
    function getTime($db, $repseq,$index)
    {
        $timerep_sql = "SELECT * FROM publixher.TBL_CONTENT_SUB_REPLY WHERE SEQ_REPLY=:SEQ_REPLY ORDER BY SEQ DESC LIMIT :INDEX,5";
        $prepare1 = $db->prepare($timerep_sql);
        $prepare1->bindValue(':SEQ_REPLY', $repseq);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $result = getWriter($result, $db);
            $result['sort'] = "time";
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }
    if($act=='sub_comment'){
        $result = getTime($db,$repseq,0);
        if(!$result){
            echo '{"result":"NO"}';
        }
    }else{
        $result=getTime($db,$repseq,$_GET['index']);
        if(!$result){
            echo '{"result":"NO"}';
        }
    }

}elseif ($act == 'commentreg_sub') {
    $userseq = $_POST['userseq'];
    $seq = $_POST['seq'];
    $comment = $_POST['comment'];
    $repseq=$_POST['repseq'];
    $sql1 = "INSERT INTO publixher.TBL_CONTENT_SUB_REPLY(SEQ_USER,SEQ_CONTENT,REPLY,SEQ_REPLY) VALUES(:SEQ_USER,:SEQ_CONTENT,:REPLY,:SEQ_REPLY);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE SEQ=:SEQ;";
    $sql3 = "SELECT SUB_REPLY,SEQ_USER FROM publixher.TBL_CONTENT_REPLY WHERE SEQ=:SEQ;";
    $sql4 = "UPDATE publixher.TBL_CONTENT_REPLY SET SUB_REPLY=SUB_REPLY+1 WHERE SEQ=:SEQ;";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
    $prepare1->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY', $comment, PDO::PARAM_STR);
    $prepare1->bindValue(':SEQ_REPLY', $repseq, PDO::PARAM_STR);
    $prepare1->execute();

    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':SEQ', $seq, PDO::PARAM_STR);
    $prepare2->execute();

    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':SEQ', $repseq, PDO::PARAM_STR);
    $prepare4->execute();

    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':SEQ', $repseq, PDO::PARAM_STR);
    $prepare3->execute();
    $result = $prepare3->fetch(PDO::FETCH_ASSOC);

    //알람처리
    $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(SEQ_CONTENT,SEQ_TARGET,ACT,SEQ_ACTOR) VALUES(:SEQ_CONTENT,:SEQ_TARGET,7,:SEQ_ACTOR)";
    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':SEQ_CONTENT', $seq, PDO::PARAM_STR);
    $prepare4->bindValue(':SEQ_TARGET', $result['SEQ_USER'], PDO::PARAM_STR);
    $prepare4->bindValue(':SEQ_ACTOR', $userseq, PDO::PARAM_STR);
    $prepare4->execute();
    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $result;
}
?>