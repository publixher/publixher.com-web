<?php
header("Content-Type:application/json");
if (!empty($_GET)) {
    require_once '../../conf/database_conf.php';
    $target=$_GET['target'];
    if($target=='name') {
        $sql = "SELECT ID,USER_NAME,IS_NICK,REPLACE(PIC,'profile','crop34') AS PIC,COMMUNITY FROM publixher.TBL_USER WHERE (MATCH(USER_NAME) AGAINST('*" . $_GET['searchword'] . "*' IN BOOLEAN MODE) AND IN_USE='Y')";
        $prepare = $db->prepare($sql);
        $prepare->execute();
        $result = $prepare->fetchALL(PDO::FETCH_ASSOC);
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        echo $result;
    }elseif($target=='title'){
        $sql = "SELECT CONT.ID,ID_WRITER,CONT.TITLE,REPLACE(USER.PIC,'profile','crop34') AS PIC FROM publixher.TBL_CONTENT AS CONT INNER JOIN publixher.TBL_USER AS USER ON USER.ID=CONT.ID_WRITER WHERE (MATCH(TITLE) AGAINST('*".$_GET['searchword']."*' IN BOOLEAN MODE) AND DEL='N' AND EXPOSE>1)";
        $prepare=$db->prepare($sql);
        $prepare->execute();
        $result=$prepare->fetchALL(PDO::FETCH_ASSOC);
        $result=json_encode($result,JSON_UNESCAPED_UNICODE);
        echo $result;
    }elseif($target=='tag'){
        $sql = "SELECT COUNT(TAG) AS CONTENT_NUM,TAG FROM publixher.TBL_TAGS WHERE MATCH(TAG) AGAINST('*".$_GET['searchword']."*' IN BOOLEAN MODE) GROUP BY TAG ORDER BY CONTENT_NUM DESC LIMIT 5";
        $prepare=$db->prepare($sql);
        $prepare->execute();
        $result=$prepare->fetchALL(PDO::FETCH_ASSOC);
        $result=json_encode($result,JSON_UNESCAPED_UNICODE);
        echo $result;
    }elseif($target=='friend'){
        $sql="SELECT SQL_CACHE DISTINCT REPLACE(USER.PIC,'profile','crop50') AS PIC,USER.USER_NAME,USER.ID FROM publixher.TBL_USER AS USER INNER JOIN publixher.TBL_FRIENDS AS FRIENDS ON FRIENDS.ID_FRIEND=USER.ID WHERE FRIENDS.ID_USER=:ID_USER AND MATCH(USER.USER_NAME) AGAINST('*".$_GET['name']."*' IN BOOLEAN MODE) AND USER.IN_USE='Y'";
        $prepare=$db->prepare($sql);
        $prepare->bindValue(':ID_USER',$_GET['mid']);
        $prepare->execute();
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $result=json_encode($result,JSON_UNESCAPED_UNICODE);
        echo $result;
    }
} else {
    exit;
}
?>