<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';

$thisitemID = $_POST['thisitemID'];
$userID = $_POST['userID'];
$point = $_POST['point'];

$sql="SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE ID_ANONY=:ID_ANONY OR ID_USER=:ID_USER";
$prepare = $db->prepare($sql);
$prepare->bindValue(':ID_ANONY', $userID);
$prepare->bindValue(':ID_USER', $userID);
$prepare->execute();
if($prepare->fetchColumn()>$point) {
    try {
        $db->beginTransaction();
        //내 포인트 깎기
        $dsql = "UPDATE publixher.TBL_CONNECTOR SET CASH_POINT=CASH_POINT-:POINT WHERE ID_USER=:ID_USER OR ID_ANONY=:ID_ANONY";
        $dprepare = $db->prepare($dsql);
        $dprepare->bindValue(':POINT', $point);
        $dprepare->bindValue(':ID_ANONY', $userID);
        $dprepare->bindValue(':ID_USER', $userID);
        $dprepare->execute();
        //콘텐츠에 기부 올리기
        $upsql = "UPDATE publixher.TBL_CONTENT SET DONATE=DONATE+:POINT WHERE ID=:ID";
        $upprepare = $db->prepare($upsql);
        $upprepare->bindValue(':ID', $thisitemID);
        $upprepare->bindValue(':POINT', $point);
        $upprepare->execute();
        //판매자의 포인트 올리기
        $psql="UPDATE publixher.TBL_CONNECTOR AS CONN
  INNER JOIN publixher.TBL_CONTENT AS CONT
  ON CONT.ID_WRITER =CONN.ID_USER OR CONT.ID_WRITER=CONN.ID_ANONY
  SET CONN.CASH_POINT=CONN.CASH_POINT+:POINT WHERE CONT.ID=:ID";
        $pprepare = $db->prepare($psql);
        $pprepare->bindValue(':POINT', $point);
        $pprepare->bindValue(':ID', $thisitemID);
        $pprepare->execute();

        $db->commit();
        echo '{"status":1}';
    } catch (PDOException $e) {
        $db->rollBack();
        echo '{"status":-1}';
        exit;
    }
}else{
    echo '{"status":-2}';
    exit;
}
?>