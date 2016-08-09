<?php
header("Content-Type:application/json");
if (!empty($_REQUEST)) {
    require_once '../../conf/database_conf.php';
    $target=$_REQUEST['target'];
    if($target=='name') {
        $sql = "SELECT ID,USER_NAME,IS_NICK,PIC FROM publixher.TBL_USER WHERE (MATCH(USER_NAME) AGAINST('*" . $_REQUEST['searchword'] . "*' IN BOOLEAN MODE) AND IN_USE='Y')";
        $prepare = $db->prepare($sql);
        $prepare->execute();
        $result = $prepare->fetchALL(PDO::FETCH_ASSOC);
        if(!$result) {
            echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
    }elseif($target=='title'){
        $sql = "SELECT ID,ID_WRITER,TITLE FROM publixher.TBL_CONTENT WHERE (MATCH(TITLE) AGAINST('*".$_REQUEST['searchword']."*' IN BOOLEAN MODE) AND DEL='N' AND EXPOSE>1)";
        $prepare=$db->prepare($sql);
        $prepare->execute();
        $result=$prepare->fetchALL(PDO::FETCH_ASSOC);
        if(!$result) {
            echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
    }elseif($target=='tag'){
        $sql = "SELECT COUNT(TAG) AS CONTENT_NUM,TAG FROM publixher.TBL_TAGS WHERE MATCH(TAG) AGAINST('*".$_REQUEST['searchword']."*' IN BOOLEAN MODE) GROUP BY TAG ORDER BY CONTENT_NUM DESC LIMIT 5";
        $prepare=$db->prepare($sql);
        $prepare->execute();
        $result=$prepare->fetchALL(PDO::FETCH_ASSOC);
        if(!$result) {
            echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
    }elseif($target=='friend'){
        $sql="SELECT SQL_CACHE DISTINCT REPLACE(USER.PIC,'profile','crop50') AS PIC,USER.USER_NAME,USER.ID FROM publixher.TBL_USER AS USER INNER JOIN publixher.TBL_FRIENDS AS FRIENDS ON FRIENDS.ID_FRIEND=USER.ID WHERE FRIENDS.ID_USER=:ID_USER AND MATCH(USER.USER_NAME) AGAINST('*".$_REQUEST['searchword']."*' IN BOOLEAN MODE) AND USER.IN_USE='Y'";
        $prepare=$db->prepare($sql);
        $prepare->bindValue(':ID_USER',$_REQUEST['userID']);
        $prepare->execute();
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        if(!$result) {
            echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
    }
} else {
    exit;
}
?>