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
            echo '{"status":1}';
            $sendmail->send_mail($email, $from, $subject, $body);
            exit;
        } else {
            echo '{"status":0}';
            exit;
        }
    } else {
        echo '{"status":-2}';
        exit;
    }
}
?>