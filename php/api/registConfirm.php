<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../lib/random_64.php';
require_once '../../lib/Sendmail.php';
//유효성 검사
$email = $_GET['email'];
$pass = $_GET['pass'];
$name = $_GET['name'];
$community = $_GET['community'];
$msg='';
$check_email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL); //옵션에 따라 유효성검사하는거 틀리면 false리턴하고 맞으면 그걸 리턴하는거
$check_pass = preg_match("/^[[:alnum:]]{6,16}$/", $pass);

//통과시 등록
if ($check_email && $check_pass) {
    $id = uniqueid($db, 'user');
    $sendmail=new Sendmail();   //기본설정을 사용
    $from="analograph";
    $subject = "analograph 회원가입을 위한 인증 메일입니다.";
    try {
        $db->beginTransaction();
        $sql = "INSERT INTO publixher.TBL_USER(ID,EMAIL,PASSWORD,USER_NAME,SEX,BIRTH,COMMUNITY) VALUES (:ID,:EMAIL,:PASSWORD,:USER_NAME,:SEX,:BIRTH,:COMMUNITY)";
        $prepare = $db->prepare($sql);
        $hash = password_hash($_GET['pass'], PASSWORD_DEFAULT);
        $prepare->bindValue(':ID', $id, PDO::PARAM_STR);
        $prepare->bindValue(':EMAIL', $check_email, PDO::PARAM_STR);
        $prepare->bindValue(':PASSWORD', $hash, PDO::PARAM_STR);
        $prepare->bindValue(':USER_NAME', $name, PDO::PARAM_STR);
        $prepare->bindValue(':SEX', $_GET['sex'], PDO::PARAM_STR);
        $prepare->bindValue(':BIRTH', $_GET['byear'] . $_GET['bmonth'] . $_GET['bday'], PDO::PARAM_STR);
        $prepare->bindValue(':COMMUNITY', $community==true?1:0);
        $prepare->execute();
        $seq=$db->lastInsertId();
        $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(ID_USER) VALUES(:ID_USER)";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID_USER', $id, PDO::PARAM_STR);
        $prepare2->execute();
//메일보내는작업
        $id_crypt = sha1($seq . $id);
        $body = "
<p>analograph에 오신 것을 환영합니다!</p>
<p>이메일 인증 후 익명 혹은 실명으로 자유롭게 소통하실 수 있습니다.</p>
<p><a href='http://analograph.com/registValid/${id}-${id_crypt}'>여기</a>를 클릭하시면 회원가입 절차가 모두 완료됩니다.</p>";
        $sendmail->send_mail($email, $from, $subject, $body);
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert(['id'=>$id]);
        $result = $mongomanager->executeBulkWrite('publixher.user', $bulk);
        $db->commit();
        echo '{"status":1}';
    } catch (PDOException $e) {
        $db->rollBack();
        $msg=array("e"=>$e);
        echo json_encode($e,JSON_UNESCAPED_UNICODE);
    }
} else {
    $msg = '{"status":-2}';
    echo $msg;
}
?>