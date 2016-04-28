<?php
header("Content-Type:application/json");
if (!empty($_GET)) {
    $dbServer = '127.0.0.1';
    $dbUser='Kang';
    $dbPass='!Pp999223';
    $dbName='publixher';

    $dsn="mysql:host={$dbServer};dbName={$dbName};,charset=utf8";

    try {
        //db연결하기
        $db = new PDO($dsn, $dbUser, $dbPass);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->query("SET NAMES utf8;");


    } catch (PDOException $e) {
        echo '접속할 수 없습니다. 이유 : ' . h($e->getMessage());
    }

    $sql = "SELECT EMAIL FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
    $prepare=$db->prepare($sql);
    $prepare->bindValue(':EMAIL',$_GET['email'],PDO::PARAM_STR);
    $prepare->execute();
    $result=$prepare->fetch(PDO::FETCH_ASSOC);
    if($result){
        echo "{\"id\":\"is\"}";
    }else{
        echo "{\"id\":\"no\"}";
    }

} else {
    exit;
}
?>