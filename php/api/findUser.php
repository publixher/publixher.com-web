<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$action = $_POST['action'];
if ($action == 'find_pass') {
    $check_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($check_email) {
        $email = $_POST['email'];
        $sql = "SELECT SUBSTRING(PASSWORD,20,8) FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
        $prepare = $db->prepare($sql);
        $prepare->execute(array('EMAIL' => $email));
        $key = $prepare->fetchColumn();
        //email이 있는지 확인
        if ($key) {
            require_once '../../lib/Sendmail.php';

            $from = "publixher.com";
            $subject = 'publixher.com 회원 인증번호 입니다.';
            $sendmail = new Sendmail();   //기본설정을 사용
            $body = "<p>publixher의 회원 인증번호 입니다.</p>
    <p><b>인증번호</b> : ${key}</p>";
            $sendmail->send_mail($email, $from, $subject, $body);
            echo '{"status":1}';
            exit;
        } else {
            echo '{"status":0}';
            exit;
        }
    } else {
        echo '{"status":-2}';
        exit;
    }
}elseif($action=='valid_pass'){
    $email = $_POST['email'];
    $valid = $_POST['valid'];
    $pass_sub;

    $sql="SELECT SUBSTRING(PASSWORD,20,8) FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('EMAIL'=>$email));
    $pass_sub = $prepare->fetchColumn();
    if($pass_sub==$valid){
        echo '{"status":1}';
        exit;
    }else{
        echo '{"status":0}';
        exit;
    }
}elseif($action=='change_pass'){
    $email = $_POST['email'];
    $valid=$_POST['valid'];
    $sql="SELECT SUBSTRING(PASSWORD,20,8) FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
    $prepare = $db->prepare($sql);
    $prepare->execute(array('EMAIL'=>$email));
    $pass_sub = $prepare->fetchColumn();
    if($pass_sub==$valid) {
        $hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);

        $sql = "UPDATE publixher.TBL_USER SET PASSWORD=:PASS WHERE EMAIL=:EMAIL";
        $prepare=$db->prepare($sql);
        $prepare->execute(array('PASS' => $hash, 'EMAIL' => $email));
        echo '{"status":1}';
    }else{
        echo '{"status":-2}';
    }
}
?>