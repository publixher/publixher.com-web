<?php
require_once '../../conf/database_conf.php';
//유효성 검사
$email=$_POST['email'];
$pass=$_POST['pass'];
$name=$_POST['name'];

$check_email=filter_var($_POST['email'],FILTER_VALIDATE_EMAIL); //옵션에 따라 유효성검사하는거 틀리면 false리턴하고 맞으면 그걸 리턴하는거
$check_pass=preg_match("/^[[:alnum:]]{6,16}$/",$pass);
$check_name=preg_match("/[\xA1-\xFE]{1,3}/",$name);
if(!$check_name){
    $check_name=preg_match('/^[a-zA-Z]{2,10}\s[a-zA-Z]{2,10}$/',$name);
}
//통과시 등록
if($check_email && $check_pass && $check_name) {
    $sql = "INSERT INTO publixher.TBL_USER(EMAIL,PASSWORD,USER_NAME,SEX,BIRTH) VALUES (:EMAIL,:PASSWORD,:USER_NAME,:SEX,:BIRTH)";
    $prepare = $db->prepare($sql);
    $hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $prepare->bindValue(':EMAIL', $check_email, PDO::PARAM_STR);
    $prepare->bindValue(':PASSWORD', $hash, PDO::PARAM_STR);
    $prepare->bindValue(':USER_NAME', $name, PDO::PARAM_STR);
    $prepare->bindValue(':SEX', $_POST['sex'], PDO::PARAM_STR);
    $prepare->bindValue(':BIRTH', $_POST['byear'] . $_POST['bmonth'] . $_POST['bday'], PDO::PARAM_STR);
    try {
        $prepare->execute();
        $seq = $db->lastInsertId();
        $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(SEQ_USER) VALUES(:SEQ_USER)";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ_USER', $seq, PDO::PARAM_STR);
        try {
            $prepare2->execute();
        } catch (PDOException $e) {
            trigger_error("<script>alert(' 입력하는데 실패. 이유 : " . $e->getMessage() . "'");
        }
    } catch (PDOException $e) {
        trigger_error("<script>alert(' 입력하는데 실패. 이유 : " . $e->getMessage() . "'");
    }
}else{
    echo '<script>alert("입력된 값을 확인해 주세요")</script>';
}
?>
<meta http-equiv='refresh' content='0;url=../login.php'>