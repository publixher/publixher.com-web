<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$action = $_POST['action'];
if ($action == 'request') {
    //친구신청 되 있는지 확인
    $targetID = $_POST['targetID'];
    $myID = $_POST['myID'];
    $sql1 = "SELECT SEQ FROM publixher.TBL_FRIENDS WHERE (ID_FRIEND=:ID_FRIEND AND ID_USER=:ID_USER)";
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
        echo '{"result":1}';
    } else {
        $result=array('status'=>array('code'=>'-2','msg'=>'already'));
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
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
    echo '{"result":1}';
} elseif ($action == 'friendno') {
    $requestid = $_POST['requestid'];
    $sql1 = "DELETE FROM publixher.TBL_FRIENDS WHERE SEQ=:SEQ";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ', $requestid, PDO::PARAM_STR);
    $prepare1->execute();
    echo '{"result":1}';
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
    echo '{"result":1}';
} elseif ($action == 'subscribe') {
    $targetID = $_POST['targetID'];
    $userID = $_POST['myID'];
    $sql3 = "INSERT INTO publixher.TBL_FOLLOW(ID_MASTER,ID_SLAVE) VALUES(:ID_MASTER,:ID_SLAVE)";
    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID_MASTER', $targetID);
    $prepare3->bindValue(':ID_SLAVE', $userID);
    $prepare3->execute();
    echo '{"result":1}';
} elseif ($action == 'dis_subscribe') {
    $targetID = $_POST['targetID'];
    $userID = $_POST['myID'];
    $sql3 = "DELETE FROM publixher.TBL_FOLLOW WHERE (ID_MASTER=:ID_MASTER AND ID_SLAVE=:ID_SLAVE)";
    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID_MASTER', $targetID);
    $prepare3->bindValue(':ID_SLAVE', $userID);
    $prepare3->execute();
    echo '{"result":1}';
}
?>
