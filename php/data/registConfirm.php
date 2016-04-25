<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../lib/random_64.php';
require_once '../../lib/Sendmail.php';
//유효성 검사
$email = $_POST['email'];
$pass = $_POST['pass'];
$name = $_POST['name'];
$msg='';
$check_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL); //옵션에 따라 유효성검사하는거 틀리면 false리턴하고 맞으면 그걸 리턴하는거
$check_pass = preg_match("/^[[:alnum:]]{6,16}$/", $pass);
$check_name = preg_match("/[\xA1-\xFE]{1,3}/", $name);
if (!$check_name) {
    $check_name = preg_match('/^[a-zA-Z]{2,10}\s[a-zA-Z]{2,10}$/', $name);
}
//통과시 등록
if ($check_email && $check_pass && $check_name) {
    $id = uniqueid($db, 'user');
    $sendmail=new Sendmail();   //기본설정을 사용
    $from="publixher.com";
    $subject = "퍼블릭셔에 가입하신것을 환영합니다.";
    $body = "하이용";

    try {
        $db->beginTransaction();
        $sql = "INSERT INTO publixher.TBL_USER(ID,EMAIL,PASSWORD,USER_NAME,SEX,BIRTH) VALUES (:ID,:EMAIL,:PASSWORD,:USER_NAME,:SEX,:BIRTH)";
        $prepare = $db->prepare($sql);
        $hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        $prepare->bindValue(':ID', $id, PDO::PARAM_STR);
        $prepare->bindValue(':EMAIL', $check_email, PDO::PARAM_STR);
        $prepare->bindValue(':PASSWORD', $hash, PDO::PARAM_STR);
        $prepare->bindValue(':USER_NAME', $name, PDO::PARAM_STR);
        $prepare->bindValue(':SEX', $_POST['sex'], PDO::PARAM_STR);
        $prepare->bindValue(':BIRTH', $_POST['byear'] . $_POST['bmonth'] . $_POST['bday'], PDO::PARAM_STR);
        $prepare->execute();
        $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(ID_USER) VALUES(:ID_USER)";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID_USER', $id, PDO::PARAM_STR);
        $prepare2->execute();
        $db->commit();
        $msg = '{"result":"regist"}';
        $sendmail->send_mail($email, $from, $subject, $body);
        echo $msg;

        exit;
    } catch (PDOException $e) {
        $db->rollBack();
        $msg='{"result":"server error"}';
        echo $msg;
        exit;
    }
} else {
    $msg = '{"result":"check value"}';
    echo $msg;
    exit;
}
?>