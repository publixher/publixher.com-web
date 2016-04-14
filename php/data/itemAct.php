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
    exit('부정한 조작이 감지되었습니다. case1 \n$_POST["token"] :'.$_POST['token'].' \n $_GET["token"] :'.$_GET['token'].'$_SESSION :'.$_SESSION);
} elseif ($_POST['token'] != $_SESSION['token'] AND $_GET['token'] != $_SESSION['token']) {
    exit('부정한 조작이 감지되었습니다. case2 \n$_POST["token"] :'.$_POST['token'].' \n $_GET["token"] :'.$_GET['token'].'$_SESSION :'.$_SESSION);
}
//세션탈취 검사
if (!isset($_POST['age']) AND !isset($_GET['age'])) {
    exit('부정한 조작이 감지되었습니다. case3 \n$_POST["age"] :'.$_POST['age'].' \n $_GET["age"] :'.$_GET['age'].'$_SESSION :'.$_SESSION);
} elseif ($_POST['age'] != $_SESSION['age'] AND $_GET['age'] != $_SESSION['age']) {
    exit('부정한 조작이 감지되었습니다. case4 \n$_POST["age"] :'.$_POST['age'].' \n $_GET["age"] :'.$_GET['age'].'$_SESSION :'.$_SESSION);
}

