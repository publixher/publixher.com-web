<?php
header("Content-Type:application/json");
if (!empty($_GET)) {
    require_once '../../conf/database_conf.php';
    $sql = "SELECT SEQ,USER_NAME,IS_NICK,PIC FROM publixher.TBL_USER WHERE (USER_NAME LIKE CONCAT('%',:USER_NAME,'%') AND IN_USE='Y')";
    $prepare=$db->prepare($sql);
    $prepare->bindValue(':USER_NAME',$_GET['searchword'],PDO::PARAM_STR);
    $prepare->execute();
    $result=$prepare->fetchALL(PDO::FETCH_ASSOC);
    $result=json_encode($result,JSON_UNESCAPED_UNICODE);
    echo $result;
} else {
    exit;
}
?>