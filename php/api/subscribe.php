<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
session_start();
$action=$_GET['action'];
if($action=='check'){
    $chk="UPDATE publixher.TBL_FOLLOW SET LAST_CHECK=NOW() WHERE (ID_MASTER=:ID_MASTER AND ID_SLAVE=:ID_SLAVE)";
    $chkp=$db->prepare($chk);
    $chkp->bindValue(':ID_MASTER',$_GET['mu']);
    $chkp->bindValue(':ID_SLAVE',$_GET['userID']);
    $chkp->execute();
}
?>