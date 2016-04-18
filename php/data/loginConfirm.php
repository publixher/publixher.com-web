<?php
session_start();
session_regenerate_id(true);
//db뒤지기
require_once'../../conf/database_conf.php';
require_once '../../conf/User.php';
//넘어온 값 받기
if (!isset($_POST['email'])) exit;
$email = $_POST['email'];
$pass = $_POST['pass'];
//쿼리
$sql = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
$prepare = $db->prepare($sql);
$prepare->bindValue(':EMAIL', $email, PDO::PARAM_STR);
$prepare->execute();
$result = $prepare->fetchObject(User);

//db데이터와 대조
if(!$result){
    echo '<script>alert("회원이 아닙니다. 회원가입을 먼저 해주세요.");history.back();</script>';
    exit;
}
if (!password_verify($pass, $result->getPASSWORD())) {
    echo "<script>alert('아이디 또는 패스워드가 잘못되었습니다.');history.back();</script>";
    exit;
}
//유저객체에 할당
$_SESSION['user'] = $result;
//세션토큰 생성(CSRF등 대책)
if(!isset($_SESSION['token'])){
    $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
}
?>
<meta http-equiv='refresh' content='0;url=/'>