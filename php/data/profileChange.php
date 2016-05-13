<?php
require_once'../../conf/database_conf.php';
require_once'../../conf/User.php';
session_start();
session_regenerate_id(true);
$userinfo = $_SESSION['user'];
$userID = $userinfo->getID();
if ($_POST['action'] == 'profilechange') {
    //CSRF검사
    if (!isset($_POST['token']) AND !isset($_GET['token'])) {
        exit('부정한 조작이 감지되었습니다. case1 \n$_POST["token"] :'.$_POST['token'].' \n $_GET["token"] :'.$_GET['token'].'$_SESSION :'.$_SESSION);
    } elseif ($_POST['token'] != $_SESSION['token'] AND $_GET['token'] != $_SESSION['token']) {
        exit('부정한 조작이 감지되었습니다. case2 \n$_POST["token"] :'.$_POST['token'].' \n $_GET["token"] :'.$_GET['token'].'$_SESSION :'.$_SESSION);
    }
//유효성 검사
    $pass = $_POST['pass'];
    $check_pass = preg_match("/^[[:alnum:]]{6,16}$/", $pass);

    $hash=null;
    $region=$_POST['region'];
    $hschool=$_POST['hschool'];
    $univ=$_POST['univ'];
    if ($_POST['pass'] == '') {
        //비밀번호 변경을 안했을때
        $sql = "UPDATE publixher.TBL_USER SET REGION=:REGION,H_SCHOOL=:H_SCHOOL,UNIV=:UNIV,BIRTH=:BIRTH WHERE ID=:ID";
        $prepare = $db->prepare($sql);
    } else {
        //비밀번호 변경을 했을때
        //통과시 등록
        if ($check_pass) {
            $sql = "UPDATE publixher.TBL_USER SET REGION=:REGION,H_SCHOOL=:H_SCHOOL,UNIV=:UNIV,PASSWORD=:PASSWORD,BIRTH=:BIRTH WHERE ID=:ID";
            $prepare = $db->prepare($sql);
            $hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $prepare->bindValue(':PASSWORD', $hash, PDO::PARAM_STR);
        } else {
            echo "<script>alert('비밀번호가 유효하지 않습니다. 6~16자 영어,숫자조합으로 입력해 주세요');</script>";
        }
    }
    $prepare->bindValue(':REGION', $region, PDO::PARAM_STR);
    $prepare->bindValue(':H_SCHOOL', $hschool, PDO::PARAM_STR);
    $prepare->bindValue(':UNIV', $univ, PDO::PARAM_STR);
    $prepare->bindValue(':ID', $userID, PDO::PARAM_STR);
    $prepare->bindValue(':BIRTH', $_POST['byear'] . $_POST['bmonth'] . $_POST['bday'], PDO::PARAM_STR);
    $prepare->execute();
    //새로 로그인한 세션을 잡는다
    $sqlin="SELECT * FROM publixher.TBL_USER WHERE ID=:ID";
    $preparein=$db->prepare($sqlin);
    $preparein->bindValue(':ID',$userID);
    $preparein->execute();
    $user = $preparein->fetchObject(User);
    $_SESSION['user']=$user;
    echo "<meta http-equiv='refresh' content='0;url=../profileConfig.php?id=${userID}'>";

} elseif ($_POST['action'] == 'anonyregist') {
    //익명 적합성 검사
    $nick = $_POST['nick'];
    if (preg_match("/[\xA1-\xFE\xA1-\xFEa-zA-Z0-9]/", $nick)) {
        //현재 커넥터에 익명계정으로 연결된 계정의 in_use를 N으로 만들어서 계정자체는 살아있어서 닉네임을 쓸 수 없고 컨텐츠는 볼 수 있지만 접속은 할 수 없도록 만든다
        require_once'../../lib/random_64.php';
        $sql3 = "UPDATE
	publixher.TBL_USER AS USER
    INNER JOIN publixher.TBL_CONNECTOR AS CONN ON CONN.ID_ANONY = USER.ID
SET
	IN_USE = 'N'
WHERE
	CONN.ID_USER = :ID_USER";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':ID_USER',$userID,PDO::PARAM_STR);
        $prepare3->execute();
        //익명 사용자 생성
        $uid=uniqueid($db,'user');
        $sql1 = "INSERT INTO publixher.TBL_USER (ID, USER_NAME, IS_NICK, BIRTH, REGION, LEVEL)
  SELECT
    :ID,
    :USER_NAME,
    'Y',
    BIRTH,
    REGION,
    1
  FROM publixher.TBL_USER
  WHERE ID = :ID_USER";
        $prepare1 = $db->prepare($sql1);
        $prepare1->bindValue(':ID', $uid, PDO::PARAM_STR);
        $prepare1->bindValue(':USER_NAME', $_POST['nick'], PDO::PARAM_STR);
        $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
        $prepare1->execute();
        //익명계정과 실명계정을 맵핑
        $sql2 = "UPDATE publixher.TBL_CONNECTOR SET ID_ANONY=:ID_ANONY WHERE ID_USER=:ID_USER";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
        $prepare2->bindValue(':ID_ANONY', $uid, PDO::PARAM_STR);
        $prepare2->execute();
        echo "<meta http-equiv='refresh' content='0;url=../profile.php?id=${userID}'>";
    } else {
        echo "<script>alert('익명은 한글,영문,숫자조합 2~10글자만 가능합니다');history.go(-1)</script>";
    }
} elseif ($_GET['action'] == 'profileswap') {
    $isnick = $userinfo->getISNICK();
    $referer=$_SERVER['HTTP_REFERER'];
    if ($isnick == 'Y') {
        $date=date("Y-m-d H:i:s",time());
        //익명일때 실명으로 새로 로그인
        $sql= "SELECT
  ID,
  EMAIL,
  LEVEL,
  USER_NAME,
  SEX,
  BIRTH,
  REGION,
  H_SCHOOL,
  UNIV,
  PIC,
  IS_NICK,
  WRITEAUTH,
  EXPAUTH,
  BAN
FROM publixher.TBL_USER AS USER
  INNER JOIN publixher.TBL_CONNECTOR AS CONN ON CONN.ID_USER=USER.ID
WHERE CONN.ID_ANONY = :ID";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID', $userID);
        $prepare->execute();
        $result = $prepare->fetchObject(User);

        $bandate=$result->getBAN(); //로그인 제한되었으면 튕기기
//TODO:클라이언트 레벨에서 로그인 실패 동작 처리해야함
        if($bandate && $bandate>$date){
            echo '"result":"N","reason":"banned","date":"'.$bandate.'"}';
            exit;
        }
        $_SESSION['user'] = $result;
        setcookie('cid', $result->getID(), time() + 3600 * 24 * 365, '/','publixher.com',false,true);
    } elseif ($isnick == 'N') {
        //실명일때 익명으로 새로 로그인
        $sql = "SELECT ID_ANONY FROM publixher.TBL_CONNECTOR WHERE ID_USER=:ID_USER";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
        $prepare->execute();
        $anonyID = $prepare->fetchColumn();
        if (is_null($anonyID)) {
            echo '<script type="text/javascript">alert("먼저 프로필설정에서 익명계정을 만들어주세요.")</script>';
            echo "<meta http-equiv='refresh' content='0;url=${referer}'>";
        } else {
            $sql2 = "SELECT * FROM publixher.TBL_USER WHERE ID=:ID";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID', $anonyID, PDO::PARAM_STR);
            $prepare2->execute();
            $result = $prepare2->fetchObject(User);
            $_SESSION['user'] = $result;
            setcookie('cid', $result->getID(), time() + 3600 * 24 * 365, '/','publixher.com',false,true);
        }
    }
    echo "<meta http-equiv='refresh' content='0;url=${referer}'>";
} elseif ($_POST['action'] == 'newfolder') {
    require_once'../../lib/random_64.php';
    $fuid=uniqueid($db,'folder');
    $sql = "INSERT INTO publixher.TBL_FOLDER(ID,ID_USER,DIR) VALUES(:ID,:ID_USER,:DIR)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $fuid, PDO::PARAM_STR);
    $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare->bindValue('DIR', $_POST['folder'], PDO::PARAM_STR);
    $prepare->execute();
    echo "<meta http-equiv='refresh' content='0;url=../folderConfig.php?id=${userID}'>";
} elseif ($_POST['action'] == 'deletefolder') {
    if ($userID == $_POST['userID']) {
        $folderid = $_POST['folderid'];
        $sql = "DELETE FROM publixher.TBL_FOLDER WHERE ID=:ID";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID', $folderid, PDO::PARAM_STR);
        $prepare->execute();

        //폴더에 있던 컨텐츠 전부 폴더를 비분류로
        $sql = "UPDATE publixher.TBL_CONTENT SET FOLDER=NULL WHERE FOLDER=:FOLDER";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':FOLDER', $folderid, PDO::PARAM_STR);
        $prepare->execute();
        echo '{"message":"폴더 삭제가 완료되었습니다"}';
    } else {
        echo '{"message":"자신의 폴더만 지울 수 있습니다"}';
    }
}elseif($_POST['action']=='writeAuth'){
    $q="UPDATE publixher.TBL_USER SET WRITEAUTH=:WRITEAUTH WHERE ID=:ID";
    $p=$db->prepare($q);
    $p->bindValue(':WRITEAUTH',$_POST['radioValue']);
    $p->bindValue(':ID',$_POST['userID']);
    $p->execute();
}elseif($_POST['action']=='expAuth'){
    $q="UPDATE publixher.TBL_USER SET EXPAUTH=:EXPAUTH WHERE ID=:ID";
    $p=$db->prepare($q);
    $p->bindValue(':EXPAUTH',$_POST['checkValue']);
    $p->bindValue(':ID',$_POST['userID']);
    $p->execute();
}
?>