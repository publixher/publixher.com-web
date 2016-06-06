<?php
require_once '../../conf/mongo_conf.php';
$action=$_POST['action'];

if($action=='readDone'){
    $itemID=$_POST['itemID'];
    $userID=$_POST['userID'];
    $time = $_POST['time'];
    
}