<?php
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
$id=$_GET['id'];
$result=array();
$sql_up="UPDATE publixher.TBL_USER SET LEVEL=1 WHERE ID=:ID";
$prepare = $db->prepare($sql_up);
$prepare->bindValue(':ID',$id);
$prepare->execute();
$sql_sel="SELECT * FROM publixher.TBL_USER WHERE ID=:ID";
$prepare = $db->prepare($sql_sel);
$prepare->bindValue(':ID',$id);
$prepare->execute();
$result=$prepare->fetch(PDO::FETCH_ASSOC);
//유저객체에 할당
$_SESSION['user'] = $result;
//세션토큰 생성(CSRF등 대책)
if(!isset($_SESSION['token'])){
    $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
}
?>
<meta http-equiv='refresh' content='0;url=/'>