<?php
header("Content-Type:application/json");
if (!empty($_GET)) {
    require_once'../../conf/database_conf.php';
    $sql = "SELECT SEQ,SEQ_WRITER,TITLE FROM publixher.TBL_CONTENT WHERE TITLE LIKE CONCAT('%',:TITLE,'%')";
    $prepare=$db->prepare($sql);
    $prepare->bindValue(':TITLE',$_GET['searchword'],PDO::PARAM_STR);
    $prepare->execute();
    $result=$prepare->fetchALL(PDO::FETCH_ASSOC);
    $result=json_encode($result,JSON_UNESCAPED_UNICODE);
    echo $result;
} else {
    exit;
}
?>