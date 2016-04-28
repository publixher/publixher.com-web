<?php
header("Content-Type:application/json");
if (!empty($_GET)) {
    require_once '../conf/database_conf.php';

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