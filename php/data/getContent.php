<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/passing_time.php';
require_once '../../lib/getC.php';
$nowpage = $_GET['nowpage'] * 10;
$category = isset($_GET['category']) ? $_GET['category'] : null;
$sub_category = isset($_GET['sub_category']) ? $_GET['sub_category'] : null;
session_start();
if (!isset($_SESSION['user'])) include_once "../../lib/loginchk.php";
$userID = $_SESSION['user']->getID();
$getC = new getC($userID, $db);
//콘텐츠 검색임시로 그냥 다 불러오기
if (isset($_GET['profile'])) {   //프로필에선 그사람이 쓴거,그사람이 타겟인거 시간순 노출
    $result = $getC->profile($nowpage, $_GET['profile'], $category, $sub_category);
} elseif (isset($_GET['fid'])) { //폴더에선 폴더 내용물이 시간순 노출
    $result = $getC->folder($nowpage, $_GET['fid'], $category, $sub_category);
} elseif (isset($_GET['buylist'])) { //구매목록에선 구매한거 구매한 시간순(글쓴 시간순이 아님)으로 노출
    $result = $getC->buyList($nowpage, $category, $sub_category);
} elseif (isset($_GET['getItem'])) {  //한개만 특정 주소로 가서 찾는것
    if ($nowpage != 0) exit;   //첫번재 로드가 아니면 아무것도 안줌
    $result = $getC->one($_GET['getItem']);
} elseif (isset($_GET['tag'])) {
    $result = $getC->tag($nowpage, $_GET['tag'], $category, $sub_category);
} elseif (isset($_GET['body'])) {
    $result = $getC->body($nowpage, $_GET['body'], $category, $sub_category);
} elseif (isset($_GET['subscribe'])) {
    $result = $getC->subscribe($nowpage, $category, $sub_category);
} elseif (isset($_GET['community'])) {
    $result = $getC->community($nowpage, $category, $sub_category);
} else {  //메인화면에서 노출시켜줄 순
    $result = $getC->main($nowpage, $category, $sub_category);
    $getC->set_recommended();
}


for ($i = 0; $i < count($result); $i++) {
    $result[$i]['WRITE_DATE'] = passing_time($result[$i]['WRITE_DATE']);
    //유료일경우 구매한건지 안구매한건지 확인하는것
    if ($result[$i]['FOR_SALE'] == 'Y') {
        if ($result[$i]['ID_WRITER'] == $userID) {
            $result[$i]['BOUGHT'] = $result[$i]['WRITE_DATE'];
        } else {
            $sql = "SELECT BUY_DATE FROM publixher.TBL_BUY_LIST WHERE ID_USER=:ID_USER AND ID_CONTENT=:ID_CONTENT";
            $prepare = $db->prepare($sql);
            $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
            $prepare->bindValue(':ID_CONTENT', $result[$i]['ID'], PDO::PARAM_STR);
            $prepare->execute();
            $bought = $prepare->fetch(PDO::FETCH_ASSOC);
            $result[$i]['BOUGHT'] = $bought['BUY_DATE'];
        }
    }
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>