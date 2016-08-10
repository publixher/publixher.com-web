<?php
/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 8. 9.
 * Time: 오후 7:28
 */
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