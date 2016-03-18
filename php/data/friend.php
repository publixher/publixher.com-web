<?php
header("Content-Type:application/json");
if(! isset($_SERVER['HTTP_X_REQUESTED_WITH']) OR $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
    exit('부정한 호출입니다.');
}
require_once'../../conf/database_conf.php';
if ($_POST['action'] == 'request') {
    //친구신청 되 있는지 확인
    $targetseq = $_POST['targetseq'];
    $myseq = $_POST['myseq'];
    //토큰검사
    session_start();
    //CSRF대책
    if(!isset($_POST['token'])){
        exit('부정한 조작이 감지되었습니다.');
    }elseif($_POST['token'] !=$_SESSION['token']){
        exit('부정한 조작이 감지되었습니다.');
    }
    //세션 탈취검사
    if(!isset($_POST['age'])){
        exit('부정한 조작이 감지되었습니다.');
    }elseif($_POST['age'] !=$_SESSION['age']){
        exit('부정한 조작이 감지되었습니다.');
    }
    $sql1 = "SELECT SEQ FROM publixher.TBL_FRIENDS WHERE (SEQ_FRIEND=:SEQ_FRIEND AND SEQ_USER=:SEQ_USER)";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ_FRIEND', $targetseq, PDO::PARAM_STR);
    $prepare1->bindValue(':SEQ_USER', $myseq, PDO::PARAM_STR);
    $prepare1->execute();
    $requestcheck = $prepare1->fetch(PDO::FETCH_ASSOC);
    if (!$requestcheck) {
        //친구신청 안되있으면 친구신청하기
        $sql = "INSERT INTO publixher.TBL_FRIENDS(SEQ_FRIEND,SEQ_USER) VALUES(:SEQ_FRIEND,:SEQ_USER)";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':SEQ_FRIEND', $targetseq, PDO::PARAM_STR);
        $prepare->bindValue(':SEQ_USER', $myseq, PDO::PARAM_STR);
        $prepare->execute();
        //알람처리
        $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(SEQ_TARGET,ACT,SEQ_ACTOR) VALUES(:SEQ_TARGET,2,:SEQ_ACTOR)";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':SEQ_TARGET',$targetseq,PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_ACTOR',$myseq,PDO::PARAM_STR);
        $prepare4->execute();
        echo '{"result":"Y"}';
    } else {
        echo '{"result":"N","reason":"already requested"}';
    }
} elseif ($_POST['action'] == 'friendok') {
    //친구신청에 ok
    $targetseq = $_POST['targetseq'];
    $requestid = $_POST['requestid'];
    $myseq = $_POST['myseq'];
    $sql1 = "UPDATE publixher.TBL_FRIENDS SET ALLOWED='Y' WHERE SEQ=:SEQ";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ', $requestid, PDO::PARAM_STR);
    $prepare1->execute();

    $sql2 = "INSERT INTO publixher.TBL_FRIENDS(SEQ_FRIEND,SEQ_USER,ALLOWED) VALUES(:SEQ_FRIEND,:SEQ_USER,'Y')";
    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':SEQ_FRIEND', $targetseq, PDO::PARAM_STR);
    $prepare2->bindValue(':SEQ_USER', $myseq, PDO::PARAM_STR);
    $prepare2->execute();
    echo '{"result":"Y"}';
} elseif ($_POST['action'] == 'friendno') {
    $requestid = $_POST['requestid'];
    $sql1 = "DELETE FROM publixher.TBL_FRIENDS WHERE SEQ=:SEQ";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ', $requestid, PDO::PARAM_STR);
    $prepare1->execute();
    echo '{"result":"Y"}';
}
?>
