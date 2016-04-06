<?php
header("Content-Type:application/json");
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) OR $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    exit('부정한 호출입니다.');
}
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
require_once '../../conf/database_conf.php';
$action = $_POST['action'];
if ($action == 'request') {
    //친구신청 되 있는지 확인
    $targetseq = $_POST['targetseq'];
    $myseq = $_POST['myseq'];
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
        $prepare4->bindValue(':SEQ_TARGET', $targetseq, PDO::PARAM_STR);
        $prepare4->bindValue(':SEQ_ACTOR', $myseq, PDO::PARAM_STR);
        $prepare4->execute();
        echo '{"result":"Y"}';
    } else {
        echo '{"result":"N","reason":"already requested"}';
    }
} elseif ($action == 'friendok') {
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
} elseif ($action == 'friendno') {
    $requestid = $_POST['requestid'];
    $sql1 = "DELETE FROM publixher.TBL_FRIENDS WHERE SEQ=:SEQ";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ', $requestid, PDO::PARAM_STR);
    $prepare1->execute();
    echo '{"result":"Y"}';
} elseif ($action == 'endrelation') {
    $targetseq = $_POST['targetseq'];
    $userseq = $_POST['myseq'];
    //친구목록에는 양방향으로 신청이 되어 있으니 두개 다 지워야한다
    $sql2 = "DELETE FROM publixher.TBL_FRIENDS WHERE (SEQ_FRIEND=:SEQ_FRIEND AND SEQ_USER=:SEQ_USER) OR (SEQ_FRIEND=:SEQ_FRIEND2 AND SEQ_USER=:SEQ_USER2)";
    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':SEQ_FRIEND', $targetseq, PDO::PARAM_STR);
    $prepare2->bindValue(':SEQ_USER2', $targetseq, PDO::PARAM_STR);
    $prepare2->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
    $prepare2->bindValue(':SEQ_FRIEND2', $userseq, PDO::PARAM_STR);
    $prepare2->execute();
    echo '{"result":"Y"}';
} elseif ($action == 'subscribe') {
    $targetseq = $_POST['targetseq'];
    $userseq = $_POST['userseq'];
    $sql3 = "INSERT INTO publixher.TBL_FOLLOW(SEQ_MASTER,SEQ_SLAVE) VALUES(:SEQ_MASTER,:SEQ_SLAVE)";
    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':SEQ_MASTER', $targetseq);
    $prepare3->bindValue(':SEQ_SLAVE', $userseq);
    $prepare3->execute();
    echo '{"result":"Y"}';
} elseif ($action == 'dis_subscribe') {
    $targetseq = $_POST['targetseq'];
    $userseq = $_POST['userseq'];
    $sql3 = "DELETE FROM publixher.TBL_FOLLOW WHERE (SEQ_MASTER=:SEQ_MASTER AND SEQ_SLAVE=:SEQ_SLAVE)";
    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':SEQ_MASTER', $targetseq);
    $prepare3->bindValue(':SEQ_SLAVE', $userseq);
    $prepare3->execute();
    echo '{"result":"Y"}';
}
?>
