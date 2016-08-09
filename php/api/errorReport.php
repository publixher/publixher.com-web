<?php
/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 6. 27.
 * Time: 오후 5:05
 */
header("Content-Type:application/json");
$data=$_REQUEST;
$mongomanager=new MongoDB\Driver\Manager("mongodb://DongGyun:Pp999223#@localhost:27017/publixher");
$now=date('Y-m-d H:i:s');
$bulk=new MongoDB\Driver\BulkWrite;
$error=['action'=>$data['action'],'sending_data'=>$data['sending_data'],'status'=>$data['status'],'error'=>$data['error'],'time'=>$now];
$bulk->insert($error);
$mongomanager->executeBulkWrite('publixher.errorLog',$bulk);