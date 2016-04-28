<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
session_start();
if(!isset($_SESSION['user'])) include_once "../../lib/loginchk.php";
$userinfo = $_SESSION['user'];
$userID = $userinfo->getID();
$action = $_GET['action'];
$nowpage=$_GET['nowpage']*20;
if($action=='loadpin'){
    $selPin="SELECT PIN.ID_CONTENT,PIN.MODIFIED,PIN.KNOCK,PIN.REPLY,PIN.LAST_UPDATE,LEFT(CONT.BODY_TEXT,20) AS BODY FROM publixher.TBL_PIN_LIST AS PIN INNER JOIN publixher.TBL_CONTENT AS CONT ON PIN.ID_CONTENT=CONT.ID WHERE ID_USER=:ID_USER ORDER BY LAST_UPDATE DESC LIMIT :PAGE,20";
    $selpre=$db->prepare($selPin);
    $selpre->bindValue(':ID_USER',$userID);
    $selpre->bindValue(':PAGE',$nowpage);
    $selpre->execute();
    $result=$selpre->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
    $upPin="UPDATE publixher.TBL_PIN_LIST SET MODIFIED=0,KNOCK=0,REPLY=0,LAST_CHECK=NOW() WHERE ID_USER=:ID_USER";
    $uppre = $db->prepare($upPin);
    $uppre->bindValue(':ID_USER', $userID);
    $uppre->execute();
}
?>