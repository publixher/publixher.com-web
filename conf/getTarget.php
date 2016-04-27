<?php
require_once 'User.php';
require_once 'database_conf.php';
session_start();
//$userinfo는 현재 접속한 유저 targetid는 프로필 유
$userinfo = $_SESSION['user'];
$targetid = $_GET['id'];

$sql1="SELECT USER_NAME,SEX,BIRTH,REGION,H_SCHOOL,UNIV,PIC,JOIN_DATE,IS_NICK,IN_USE FROM publixher.TBL_USER WHERE ID=:ID";
$prepare1 = $db->prepare($sql1);
$prepare1 ->bindValue(':ID',$targetid,PDO::PARAM_STR);
$prepare1->execute();
$target=$prepare1->fetch(PDO::FETCH_ASSOC);
?>