<?php
if(!isset($_SESSION['user'])) {
    if (($_COOKIE['cid'] != '' and isset($_COOKIE['cid']))) {
        setcookie('cid', $_COOKIE['cid'], time() + 3600 * 24 * 365, '/', 'publixher.com', false, true);
        //쿠키있으면 로그인
        include_once '../conf/database_conf.php';
        $loginsql = "SELECT * FROM publixher.TBL_USER WHERE SEQ=:SEQ";
        $loginprepare = $db->prepare($loginsql);
        $loginprepare->bindValue(':SEQ', $_COOKIE['cid'], PDO::PARAM_STR);
        $loginprepare->execute();
        $user = $loginprepare->fetchObject(User);

        $_SESSION['user'] = $user;
        //세션토큰 생성(CSRF등 대책)
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
        }
        //세션 중간에는 브라우저가 바뀌지 않는다고 가정하고 HTTP_USER_AGENT를 세션에 저장해서 탈취됬는지 확인하기
        if (!isset($_SESSION['age'])) {
            $_SESSION['age'] = $_SERVER['HTTP_USER_AGENT'];
        }
    }else{
        //세션에 user가 없으면 로그인페이지로 넘기고 있으면 유저 등록
        echo "<meta http-equiv='refresh' content='0;url=/php/login.php'>";
        exit;
    }
    session_write_close();
}
?>