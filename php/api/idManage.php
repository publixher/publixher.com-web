<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
//토큰검사
$action = $_POST['action'];
if ($action == 'ban') {
    $managerID = $_POST['managerID'];
    $sql = "INSERT INTO publixher.TBL_BAN_LIST(ID_TARGET,ID_MANAGER,TIME) VALUES(:ID_TARGET,:ID_MANAGER,DATE_ADD(NOW(),INTERVAL :DAYS DAY))";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_TARGET', $_POST['target']);
    $prepare->bindValue(':ID_MANAGER', $managerID);
    $prepare->bindValue(':DAYS', $_POST['days']);
    $prepare->execute();
} elseif ($action == 'release') {
    $sql = 'UPDATE publixher.TBL_USER SET BAN=NULL WHERE ID=:ID';
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $_POST['target']);
    $prepare->execute();
}
?>