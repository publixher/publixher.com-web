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
    if ($sort == 'late') {
        $SORT = 'WRITE_DATE';
    } elseif ($sort == 'sell') {
        $SORT = 'SALE';
    } elseif ($sort == 'money') {
        $SORT = 'REVENUE';
    }
    $sql = "SELECT
ID,
  TITLE,
  CONTENT.PRICE,
  CATEGORY,
  SUB_CATEGORY,
  KNOCK,
  COMMENT,
  REPORT,
  SALE,
  SUM(BUY_LIST.PRICE)+DONATE AS REVENUE,
  DONATE,
  WRITE_DATE
FROM publixher.TBL_CONTENT AS CONTENT
INNER JOIN publixher.TBL_BUY_LIST AS BUY_LIST
ON BUY_LIST.ID_CONTENT=CONTENT.ID
WHERE ID_WRITER = :ID_WRITER AND FOR_SALE='Y'
  GROUP BY CONTENT.ID
ORDER BY $SORT DESC
LIMIT :PAGE,5";
    
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_WRITER', $userID);
    $prepare->bindValue(':PAGE', $page);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'monthly') {
    $start = $_GET['start'];
    $end = $_GET['end'];
    //총출판,평균 판매 가격
    $sql = "SELECT COUNT(*) AS TOTAL_PUBLIXH,AVG(PRICE) AS AVG_PRICE
FROM publixher.TBL_CONTENT 
WHERE ID_WRITER = :ID_WRITER AND WRITE_DATE >=:START AND WRITE_DATE<=:END AND FOR_SALE='Y'";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('ID_WRITER'=>$userID,'START'=>$start,'END'=>$end));
    $result = $prepare->fetch(PDO::FETCH_ASSOC);
    //총 판매,출판당 평균 판매
    $sql = "SELECT COUNT(*) AS TOTAL_SALE  ,AVG(BUY_LIST.PRICE) AS AVG
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
        $result['TOTAL_POINT']+=$sale[$i]['TOTAL_SALE']*$sale[$i]['AVG'];
    }
    $result['SALE_PER_ITEM']=$result['TOTAL_SALE']/$count;
    //총후원
    $sql= "SELECT SUM(DONATE.POINT) AS DONATE
FROM publixher.TBL_CONTENT_DONATE AS DONATE
  INNER JOIN publixher.TBL_CONTENT AS CONT
    ON CONT.ID = DONATE.ID_CONTENT
WHERE CONT.ID_WRITER = :ID_WRITER AND
      DONATE_DATE >= :START AND DONATE_DATE <= :END
GROUP BY CONT.ID";
    $prepare=$db->prepare($sql);
    $prepare->execute(array('ID_WRITER'=>$userID,'START'=>$start,'END'=>$end));
    $donate = $prepare->fetchAll(PDO::FETCH_ASSOC);
    $count = count($donate);
    for($i=0;$i<$count;$i++){
        $result['TOTAL_DONATE']+=$donate[$i]['DONATE'];
    }
    $result['DONATE_PER_ITEM']=$result['TOTAL_DONATE']/$count;
    $result['TOTAL_REVENUE']=$result['TOTAL_POINT']+$result['TOTAL_DONATE'];
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}elseif($action=='item'){
    $contentID = $_GET['contentID'];
    //날짜와 그날 총 팔린 금액
    $sql= "SELECT DATE_FORMAT(BUY_DATE, '%Y/%m/%d %h') AS DATE,
  SUM(PRICE) AS PRICE
FROM publixher.TBL_BUY_LIST
WHERE ID_CONTENT = :ID_CONTENT
GROUP BY DATE";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('ID_CONTENT'=>$contentID));
    $price = $prepare->fetchAll(PDO::FETCH_ASSOC);

    //날짜와 그날 총 후원된 금액
    $sql= "SELECT DATE_FORMAT(DONATE_DATE,'%Y/%m/%d %h') AS DATE,
    SUM(POINT) AS DONATE
    FROM publixher.TBL_CONTENT_DONATE
    WHERE ID_CONTENT=:ID_CONTENT
    GROUP BY DATE";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('ID_CONTENT'=>$contentID));
    $donate=$prepare->fetchAll(PDO::FETCH_ASSOC);

    $result=array('PRICE'=>$price,'DONATE'=>$donate);
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
?>