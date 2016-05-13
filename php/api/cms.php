<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$action = $_GET['action'];
$userID = $_GET['userID'];
if ($action == 'most') {
    $sort = $_GET['sort'];
    $SORT = '';
    $page = $_GET['page'] * 5;
    $sql = "SELECT
ID,
  TITLE,
  PRICE,
  CATEGORY,
  SUB_CATEGORY,
  KNOCK,
  COMMENT,
  REPORT,
  SALE,
  (SALE*PRICE)+DONATE AS REVENUE
FROM publixher.TBL_CONTENT
WHERE ID_WRITER = :ID_WRITER AND FOR_SALE='Y'
ORDER BY :SORT DESC
LIMIT :PAGE,5";
    if ($sort == 'late') {
        $SORT = 'WRITE_DATE';
    } elseif ($sort == 'sell') {
        $SORT = 'SALE';
    } elseif ($sort == 'money') {
        $SORT = 'REVENUE';
    }

    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_WRITER', $userID);
    $prepare->bindValue(':SORT', $SORT);
    $prepare->bindValue(':PAGE', $page);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'monthly') {
    $start = $_GET['start'];
    $end = $_GET['end'];
    $sql = "SELECT COUNT(*) AS TOTAL_PUBLIXH,AVG(PRICE) AS AVG_PRICE
FROM publixher.TBL_CONTENT 
WHERE ID_WRITER = :ID_WRITER AND WRITE_DATE >=:START AND WRITE_DATE<=:END AND FOR_SALE='Y'";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('ID_WRITER'=>$userID,'START'=>$start,'END'=>$end));
    $result = $prepare->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT COUNT(*) AS TOTAL_SALE  
FROM publixher.TBL_BUY_LIST AS BUY_LIST
INNER JOIN publixher.TBL_CONTENT AS CONT
ON CONT.ID=BUY_LIST.ID_CONTENT
WHERE CONT.ID_WRITER = :ID_WRITER AND BUY_DATE >=:START AND BUY_DATE<=:END
GROUP BY ID_CONTENT";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('ID_WRITER'=>$userID,'START'=>$start,'END'=>$end));
    $sale=$prepare->fetchAll();
    $count=count($sale);
    for($i=0;$i<$count;$i++){
        $result['TOTAL_SALE']+=$sale[$i]['TOTAL_SALE'];
    }
    $result['SALE_PER_ITEM']=$result['TOTAL_SALE']/$count;
    //TODO:기부를 안받았으면 목록을 찾지 못함
    $sql= "SELECT SUM(BUY_LIST.PRICE)+SUM(DONATE.POINT) AS TOTAL_REVENUE
FROM publixher.TBL_BUY_LIST AS BUY_LIST
  INNER JOIN publixher.TBL_CONTENT AS CONT
    ON CONT.ID = BUY_LIST.ID_CONTENT
  INNER JOIN publixher.TBL_CONTENT_DONATE AS DONATE
    ON CONT.ID = DONATE.ID_CONTENT
WHERE CONT.ID_WRITER = :ID_WRITER AND
      ((BUY_DATE >= :START1 AND BUY_DATE <= :END1) OR (DONATE_DATE >= :START2 AND DONATE_DATE <= :END2))
GROUP BY BUY_LIST.ID_CONTENT";
    $prepare=$db->prepare($sql);
    $prepare->execute(array('ID_WRITER'=>$userID,'START1'=>$start,'END1'=>$end,'START2'=>$start,'END2'=>$end));
    $total = $prepare->fetchAll(PDO::FETCH_ASSOC);
    $count = count($total);
    for($i=0;$i<$count;$i++){
        $result['TOTAL_REVENUE']+=$total[$i]['TOTAL_REVENUE'];
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>