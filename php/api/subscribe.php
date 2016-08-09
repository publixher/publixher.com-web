<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
session_start();
$action=$_REQUEST['action'];
if($action=='check'){
    $chk="UPDATE publixher.TBL_FOLLOW SET LAST_CHECK=NOW() WHERE (ID_MASTER=:ID_MASTER AND ID_SLAVE=:ID_SLAVE)";
    $chkp=$db->prepare($chk);
    $chkp->bindValue(':ID_MASTER',$_REQUEST['mu']);
    $chkp->bindValue(':ID_SLAVE',$_REQUEST['userID']);
    $chkp->execute();
    echo '{"status":1}';
}
?>