$act = $_POST['action'];
if (!$act) {
    $act = $_GET['action'];
}
//액션에 따라 동작이 달라짐 knock,comment,commentreg,share,buy
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
        echo '{"result":"N","reason":"already"}';
    }
} elseif ($act == 'comment' OR $act == 'more_comment') {  //처음 불러오는거나 이상 불러오는거 둘다 이 분기로 들어가기
    require_once '../../lib/passing_time.php';
    $ID = $_GET['ID'];
    $userID=$_GET['userID'];
    function getWriter($result, $db)
    {
        for ($i = 0; $i < count($result); $i++) {   //각 댓글별로 쓴사람과 사진 가져오기
            $sql2 = "SELECT USER_NAME,PIC FROM publixher.TBL_USER WHERE ID=:ID";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID', $result[$i]['ID_USER'], PDO::PARAM_STR);
            $prepare2->execute();
            $fetch = $prepare2->fetch(PDO::FETCH_ASSOC);
            $result[$i]['USER_NAME'] = $fetch['USER_NAME'];
            $result[$i]['REPLY_DATE'] = passing_time($result[$i]['REPLY_DATE']);
            $result[$i]['PIC'] = $fetch['PIC'];
        }
        return $result;
    }
    function getBest($db, $ID,$index)
    {
        $bestrep_sql = "SELECT \n"  //베스트댓글5개
            . "	* \n"
            . "FROM \n"
            . "	publixher.TBL_CONTENT_REPLY REPLY \n"
            . "WHERE \n"
            . "	KNOCK + SUB_REPLY >= 10 \n"
            . "	AND ID_CONTENT = :ID_CONTENT \n"
            . "ORDER BY \n"
            . "	KNOCK + SUB_REPLY DESC \n"
            . "LIMIT \n"
            . ":INDEX, 6";
        $prepare1 = $db->prepare($bestrep_sql);
        $prepare1->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
        $prepare1->bindValue(':INDEX', $index, PDO::PARAM_STR);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {    //베스트 있을때
            $result = getWriter($result, $db);
            $result['sort'] = "best";
            if($result[5]) {
                $result['more']='1';
                unset($result[5]);
            }else{
                $result['more']='0';
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }
    function getTime($db, $ID,$index)
    {
        $timerep_sql = "SELECT * FROM publixher.TBL_CONTENT_REPLY WHERE ID_CONTENT=:ID_CONTENT ORDER BY SEQ DESC LIMIT :INDEX,6";
        $prepare1 = $db->prepare($timerep_sql);
        $prepare1->bindValue(':ID_CONTENT', $ID);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $result = getWriter($result, $db);
            $result['sort'] = "time";
            if($result[5]) {
                $result['more']='1';
                unset($result[5]);
            }else{
                $result['more']='0';
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }
    function getFrie($db,$ID,$userID,$index){
        $friend_sql = "SELECT \n"
            . "	REPLY.* \n"
            . "FROM \n"
            . "	publixher.TBL_CONTENT_REPLY REPLY STRAIGHT_JOIN publixher.TBL_FRIENDS FRIEND ON REPLY.ID_USER = FRIEND.ID_FRIEND \n"
            . "WHERE \n"
            . "	FRIEND.ID_USER = :ID_USER \n"
            . "	AND REPLY.ID_CONTENT = :ID_CONTENT \n"
            . "ORDER BY \n"
            . "	REPLY.ID DESC \n"
            . "LIMIT \n"
            . "	:INDEX, 6";
        $prepare1 = $db->prepare($friend_sql);
        $prepare1->bindValue(':ID_CONTENT', $ID);
        $prepare1->bindValue(':ID_USER', $userID);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if($result){
            $result = getWriter($result, $db);
            $result['sort']="friend";
            if($result[5]) {
                $result['more']='1';
                unset($result[5]);
            }else{
                $result['more']='0';
            }
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
            $result = getBest($db, $ID,0);
            if (!$result) {
                $result = getTime($db, $ID,0);
                if (!$result) {
                    echo '{"result":"NO"}';
                }
            }
        } elseif ($sort== 'best') { //처음으로 로딩한게 아니라 각 탭을 보는거면 sort로 구분한다
            $result=getBest($db,$ID,0);
            if(!$result){
                echo '{"result":"NO"}';
            }
        }elseif($sort=='time'){
            $result=getTime($db,$ID,0);
            if(!$result){
                echo '{"result":"NO"}';
            }
        }elseif($sort=='frie'){
            $result=getFrie($db,$ID,$userID,0);
            if(!$result){
                echo '{"result":"NO"}';
            }
        }
    } elseif ($act == 'more_comment') {
        $sort = $_GET['sort'];
        $index = $_GET['index'];
        if ($sort == 'best') {
            $result=getBest($db,$ID,$index);
            if(!$result) echo '{"result":"NO"}';
        } elseif ($sort == 'time') {
            $result=getTime($db,$ID,$index);
            if(!$result) echo '{"result":"NO"}';
        } elseif ($sort == 'frie') {
            $result=getFrie($db,$ID,$userID,$index);
            if(!$result) echo '{"result":"NO"}';
        }
    }
} elseif ($act == 'commentreg') {
    $userID = $_POST['userID'];
    $ID = $_POST['ID'];
    $comment = $_POST['comment'];
    $sql1 = "INSERT INTO publixher.TBL_CONTENT_REPLY(ID_USER,ID_CONTENT,REPLY) VALUES(:ID_USER,:ID_CONTENT,:REPLY);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE ID=:ID;";
    $sql3 = "SELECT COMMENT,ID_WRITER FROM publixher.TBL_CONTENT WHERE ID=:ID;";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID_USER', $_POST['userID'], PDO::PARAM_STR);
    $prepare1->bindValue(':ID_CONTENT', $_POST['ID'], PDO::PARAM_STR);
    $prepare1->bindValue(':REPLY', $_POST['comment'], PDO::PARAM_STR);
    $prepare1->execute();

    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':ID', $_POST['ID'], PDO::PARAM_STR);
    $prepare2->execute();

    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID', $_POST['ID'], PDO::PARAM_STR);
    $prepare3->execute();
    $result = $prepare3->fetch(PDO::FETCH_ASSOC);

    //알람처리
    $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_CONTENT,ID_TARGET,ACT,ID_ACTOR) VALUES(:ID_CONTENT,:ID_TARGET,3,:ID_ACTOR)";
    $prepare4 = $db->prepare($sql4);
    $prepare4->bindValue(':ID_CONTENT', $ID, PDO::PARAM_STR);
    $prepare4->bindValue(':ID_TARGET', $result['ID_WRITER'], PDO::PARAM_STR);
    $prepare4->bindValue(':ID_ACTOR', $userID, PDO::PARAM_STR);
    $prepare4->execute();
    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
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
    $sql7 = "SELECT PRICE,ID_WRITER FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $prepare7 = $db->prepare($sql7);
    $prepare7->bindValue(':ID', $ID, PDO::PARAM_STR);
    $prepare7->execute();
    $result = $prepare7->fetch(PDO::FETCH_ASSOC);
    $price = $result['PRICE'];
    $writer = $result['ID_WRITER'];
    //유저 id로 커넥터에 접속해서 캐쉬정보 가져오기
    if ($isnick == 'N') {
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
        echo '{"buy":"f","reason":"not enough cash"}';
        exit;
    } else if ($age < 19) {
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
        echo '{"buy":"t"}';

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
        exit;
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
                echo '구매먼저 해주세요!';
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
        echo '{"result":"Y"}';
    } else {
        echo '작성자만 삭제할 수 있습니다';
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
    $sql = "SELECT ID FROM publixher.TBL_CONTENT_REPLY_KNOCK WHERE (ID_USER=:ID_USER AND ID_REPLY=:ID_REPLY) LIMIT 1 ";
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
        echo '{"knock":"' . $target['KNOCK'] . '"}';
    } else {
        echo '{"result":"N","reason":"already"}';
    }
}elseif($act=='sub_comment' or $act=='more_sub_comment'){
    require_once '../../lib/passing_time.php';
    $ID = $_GET['ID'];
    $userID=$_GET['userID'];
    $repID=$_GET['repID'];
    function getWriter($result, $db)
    {
        for ($i = 0; $i < count($result); $i++) {   //각 댓글별로 쓴사람과 사진 가져오기
            $sql2 = "SELECT USER_NAME,PIC FROM publixher.TBL_USER WHERE ID=:ID";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID', $result[$i]['ID_USER'], PDO::PARAM_STR);
            $prepare2->execute();
            $fetch = $prepare2->fetch(PDO::FETCH_ASSOC);
            $result[$i]['USER_NAME'] = $fetch['USER_NAME'];
            $result[$i]['REPLY_DATE'] = passing_time($result[$i]['REPLY_DATE']);
            $result[$i]['PIC'] = $fetch['PIC'];
        }
        return $result;
    }
    function getTime($db, $repID,$index)
    {
        $timerep_sql = "SELECT * FROM publixher.TBL_CONTENT_SUB_REPLY WHERE ID_REPLY=:ID_REPLY ORDER BY SEQ DESC LIMIT :INDEX,6";
        $prepare1 = $db->prepare($timerep_sql);
        $prepare1->bindValue(':ID_REPLY', $repID);
        $prepare1->bindValue(':INDEX', $index);
        $prepare1->execute();
        $result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $result = getWriter($result, $db);
            $result['sort'] = "time";
            if($result[5]) {
                $result['more']='1';
                unset($result[5]);
            }else{
                $result['more']='0';
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return true;
        } else {
            return false;
        }
    }
    if($act=='sub_comment'){
        $result = getTime($db,$repID,0);
        if(!$result){
            echo '{"result":"NO"}';
        }
    }else{
        $result=getTime($db,$repID,$_GET['index']);
        if(!$result){
            echo '{"result":"NO"}';
        }
    }

}elseif ($act == 'commentreg_sub') {
    $userID = $_POST['userID'];
    $ID = $_POST['ID'];
    $comment = $_POST['comment'];
    $repID=$_POST['repID'];
    $sql1 = "INSERT INTO publixher.TBL_CONTENT_SUB_REPLY(ID_USER,ID_CONTENT,REPLY,ID_REPLY) VALUES(:ID_USER,:ID_CONTENT,:REPLY,:ID_REPLY);";
    $sql2 = "UPDATE publixher.TBL_CONTENT SET COMMENT=COMMENT+1 WHERE ID=:ID;";
    $sql3 = "SELECT SUB_REPLY,ID_USER FROM publixher.TBL_CONTENT_REPLY WHERE ID=:ID;";
    $sql4 = "UPDATE publixher.TBL_CONTENT_REPLY SET SUB_REPLY=SUB_REPLY+1 WHERE ID=:ID;";
    $prepare1 = $db->prepare($sql1);
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
    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $result;
}
?>