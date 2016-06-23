<?php
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/setCookie.php';
//유효성 검사
$info = $_POST;
if ($info['api'] == 'naver') {
    require_once '../../lib/random_64.php';
    require_once '../../lib/getImgFromUrl.php';
    $sql = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
    $q = $db->prepare($sql);
    $q->bindValue(':EMAIL', $info['email']);
    $q->execute();
    $user = $q->fetchObject(User);
    if (!$user) {
        $id = uniqueid($db, 'user');
        $age = date("Y") - (substr($info['age'], 0, 1) . '5') . '-';
        $pic = getImgFromUrl($info['image'], 'profile', 'crop50', 50, 'crop34', 34, 'origin');
        try {
            $db->beginTransaction();
            $sql = "INSERT INTO publixher.TBL_USER(ID,EMAIL,USER_NAME,SEX,BIRTH,PIC,LEVEL) VALUES (:ID,:EMAIL,:USER_NAME,:SEX,:BIRTH,:PIC,1)";
            $prepare = $db->prepare($sql);
            $prepare->bindValue(':ID', $id, PDO::PARAM_STR);
            $prepare->bindValue(':EMAIL', $info['email'], PDO::PARAM_STR);
            $prepare->bindValue(':USER_NAME', $info['name'], PDO::PARAM_STR);
            $prepare->bindValue(':SEX', $info['gender'], PDO::PARAM_STR);
            $prepare->bindValue(':BIRTH', $age . $info['birthday'], PDO::PARAM_STR);
            $prepare->bindValue(':PIC', $pic, PDO::PARAM_STR);
            $prepare->execute();
            $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(ID_USER) VALUES(:ID_USER)";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID_USER', $id, PDO::PARAM_STR);
            $prepare2->execute();
            $sql2 = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
            $q2 = $db->prepare($sql2);
            $q2->bindValue(':EMAIL', $info['email']);
            $q2->execute();
            $user = $q2->fetchObject(User);
            $db->commit();
        } catch (PDOException $e) {
            $db->rollBack();
            $msg = '{"result":"server error"}';
            echo $msg;
            exit;

        }
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
            require_once '../../lib/random_64.php';
            require_once '../../lib/getImgFromUrl.php';
            $id = uniqueid($db, 'user');
            $pic = getImgFromUrl($info['image'], 'profile', 'crop50', 50, 'crop34', 34, 'origin');
            try {
                $db->beginTransaction();
                $sql = "INSERT INTO publixher.TBL_USER(ID,EMAIL,USER_NAME,SEX,BIRTH,PIC,LEVEL) VALUES (:ID,:EMAIL,:USER_NAME,:SEX,:BIRTH,:PIC,1)";
                $prepare = $db->prepare($sql);
                $prepare->bindValue(':ID', $id, PDO::PARAM_STR);
                $prepare->bindValue(':EMAIL', $info['email'], PDO::PARAM_STR);
                $prepare->bindValue(':USER_NAME', $info['name'], PDO::PARAM_STR);
                $prepare->bindValue(':SEX', $info['gender'], PDO::PARAM_STR);
                $prepare->bindValue(':BIRTH', $info['birthday'], PDO::PARAM_STR);
                $prepare->bindValue(':PIC', $pic, PDO::PARAM_STR);
                $prepare->execute();
                $sql2 = "INSERT INTO publixher.TBL_CONNECTOR(ID_USER) VALUES(:ID_USER)";
                $prepare2 = $db->prepare($sql2);
                $prepare2->bindValue(':ID_USER', $id, PDO::PARAM_STR);
                $sql2 = "SELECT * FROM publixher.TBL_USER WHERE EMAIL=:EMAIL";
                $q2 = $db->prepare($sql2);
                $q2->bindValue(':EMAIL', $info['email']);
                $q2->execute();
                $user = $q2->fetchObject(User);
            }catch(PDOException $e){
                $db->rollBack();
                $msg = '{"result":"server error"}';
                echo $msg;
                exit;
            }
        }
    }
}
$_SESSION['user'] = $user;
//세션토큰 생성(CSRF등 대책)
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
}
$cookieset=new setCookie();
$cookieset->setCid($user->getID(),$db);
echo '{"result":"Y"}';

?>