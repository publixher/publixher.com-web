<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';

$userID = $_GET['userID'];

$sql="SELECT USER_NAME,REPLACE(PIC,'profile','crop34') AS PIC,
USER.ID
FROM publixher.TBL_USER AS USER
INNER JOIN publixher.TBL_FRIENDS AS COMM
ON COMM.ID_FRIEND=USER.ID
WHERE COMM.ID_USER=:ID_USER AND USER.COMMUNITY=1";
$prepare = $db->prepare($sql);
$prepare->bindValue(':ID_USER',$userID);
$prepare->execute();
$commlist=$prepare->fetchAll(PDO::FETCH_ASSOC);
$commlistnum = count($commlist);

$sql2= "SELECT
  ID,
  IF(TITLE IS NOT NULL, TITLE, LEFT(BODY_TEXT, 20) )AS TITLE
     FROM publixher.TBL_CONTENT WHERE
     ID_TARGET = :ID_TARGET AND EXPOSE > 1 AND DEL = 'N' ORDER BY SEQ DESC LIMIT 5";
$prepare = $db->prepare($sql2);
for($i=0;$i<$commlistnum;$i++){
    $prepare->bindValue(':ID_TARGET', $commlist[$i]['ID']);
    $prepare->execute();
    $commlist[$i]['CONT_LIST'] = $prepare->fetchAll(PDO::FETCH_ASSOC);
}
if(!$commlist) {
    echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
    exit;
}
echo json_encode(array('result'=>$commlist,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);

?>