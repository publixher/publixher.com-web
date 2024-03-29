<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../lib/random_64.php';
require_once '../../lib/Sendmail.php';
require_once '../../conf/mongo_conf.php';
if($_POST['resend']=="false") {
//유효성 검사
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $name = $_POST['name'];
    $community = isset($_POST['community']) ? true : false;
    $msg = '';
    $check_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL); //옵션에 따라 유효성검사하는거 틀리면 false리턴하고 맞으면 그걸 리턴하는거
    $check_pass = preg_match("/^[[:alnum:]]{6,16}$/", $pass);

//통과시 등록
    if ($check_email && $check_pass) {
        $id = uniqueid($db, 'user');
        try {
            $db->beginTransaction();
            $sql = "INSERT INTO publixher.TBL_USER(ID,EMAIL,PASSWORD,USER_NAME,SEX,BIRTH,COMMUNITY) VALUES (:ID,:EMAIL,:PASSWORD,:USER_NAME,:SEX,:BIRTH,:COMMUNITY)";
            $prepare = $db->prepare($sql);
            $hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $prepare->bindValue(':ID', $id, PDO::PARAM_STR);
            $prepare->bindValue(':EMAIL', $check_email, PDO::PARAM_STR);
            $prepare->bindValue(':PASSWORD', $hash, PDO::PARAM_STR);
            $prepare->bindValue(':USER_NAME', $name, PDO::PARAM_STR);
            $prepare->bindValue(':SEX', $_POST['sex'], PDO::PARAM_STR);
            $prepare->bindValue(':BIRTH', $_POST['byear'] . $_POST['bmonth'] . $_POST['bday'], PDO::PARAM_STR);
            $prepare->bindValue(':COMMUNITY', $community ? 1 : 0);
            $prepare->execute();
            $seq = $db->lastInsertId();
            $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(ID_USER) VALUES(:ID_USER)";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID_USER', $id, PDO::PARAM_STR);
            $prepare2->execute();
//메일보내는작업
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->insert(['id'=>$id]);
            $result = $mongomanager->executeBulkWrite('publixher.user', $bulk);
            $db->commit();
            $msg = '{"result":"reg"}';
            echo $msg;
        } catch (PDOException $e) {
            $db->rollBack();
            $msg = '{"result":"server error"}';
            echo $msg;
        }
    } else {
        $msg = '{"result":"check value"}';
        echo $msg;
    }
}else{
    //재전송 요청일때
    $email=$_POST['email'];
    $from = "analograph";
    $subject = "analograph 회원가입을 위한 인증 메일입니다.";
    $sql="SELECT SEQ,ID FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
    $prepare=$db->prepare($sql);
    $prepare->execute(array('EMAIL'=>$email));
    $user=$prepare->fetch(PDO::FETCH_ASSOC);
    $seq=$user['SEQ'];
    $id=$user['ID'];
    $id_crypt=sha1($seq.$id);
    $body = "
<p>analograph에 오신 것을 환영합니다!</p>
<p>이메일 인증 후 익명 혹은 실명으로 자유롭게 소통하실 수 있습니다.</p>
<p><a href='http://analograph.com/registValid/${id}-${id_crypt}'>여기</a>를 클릭하시면 회원가입 절차가 모두 완료됩니다.</p>";
    $sendmail=new Sendmail();
    $sendmail->send_mail($email,$from,$subject,$body);
}
?>