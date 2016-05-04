<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$sql1 = "SELECT CONTENT_NUM,DIR,ID FROM publixher.TBL_FOLDER WHERE ID_USER=:ID_USER";
$prepare1 = $db->prepare($sql1);
$prepare1->bindValue(':ID_USER', $_GET['targetID'], PDO::PARAM_STR);
$prepare1->execute();
$result = $prepare1->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result,JSON_UNESCAPED_UNICODE)
?>