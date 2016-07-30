<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
$userID = $_GET['userID'];
if ($_GET['action'] == 'profilechange') {
    $pass = $_GET['pass'];
    $check_pass = preg_match("/^[[:alnum:]]{6,16}$/", $pass);

    $hash = null;
    $region = $_GET['region'];
    $hschool = $_GET['hschool'];
    $univ = $_GET['univ'];
    if ($_GET['pass'] == '') {
        //비밀번호 변경을 안했을때
        $sql = "UPDATE publixher.TBL_USER SET REGION=:REGION,H_SCHOOL=:H_SCHOOL,UNIV=:UNIV,BIRTH=:BIRTH WHERE ID=:ID";
        $prepare = $db->prepare($sql);
    } else {
        //비밀번호 변경을 했을때
        //통과시 등록
        if ($check_pass) {
            $sql = "UPDATE publixher.TBL_USER SET REGION=:REGION,H_SCHOOL=:H_SCHOOL,UNIV=:UNIV,PASSWORD=:PASSWORD,BIRTH=:BIRTH WHERE ID=:ID";
            $prepare = $db->prepare($sql);
            $hash = password_hash($_GET['pass'], PASSWORD_DEFAULT);
            $prepare->bindValue(':PASSWORD', $hash, PDO::PARAM_STR);
        } else {
            echo json_encode(array('status' => -2), JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    $prepare->bindValue(':REGION', $region, PDO::PARAM_STR);
    $prepare->bindValue(':H_SCHOOL', $hschool, PDO::PARAM_STR);
    $prepare->bindValue(':UNIV', $univ, PDO::PARAM_STR);
    $prepare->bindValue(':ID', $userID, PDO::PARAM_STR);
    $prepare->bindValue(':BIRTH', $_GET['byear'] . $_GET['bmonth'] . $_GET['bday'], PDO::PARAM_STR);
    $prepare->execute();
    //새로 로그인한 세션을 잡는다
    $sqlin = "SELECT ID,EMAIL,LEVEL,USER_NAME,SEX,BIRTH,REGION,H_SCHOOL,UNIV,PIC,IS_NICK,WRITEAUTH,EXPAUTH,BAN FROM publixher.TBL_USER WHERE ID=:ID";
    $preparein = $db->prepare($sqlin);
    $preparein->bindValue(':ID', $userID);
    $preparein->execute();
    $result = $preparein->fetch(PDO::FETCH_ASSOC);
    echo '{"status":1}';

} elseif ($_GET['action'] == 'anonyregist') {
    //익명 적합성 검사
    $nick = $_GET['nick'];
    if (preg_match("/[\xA1-\xFE\xA1-\xFEa-zA-Z0-9]/", $nick)) {
        //현재 커넥터에 익명계정으로 연결된 계정의 in_use를 N으로 만들어서 계정자체는 살아있어서 닉네임을 쓸 수 없고 컨텐츠는 볼 수 있지만 접속은 할 수 없도록 만든다
        require_once '../../lib/random_64.php';
        $sql3 = "UPDATE
	publixher.TBL_USER AS USER
    INNER JOIN publixher.TBL_CONNECTOR AS CONN ON CONN.ID_ANONY = USER.ID
SET
	IN_USE = 'N'
WHERE
	CONN.ID_USER = :ID_USER";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
        $prepare3->execute();
        //익명 사용자 생성
        $uid = uniqueid($db, 'user');
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
        $prepare1->bindValue(':USER_NAME', $_GET['nick'], PDO::PARAM_STR);
        $prepare1->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
        $prepare1->execute();
        //익명계정과 실명계정을 맵핑
        $sql2 = "UPDATE publixher.TBL_CONNECTOR SET ID_ANONY=:ID_ANONY WHERE ID_USER=:ID_USER";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
        $prepare2->bindValue(':ID_ANONY', $uid, PDO::PARAM_STR);
        $prepare2->execute();
        //새로 로그인한 세션을 잡는다
        $sqlin = "SELECT ID,EMAIL,LEVEL,USER_NAME,PIC,IS_NICK,WRITEAUTH,EXPAUTH,BAN FROM publixher.TBL_USER WHERE ID=:ID";
        $preparein = $db->prepare($sqlin);
        $preparein->bindValue(':ID', $userID);
        $preparein->execute();
        $result = $preparein->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
    } else {
        echo '{"status":-3}';
    }
} elseif ($_GET['action'] == 'profileswap') {
    $isnick = $_GET['isNick'];
    if ($isnick == true) {
        //익명일때 실명으로 새로 로그인
        $sql = "SELECT
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
        $result = $prepare->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
        exit;
    } elseif ($isnick == false) {
        //실명일때 익명으로 새로 로그인
        $sql = "SELECT ID_ANONY FROM publixher.TBL_CONNECTOR WHERE ID_USER=:ID_USER";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
        $prepare->execute();
        $anonyID = $prepare->fetchColumn();
        if (is_null($anonyID)) {
            echo '{"result":-3}';
        } else {
            $sql2 = "SELECT ID,EMAIL,LEVEL,USER_NAME,PIC,IS_NICK,WRITEAUTH,EXPAUTH,BAN FROM publixher.TBL_USER WHERE ID=:ID";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID', $anonyID, PDO::PARAM_STR);
            $prepare2->execute();
            $result = $prepare2->fetchObject(User);
            if(!$result) {
                echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
        }
    }
} elseif ($_GET['action'] == 'newfolder') {
    require_once '../../lib/random_64.php';
    $fuid = uniqueid($db, 'folder');
    $sql = "INSERT INTO publixher.TBL_FOLDER(ID,ID_USER,DIR) VALUES(:ID,:ID_USER,:DIR)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $fuid, PDO::PARAM_STR);
    $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
    $prepare->bindValue('DIR', $_GET['folder'], PDO::PARAM_STR);
    $prepare->execute();
    echo '{"status":1,"result":{"ID":"'.$fuid.'"}}';
} elseif ($_GET['action'] == 'deletefolder') {
    $folderid = $_GET['folderID'];
    $sql = "DELETE FROM publixher.TBL_FOLDER WHERE ID=:ID";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $folderid, PDO::PARAM_STR);
    $prepare->execute();

    //폴더에 있던 컨텐츠 전부 폴더를 비분류로
    $sql = "UPDATE publixher.TBL_CONTENT SET FOLDER=NULL WHERE FOLDER=:FOLDER";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':FOLDER', $folderid, PDO::PARAM_STR);
    $prepare->execute();
    echo '{"status":1}';
} elseif ($_GET['action'] == 'writeAuth') {
    $q = "UPDATE publixher.TBL_USER SET WRITEAUTH=:WRITEAUTH WHERE ID=:ID";
    $p = $db->prepare($q);
    $p->bindValue(':WRITEAUTH', $_GET['radioValue']);
    $p->bindValue(':ID', $_GET['userID']);
    $p->execute();
    echo '{"status":1}';
} elseif ($_GET['action'] == 'expAuth') {
    $q = "UPDATE publixher.TBL_USER SET EXPAUTH=:EXPAUTH WHERE ID=:ID";
    $p = $db->prepare($q);
    $p->bindValue(':EXPAUTH', $_GET['checkValue']);
    $p->bindValue(':ID', $_GET['userID']);
    $p->execute();
    echo '{"status":1}';
}elseif($_GET['action']=='charge'){
    $sql="UPDATE publixher.TBL_CONNECTOR SET CASH_POINT=CASH_POINT+:POINT WHERE ID_ANONY=:ID_ANONY OR ID_USER=:ID_USER";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_ANONY', $userID);
    $prepare->bindValue(':ID_USER', $userID);
    $prepare->bindValue(':POINT', $_GET['point']);
    $prepare->execute();
    echo '{"status":1}';
}elseif($_GET['action']=='viewAuth'){
    $sql="UPDATE publixher.TBL_USER SET VIEWAUTH=:VIEWAUTH WHERE ID=:ID";
    $prepare=$db->prepare($sql);
    $prepare->execute(array('VIEWAUTH'=>$_GET['checkValue'],'ID'=>$_GET['userID']));
    echo '{"status":1}';
}
?>