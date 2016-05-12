<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
session_start();
$action = $_GET['action'];
$userinfo = $_SESSION['user'];
$userID = $userinfo->getID();
if ($action == 'most') {
    $sort = $_GET['sort'];
    $SORT = '';
    $page = $_GET['page'] * 5;
    $sql = "SELECT
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
    $sql= "SELECT COUNT(*) AS TOTAL_PUBLIXH,
  SUM(SALE) AS TOTAL_SALE,
  SUM((SALE*PRICE)+DONATE) AS TOTAL_REVENUE,
  AVG(PRICE) AS AVG_PRICE,
  AVG(SALE) AS SALE_PER_ITEM,
  AVG((SALE*PRICE)+DONATE) AS REVENUE_PER_ITEM
FROM publixher.TBL_CONTENT AS CONT
INNER JOIN publixher.TBL_CONTENT_DONATE AS DONATE
ON DONATE.ID_CONTENT=CONT.ID
INNER JOIN publixher.TBL_BUY_LIST AS BUY
ON BUY.ID_CONTENT=CONT.ID
WHERE ID_WRITER = :ID_WRITER AND WRITE_DATE >=:START AND WRITE_DATE<=:END AND FOR_SALE='Y'";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_WRITER', $userID);
    $prepare->bindValue(':START', $start);
    $prepare->bindValue(':END', $end);
        $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>