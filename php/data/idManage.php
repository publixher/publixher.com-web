<?php
header("Content-Type:application/json");
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) OR $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    exit('부정한 호출입니다.');
}
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
//토큰검사
session_start();
//CSRF검사
if (!isset($_POST['token']) AND !isset($_GET['token'])) {
    exit('부정한 조작이 감지되었습니다. case1 \n$_POST["token"] :' . $_POST['token'] . ' \n $_GET["token"] :' . $_GET['token'] . '$_SESSION :' . $_SESSION);
} elseif ($_POST['token'] != $_SESSION['token'] AND $_GET['token'] != $_SESSION['token']) {
    exit('부정한 조작이 감지되었습니다. case2 \n$_POST["token"] :' . $_POST['token'] . ' \n $_GET["token"] :' . $_GET['token'] . '$_SESSION :' . $_SESSION);
}
if ($_SESSION['user']->getLEVEL() != 99) {
    exit('관리자의 권한이 없습니다.');
}
$action = $_POST['action'];
if ($action == 'ban') {
    $managerID = $_SESSION['user']->getID();
    $sql = "INSERT INTO publixher.TBL_BAN_LIST(ID_TARGET,ID_MANAGER,TIME) VALUES(:ID_TARGET,:ID_MANAGER,DATE_ADD(NOW(),INTERVAL :DAYS DAY))";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_TARGET', $_POST['target']);
    $prepare->bindValue(':ID_MANAGER', $managerID);
    $prepare->bindValue(':DAYS', $_POST['days']);
    $prepare->execute();
    echo '{"result":"Y"}';
} elseif ($action == 'release') {
    $sql = 'UPDATE publixher.TBL_USER SET BAN=NULL WHERE ID=:ID';
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $_POST['target']);
    $prepare->execute();
    echo '{"result":"N"}';
}
?>