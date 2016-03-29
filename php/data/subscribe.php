<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
session_start();
$action=$_GET['action'];
if($action=='check'){
    $chk="UPDATE publixher.TBL_FOLLOW SET LAST_CHECK=NOW() WHERE (SEQ_MASTER=:SEQ_MASTER AND SEQ_SLAVE=:SEQ_SLAVE)";
    $chkp=$db->prepare($chk);
    $chkp->bindValue(':SEQ_MASTER',$_GET['mu']);
    $chkp->bindValue(':SEQ_SLAVE',$_SESSION['user']->getSEQ());
    $chkp->execute();
}
?>