<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$report = $_GET['report'];
$id = $_GET['userID'];
$sql = "INSERT INTO publixher.TBL_REPORT(WRITER_ID,REPORT) VALUES(:WRITER_ID,:REPORT)";
$prepare = $db->prepare($sql);
$prepare->bindValue(':WRITER_ID',$id);
$prepare->bindValue(':REPORT',$report);
$prepare->execute();
echo '{"status":1}';
?>