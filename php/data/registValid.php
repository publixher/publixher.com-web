<?php
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/setCookie.php';
$id=$_GET['id'];
$id_crypt = $_GET['crypt'];
$result=array();
$sql_sel="SELECT SEQ FROM publixher.TBL_USER WHERE ID=:ID";
$prepare = $db->prepare($sql_sel);
$prepare->bindValue(':ID',$id);
$prepare->execute();
$seq = $prepare->fetchColumn();
//id와 시퀀스로 암호화를 해제
if(sha1($seq.$id)==$id_crypt) {
    $sql_up = "UPDATE publixher.TBL_USER SET LEVEL=1 WHERE ID=:ID";
    $prepare = $db->prepare($sql_up);
    $prepare->bindValue(':ID', $id);
    $prepare->execute();
    $login="SELECT * FROM publixher.TBL_USER WHERE SEQ=:SEQ";
    $prepare = $db->prepare($login);
    $prepare->bindValue(':SEQ', $seq);
    $prepare->execute();
    $user=$prepare->fetchObject(User);

//유저객체에 할당
    $_SESSION['user'] = $user;
//세션토큰 생성(CSRF등 대책)
    if (!isset($_SESSION['token'])) {
        $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
    }
    $c=new setCookie();
    $c->setCid($user->getID(),$db);
}
?>
<meta http-equiv='refresh' content='0;url=/'>