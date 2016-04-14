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
    $targetID = $_POST['targetID'];
    $myID = $_POST['myID'];
    $sql1 = "SELECT ID FROM publixher.TBL_FRIENDS WHERE (ID_FRIEND=:ID_FRIEND AND ID_USER=:ID_USER)";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID_FRIEND', $targetID, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_USER', $myID, PDO::PARAM_STR);
    $prepare1->execute();
    $requestcheck = $prepare1->fetch(PDO::FETCH_ASSOC);
    if (!$requestcheck) {
        //친구신청 안되있으면 친구신청하기
        $sql = "INSERT INTO publixher.TBL_FRIENDS(ID_FRIEND,ID_USER) VALUES(:ID_FRIEND,:ID_USER)";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID_FRIEND', $targetID, PDO::PARAM_STR);
        $prepare->bindValue(':ID_USER', $myID, PDO::PARAM_STR);
        $prepare->execute();
        //알람처리
        $sql4 = "INSERT INTO publixher.TBL_CONTENT_NOTI(ID_TARGET,ACT,ID_ACTOR) VALUES(:ID_TARGET,2,:ID_ACTOR)";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':ID_TARGET', $targetID, PDO::PARAM_STR);
        $prepare4->bindValue(':ID_ACTOR', $myID, PDO::PARAM_STR);
        $prepare4->execute();
        echo '{"result":"Y"}';
    } else {
        echo '{"result":"N","reason":"already requested"}';
    }
} elseif ($action == 'friendok') {
    //친구신청에 ok
    $targetID = $_POST['targetID'];
    $requestid = $_POST['requestid'];
    $myID = $_POST['myID'];
    $sql1 = "UPDATE publixher.TBL_FRIENDS SET ALLOWED='Y' WHERE SEQ=:SEQ";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ', $requestid, PDO::PARAM_STR);
    $prepare1->execute();

    $sql2 = "INSERT INTO publixher.TBL_FRIENDS(ID_FRIEND,ID_USER,ALLOWED) VALUES(:ID_FRIEND,:ID_USER,'Y')";
    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':ID_FRIEND', $targetID, PDO::PARAM_STR);
    $prepare2->bindValue(':ID_USER', $myID, PDO::PARAM_STR);
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
    $targetID = $_POST['targetID'];
    $userID = $_POST['myID'];
    //친구목록에는 양방향으로 신청이 되어 있으니 두개 다 지워야한다
    $sql2 = "DELETE FROM publixher.TBL_FRIENDS WHERE (ID_FRIEND=:ID_FRIEND AND ID_USER=:ID_USER) OR (ID_FRIEND=:ID_FRIEND2 AND ID_USER=:ID_USER2)";
    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':ID_FRIEND', $targetID, PDO::PARAM_STR);
    $prepare2->bindValue(':ID_USER2', $targetID, PDO::PARAM_STR);
    $prepare2->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare2->bindValue(':ID_FRIEND2', $userID, PDO::PARAM_STR);
    $prepare2->execute();
    echo '{"result":"Y"}';
} elseif ($action == 'subscribe') {
    $targetID = $_POST['targetID'];
    $userID = $_POST['userID'];
    $sql3 = "INSERT INTO publixher.TBL_FOLLOW(ID_MASTER,ID_SLAVE) VALUES(:ID_MASTER,:ID_SLAVE)";
    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID_MASTER', $targetID);
    $prepare3->bindValue(':ID_SLAVE', $userID);
    $prepare3->execute();
    echo '{"result":"Y"}';
} elseif ($action == 'dis_subscribe') {
    $targetID = $_POST['targetID'];
    $userID = $_POST['userID'];
    $sql3 = "DELETE FROM publixher.TBL_FOLLOW WHERE (ID_MASTER=:ID_MASTER AND ID_SLAVE=:ID_SLAVE)";
    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID_MASTER', $targetID);
    $prepare3->bindValue(':ID_SLAVE', $userID);
    $prepare3->execute();
    echo '{"result":"Y"}';
}
?>
