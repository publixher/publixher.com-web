<?php
if(!$_SESSION['user']) {
    if (($_COOKIE['cid'] != '' and isset($_COOKIE['cid']))) {
        setcookie('cid', $_COOKIE['cid'], time() + 3600 * 24 * 365, '/');
        //쿠키있으면 로그인
        include_once '../conf/database_conf.php';
        require_once "../conf/User.php";
        $loginsql = "SELECT * FROM publixher.TBL_USER WHERE ID=:ID";
        $loginprepare = $db->prepare($loginsql);
        $loginprepare->bindValue(':ID', $_COOKIE['cid'], PDO::PARAM_STR);
        $loginprepare->execute();
        $user = $loginprepare->fetchObject(User);
        
        $level=$user->getLEVEL();
        if($level==0){
            echo '{"result":"N","reason":"not valid"}';
            exit;
        }
        $_SESSION['user'] = $user;
        //세션토큰 생성(CSRF등 대책)
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
        }
    }else{
        //세션에 user가 없으면 로그인페이지로 넘기고 있으면 유저 등록
        echo "<meta http-equiv='refresh' content='0;url=/https/login.php'>";
        exit;
    }
    session_write_close();
}
?>