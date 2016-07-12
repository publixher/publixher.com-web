<?php
header("Content-Type:application/json");
/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 7. 11.
 * Time: 오후 7:21
 */
$action=$_GET['action']?$_GET['action']:$_POST['action'];
require_once '../../conf/mongo_conf.php';
if($action=='recommend') {

    require_once '../../conf/database_conf.php';

    $list = $_GET['list'];
    $fb_ids = array();
    $ids = array();
    foreach ($list as $item) {
        $fb_ids[] = $item['id'];
    }
    $filter = array('facebook_id' => array('$in' => $fb_ids));
    $option = array('projection' => array('id' => 1), 'sort' => array('_id' => -1));
    $query = new MongoDB\Driver\Query($filter, $option);
    $cursor = $mongomanager->executeQuery('publixher.user', $query);

    foreach ($cursor as $user) {
        $ids[] = $user->id;
    }
    $ids = implode('\',\'', $ids);
    $sql = "SELECT ID,USER_NAME,REPLACE(PIC,'profile','crop50') AS PIC FROM publixher.TBL_USER WHERE ID IN ('" . $ids . "')";
    $prepare = $db->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}elseif($action=='registFacebookId'){
    $facebook_id=$_POST['facebook_id'];
    $id=$_POST['mid'];
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(['id'=>$id],['$set'=>['facebook_id'=>$facebook_id]],['upsert'=>true]);
    $result = $mongomanager->executeBulkWrite('publixher.user', $bulk);
    echo '{"result":1}';
}