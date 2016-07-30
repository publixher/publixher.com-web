<?php
//db뒤지기
require_once '../../conf/database_conf.php';
//넘어온 값 받기
if (!isset($_GET['email'])){
    echo '{"status":-2}';
    exit;
}
$email = $_GET['email'];
$pass = $_GET['pass'];
$date=date("Y-m-d H:i:s",time());
$result=array();
//쿼리
$sql = "SELECT ID,EMAIL,LEVEL,USER_NAME,SEX,BIRTH,REGION,H_SCHOOL,UNIV,PIC,IS_NICK,TOP_CONTENT,WRITEAUTH,EXPAUTH,PIN,COMMUNITY,BAN FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
$prepare = $db->prepare($sql);
$prepare->bindValue(':EMAIL', $email, PDO::PARAM_STR);
$prepare->execute();
$result = $prepare->fetch(PDO::FETCH_ASSOC);

//db데이터와 대조
if(!$result){
    echo '{"status":-3}';
    exit;
}
if (!password_verify($pass, $result->getPASSWORD())) {
    echo '{"status":-4}';
    exit;
}
$bandate=$result['BAN']; //로그인 제한되었으면 튕기기
//TODO:클라이언트 레벨에서 로그인 실패 동작 처리해야함
if($bandate && $bandate>$date){
    echo '{"status":-5,"date":"'.$bandate.'"}';
    exit;
}
$level=$result['LEVEL'];
if($level==0){
    echo '{"status":-6}';
    exit;
}
echo '{"status":1}';
?>