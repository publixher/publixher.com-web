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
    $selPin="SELECT ID_CONTENT,MODIFIED,KNOCK,REPLY,LAST_UPDATE,BODY,ID_WRITER,REPLACE(WRITER_PIC,'profile','crop50') AS WRITER_PIC FROM publixher.TBL_PIN_LIST WHERE ID_USER=:ID_USER ORDER BY LAST_UPDATE DESC LIMIT :PAGE,20";
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