<?php
/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 8. 9.
 * Time: 오후 7:28
 */
$log_txt = $_REQUEST['id']."|".$_REQUEST['token'];

$log_dir = "/var/www/html/publixherBoot";
$log_file = fopen($log_dir."/log.txt", "a");
fwrite($log_file, $log_txt."\r\n");
fclose($log_file);
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';

if(isset($_REQUEST['token'])){
    $id = $_REQUEST['id'];
    $token=$_REQUEST['token'];
    $sql="INSERT INTO publixher.TBL_DEVICES(USER_ID, DEVICE_TOKEN) VALUES(:ID,:TOKEN)";
    try {
        $db->beginTransaction();
        $prepare = $db->prepare($sql);
        $prepare->execute(array('ID' => $id, 'TOKEN' => $token));
        $db->commit();
        echo '{"status":1}';
    }catch(PDOException $e){
        $db->rollBack();
        echo '{"status":-1}';
    }
}