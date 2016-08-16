<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../lib/random_64.php';
require_once '../../lib/Sendmail.php';
//유효성 검사
$email = $_REQUEST['email'];
$pass = $_REQUEST['pass'];
$name = $_REQUEST['name'];
$community = $_REQUEST['community'];
$msg='';
$check_email = filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL); //옵션에 따라 유효성검사하는거 틀리면 false리턴하고 맞으면 그걸 리턴하는거
$check_pass = preg_match("/^[[:alnum:]]{6,16}$/", $pass);

//통과시 등록
if ($check_email && $check_pass) {
    $id = uniqueid($db, 'user');
    try {
        $db->beginTransaction();
        $sql = "INSERT INTO publixher.TBL_USER(ID,EMAIL,PASSWORD,USER_NAME,SEX,BIRTH,COMMUNITY) VALUES (:ID,:EMAIL,:PASSWORD,:USER_NAME,:SEX,:BIRTH,:COMMUNITY)";
        $prepare = $db->prepare($sql);
        $hash = password_hash($_REQUEST['pass'], PASSWORD_DEFAULT);
        $prepare->bindValue(':ID', $id, PDO::PARAM_STR);
        $prepare->bindValue(':EMAIL', $check_email, PDO::PARAM_STR);
        $prepare->bindValue(':PASSWORD', $hash, PDO::PARAM_STR);
        $prepare->bindValue(':USER_NAME', $name, PDO::PARAM_STR);
        $prepare->bindValue(':SEX', $_REQUEST['sex'], PDO::PARAM_STR);
        $prepare->bindValue(':BIRTH', $_REQUEST['byear'] . $_REQUEST['bmonth'] . $_REQUEST['bday'], PDO::PARAM_STR);
        $prepare->bindValue(':COMMUNITY', $community==true?1:0);
        $prepare->execute();
        $seq=$db->lastInsertId();
        $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(ID_USER) VALUES(:ID_USER)";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID_USER', $id, PDO::PARAM_STR);
        $prepare2->execute();
//메일보내는작업
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert(['id'=>$id]);
//            $result = $mongomanager->executeBulkWrite('publixher.user', $bulk);
        $db->commit();
        echo json_encode(array('status'=>1),JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        $db->rollBack();
        if($e->getCode()==23000){
            $msg='{"status":-3}';
        }else {
            $msg = '{"status":-1}';
        }
        echo $msg;
    }
} else {
    $msg = '{"status":-2}';
    echo $msg;
}
?>