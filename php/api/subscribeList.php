<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$userID = $_GET['userID'];
$sql= "SELECT
  USER_NAME,
  REPLACE(PIC, 'profile', 'crop34') AS PIC,
  USER.ID,
  FOLLOW.LAST_CHECK,
  FOLLOW.LAST_UPDATE
FROM publixher.TBL_USER USER
  INNER JOIN publixher.TBL_FOLLOW FOLLOW ON FOLLOW.ID_MASTER = USER.ID
WHERE FOLLOW.ID_SLAVE = 'T_c3kDKpip'";
$prepare = $db->prepare($sql);
$prepare->bindValue(':ID_SLAVE',$userID);
$prepare->execute();
$masList=$prepare->fetchAll(PDO::FETCH_ASSOC);
$masCount=count($masList);
$sql2="SELECT TITLE,ID FROM publixher.TBL_CONTENT WHERE ID_WRITER=:ID_WRITER ORDER BY SEQ DESC LIMIT 5";
$prepare = $db->prepare($sql2);
for($i=0;$i<$masCount;$i++){
    $prepare->bindValue(':ID_WRITER',$masList[$i]['ID']);
    $prepare->execute();
    $masList[$i]['CONT_LIST']=$prepare->fetchAll(PDO::FETCH_ASSOC);
}

if(!$masList) {
    echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
    exit;
}
echo json_encode(array('result'=>$masList,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
?>

