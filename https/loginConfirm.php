<?php
session_start();
session_regenerate_id(true);
//db뒤지기
require_once 'database_conf.php';
require_once 'User.php';
//넘어온 값 받기
if (!isset($_POST['email'])) exit;
$email = $_POST['email'];
$pass = $_POST['pass'];
$date=date("Y-m-d H:i:s",time());
$result=array();
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
$bandate=$result->getBAN(); //로그인 제한되었으면 튕기기
//TODO:클라이언트 레벨에서 로그인 실패 동작 처리해야함
if($bandate && $bandate>$date){
    echo '"result":"N","reason":"banned","date":"'.$bandate.'"}';
    exit;
}
$level=$result->getLEVEL();
if($level==0){
    echo '{"result":"N","reason":"not valid"}';
    exit;
}
//유저객체에 할당
$_SESSION['user'] = $result;
//세션토큰 생성(CSRF등 대책)
if(!isset($_SESSION['token'])){
    $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
}
setcookie('cid', $result->getID(), time() + 3600 * 24 * 365, '/', 'publixher.com', false, true);
?>
<meta http-equiv='refresh' content='0;url=/'>
