<?php
require_once '../../conf/mongo_conf.php';
$action = $_REQUEST['action'];

if ($action == 'readDone') {
    $itemID = $_REQUEST['itemID'];
    $userID = $_REQUEST['userID'];
    $time = $_REQUEST['time'];
    $total_read;
    $average_time;
    $average_time_new;
    $total_time;
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(['id' => $userID], ['$addToSet' => ['interest' => ['id'=>$itemID,'time' => $time,'when'=>time()]]], ['upsert' => true]);
    $result = $mongomanager->executeBulkWrite('publixher.user', $bulk);

    $filter=['id'=>$itemID];
    $options=['projection'=>['_id'=>0]];
    $query=new MongoDB\Driver\Query($filter,$options);
    $rows=$mongomanager->executeQuery('publixher.contents',$query);
    foreach($rows as $row){
        $total_read=$row->total_read?$row->total_read:0;
        $average_time=$row->average_time?$row->average_time:false;
    }
    $total_time=$total_read*$average_time;
    $total_read++;
    $average_time_new=($total_time+$time)/$total_read;
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(['id'=>$itemID],['$set'=>['average_time'=>$average_time_new,'total_read'=>$total_read]],['upsert'=>true]);
    $mongomanager->executeBulkWrite('publixher.contents',$bulk);
}