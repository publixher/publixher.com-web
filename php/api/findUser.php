<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
$action = $_POST['action'];
if ($action == 'find_pass') {
    $check_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($check_email) {
        $email = $_POST['email'];
        $sql = "SELECT COUNT(*) AS COUNT FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
        $prepare = $db->prepare($sql);
        $prepare->execute(array('EMAIL' => $email));
        $idExist = $prepare->fetch(PDO::FETCH_ASSOC);
        //email이 있는지 확인
        if ($idExist) {
            require_once '../../lib/random_64.php';
            require_once '../../lib/Sendmail.php';

            $from = "publixher.com";
            $subject = 'publixher.com의 임시 비밀번호 입니디.';
            $tmp_pass = rand64(15);
            $hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $sendmail = new Sendmail();   //기본설정을 사용
            $body = "
    <p>publixher의 임시 비밀번호입니다.</p>
    <p><b>임시 비밀번호</b> : ${tmp_pass}</p>
    <p>로그인 후 회원정보에서 비밀번호를 수정하라냥.</p>
    ";

            $sql = "UPDATE publixher.TBL_USER SET PASSWORD=:PASSWORD WHERE EMAIL=:EMAIL";
            $prepare = $db->prepare($sql);
            $prepare->execute(array('PASSWORD' => $hash, 'EMAIL' => $email));
            $sendmail->send_mail($email, $from, $subject, $body);
            $result = array('status' => 1);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('status' => 0), JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(array('status' => -2), JSON_UNESCAPED_UNICODE);
    }
}
?>