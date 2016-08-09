<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
//토큰검사
$action = $_REQUEST['action'];
if ($action == 'ban') {
    $managerID = $_REQUEST['managerID'];
    $sql = "INSERT INTO publixher.TBL_BAN_LIST(ID_TARGET,ID_MANAGER,TIME) VALUES(:ID_TARGET,:ID_MANAGER,DATE_ADD(NOW(),INTERVAL :DAYS DAY))";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_TARGET', $_REQUEST['target']);
    $prepare->bindValue(':ID_MANAGER', $managerID);
    $prepare->bindValue(':DAYS', $_REQUEST['days']);
    $prepare->execute();

    $sqlu = "UPDATE publixher.TBL_USER SET WRITEAUTH=0 WHERE ID=:ID_TARGET";
    $prepareu = $db->prepare($sqlu);
    $prepareu->bindValue(':ID_TARGET', $_REQUEST['target']);
    $prepareu->execute();
    echo '{"status":1}';
} elseif ($action == 'release') {
    $sql = 'UPDATE publixher.TBL_USER SET BAN=NULL WHERE ID=:ID';
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $_REQUEST['target']);
    $prepare->execute();
    echo '{"status":1}';
}
?>