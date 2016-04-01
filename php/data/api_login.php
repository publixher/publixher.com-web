<?php
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
//유효성 검사
$info = $_POST;
if ($info['api'] == 'naver') {
    $sql = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
    $q = $db->prepare($sql);
    $q->bindValue(':EMAIL', $info['email']);
    $q->execute();
    $user = $q->fetchObject(User);
    if (!$user) {
        $age = date("Y") - (substr($info['age'], 0, 1) . '5') . '-';
        $sql = "INSERT INTO publixher.TBL_USER(EMAIL,USER_NAME,SEX,BIRTH,PIC) VALUES (:EMAIL,:USER_NAME,:SEX,:BIRTH,:PIC)";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':EMAIL', $info['email'], PDO::PARAM_STR);
        $prepare->bindValue(':USER_NAME', $info['name'], PDO::PARAM_STR);
        $prepare->bindValue(':SEX', $info['gender'], PDO::PARAM_STR);
        $prepare->bindValue(':BIRTH', $age . $info['birthday'], PDO::PARAM_STR);
        $prepare->bindValue(':PIC', $info['image'], PDO::PARAM_STR);
        $prepare->execute();
        $seq = $db->lastInsertId();
        $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(SEQ_USER) VALUES(:SEQ_USER)";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ_USER', $seq, PDO::PARAM_STR);
        $sql2 = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
        $q2 = $db->prepare($sql2);
        $q2->bindValue(':EMAIL', $info['email']);
        $q2->execute();
        $user = $q2->fetchObject(User);
    }
} elseif ($info['api'] == 'facebook') {
    if ($info['action'] == 'login') {
        $sql = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
        $q = $db->prepare($sql);
        $q->bindValue(':EMAIL', $info['email']);
        $q->execute();
        $user = $q->fetchObject(User);
    } elseif ($info['action'] == 'reg') {
        $sql = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
        $q = $db->prepare($sql);
        $q->bindValue(':EMAIL', $info['email']);
        $q->execute();
        $user = $q->fetchObject(User);
        if (!$user) {
            $sql = "INSERT INTO publixher.TBL_USER(EMAIL,USER_NAME,SEX,BIRTH,PIC) VALUES (:EMAIL,:USER_NAME,:SEX,:BIRTH,:PIC)";
            $prepare = $db->prepare($sql);
            $prepare->bindValue(':EMAIL', $info['email'], PDO::PARAM_STR);
            $prepare->bindValue(':USER_NAME', $info['name'], PDO::PARAM_STR);
            $prepare->bindValue(':SEX', $info['gender'], PDO::PARAM_STR);
            $prepare->bindValue(':BIRTH', $info['birthday'], PDO::PARAM_STR);
            $prepare->bindValue(':PIC', $info['image'], PDO::PARAM_STR);
            $prepare->execute();
            $seq = $db->lastInsertId();
            $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(SEQ_USER) VALUES(:SEQ_USER)";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':SEQ_USER', $seq, PDO::PARAM_STR);
            $sql2 = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
            $q2 = $db->prepare($sql2);
            $q2->bindValue(':EMAIL', $info['email']);
            $q2->execute();
            $user = $q2->fetchObject(User);
        }
    }
}
$_SESSION['user'] = $user;
//세션토큰 생성(CSRF등 대책)
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
}
//세션 중간에는 브라우저가 바뀌지 않는다고 가정하고 HTTP_USER_AGENT를 세션에 저장해서 탈취됬는지 확인하기
if (!isset($_SESSION['age'])) {
    $_SESSION['age'] = $_SERVER['HTTP_USER_AGENT'];
}
setcookie('cid', $user->getSEQ(), time() + 3600 * 24 * 365, '/', 'publixher.com', false, true);
echo '{"result":"Y"}';

?>