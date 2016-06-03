<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$userID = $_GET['userID'];
$action = $_GET['action'];
if ($action == 'point') {
    $sql1 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE ID_USER=:ID_TARGET OR ID_ANONY=:ID_ANONY";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID_TARGET', $userID, PDO::PARAM_STR);
    $prepare1->bindValue(':ID_ANONY', $userID, PDO::PARAM_STR);
    $prepare1->execute();
    $cash = $prepare1->fetch(PDO::FETCH_ASSOC);
    if (!$cash) {
        echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(array('result' => $cash, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
} elseif ($action == 'auth') {
    $w = "SELECT WRITEAUTH,EXPAUTH FROM publixher.TBL_USER WHERE ID=:ID";
    $p = $db->prepare($w);
    $p->bindValue(':ID', $userID);
    $p->execute();
    $auth = $p->fetch(PDO::FETCH_ASSOC);
    if (!$auth) {
        echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(array('result' => $auth, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
} elseif ($action == 'friendList') {
    $sql3 = "SELECT FRIEND.ID_FRIEND AS ID,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC
FROM publixher.TBL_FRIENDS AS FRIEND
  INNER JOIN publixher.TBL_USER AS USER ON USER.ID=FRIEND.ID_FRIEND
WHERE ID_USER = :ID_USER AND ALLOWED = 'Y'";
    $prepare3 = $db->prepare($sql3);
    $prepare3->bindValue(':ID_USER', $userID);
    $prepare3->execute();
    $friends = $prepare3->fetchAll(PDO::FETCH_ASSOC);
    if (!$friends) {
        echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode(array('result' => $friends, 'status' => array('code' => 1)), JSON_UNESCAPED_UNICODE);
}
?>