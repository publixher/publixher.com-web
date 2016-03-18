<?php
//TODO:데이터베이스 설정 바꿔야함
$dbServer = '127.0.0.1';
$dbUser='Kang';
$dbPass='sk763118';
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
?>