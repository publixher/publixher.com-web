<?php
header("Content-Type:application/json");
if (!empty($_GET)) {
    require_once '../../conf/database_conf.php';
    $target=$_GET['target'];
    if($target=='name') {
        $sql = "SELECT ID,USER_NAME,IS_NICK,PIC FROM publixher.TBL_USER WHERE (MATCH(USER_NAME) AGAINST('*" . $_GET['searchword'] . "*' IN BOOLEAN MODE) AND IN_USE='Y')";
        $prepare = $db->prepare($sql);
        $prepare->execute();
        $result = $prepare->fetchALL(PDO::FETCH_ASSOC);
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        echo $result;
    }elseif($target=='title'){
        $sql = "SELECT ID,ID_WRITER,TITLE FROM publixher.TBL_CONTENT WHERE (MATCH(TITLE) AGAINST('*".$_GET['searchword']."*' IN BOOLEAN MODE) AND DEL='N' AND EXPOSE>1)";
        $prepare=$db->prepare($sql);
        $prepare->bindValue(':TITLE',$_GET['searchword'],PDO::PARAM_STR);
        $prepare->execute();
        $result=$prepare->fetchALL(PDO::FETCH_ASSOC);
        $result=json_encode($result,JSON_UNESCAPED_UNICODE);
        echo $result;
    }elseif($target=='tag'){
        $sql = "SELECT COUNT(TAG) AS CONTENT_NUM,TAG FROM publixher.TBL_TAGS WHERE MATCH(TAG) AGAINST('*".$_GET['searchword']."*' IN BOOLEAN MODE) GROUP BY TAG ORDER BY CONTENT_NUM DESC LIMIT 5";
        $prepare=$db->prepare($sql);
        $prepare->bindValue(':TITLE',$_GET['searchword'],PDO::PARAM_STR);
        $prepare->execute();
        $result=$prepare->fetchALL(PDO::FETCH_ASSOC);
        $result=json_encode($result,JSON_UNESCAPED_UNICODE);
        echo $result;
    }
} else {
    exit;
}
?>