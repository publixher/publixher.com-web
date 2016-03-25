<?php
require_once'../../conf/database_conf.php';
require_once'../../conf/User.php';
session_start();
session_regenerate_id(true);
$userinfo = $_SESSION['user'];
$userseq = $userinfo->getSEQ();
if ($_POST['action'] == 'profilechange') {
    //토큰검사
    if(!isset($_POST['token'])){
        exit('부정한 조작이 감지되었습니다.');
    }elseif($_POST['token'] !=$_SESSION['token']){
        exit('부정한 조작이 감지되었습니다.');
    }
    //브라우저 검사
    if(!isset($_POST['age'])){
        exit('부정한 조작이 감지되었습니다.');
    }elseif($_POST['age'] !=$_SESSION['age']){
        exit('부정한 조작이 감지되었습니다.');
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
        $sql = "UPDATE publixher.TBL_USER SET REGION=:REGION,H_SCHOOL=:H_SCHOOL,UNIV=:UNIV WHERE SEQ=:SEQ";
        $prepare = $db->prepare($sql);
    } else {
        //비밀번호 변경을 했을때
        //통과시 등록
        if ($check_pass) {
            $sql = "UPDATE publixher.TBL_USER SET REGION=:REGION,H_SCHOOL=:H_SCHOOL,UNIV=:UNIV,PASSWORD=:PASSWORD WHERE SEQ=:SEQ";
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
    $prepare->bindValue(':SEQ', $userseq, PDO::PARAM_STR);
    $prepare->execute();
    //새로 로그인한 세션을 잡는다
    $sqlin="SELECT * FROM publixher.TBL_USER WHERE SEQ=:SEQ";
    $preparein=$db->prepare($sqlin);
    $preparein->bindValue(':SEQ',$userseq);
    $preparein->execute();
    $user = $preparein->fetchObject(User);
    $_SESSION['user']=$user;
    echo "<meta http-equiv='refresh' content='0;url=../profileConfig.php?id=${userseq}'>";

} elseif ($_POST['action'] == 'anonyregist') {
    //익명 적합성 검사
    $nick = $_POST['nick'];
    if (preg_match("/[\xA1-\xFE\xA1-\xFEa-zA-Z0-9]{2,10}/", $nick)) {
        //현재 커넥터에 익명계정으로 연결된 계정의 in_use를 N으로 만들어서 계정자체는 살아있어서 닉네임을 쓸 수 없고 컨텐츠는 볼 수 있지만 접속은 할 수 없도록 만든다
        $sql = "SELECT SEQ_ANONY  FROM publixher.TBL_CONNECTOR WHERE SEQ_USER=:SEQ_USER";
        $prepare = $db ->prepare($sql);
        $prepare->bindValue(':SEQ_USER',$userseq,PDO::PARAM_STR);
        $prepare->execute();
        $seq_anony = $prepare->fetchColumn();
        $sql3 = "UPDATE publixher.TBL_USER SET IN_USE='N' WHERE SEQ=:SEQ";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':SEQ',$seq_anony,PDO::PARAM_STR);
        $prepare3->execute();
        //익명 사용자 생성
        $sql1 = "INSERT INTO publixher.TBL_USER(USER_NAME,IS_NICK,BIRTH,REGION) VALUES (:USER_NAME,'Y',:BIRTH,:REGION)";
        $prepare1 = $db->prepare($sql1);
        $prepare1->bindValue(':USER_NAME', $_POST['nick'], PDO::PARAM_STR);
        $birth = $userinfo->getBirth();
        $prepare1->bindValue(':BIRTH', $birth, PDO::PARAM_STR);
        $region = $userinfo->getRegion();
        $prepare1->bindValue(':REGION', $region, PDO::PARAM_STR);
        $prepare1->execute();
        $seq = $db->lastInsertId();
        //익명계정과 실명계정을 맵핑
        $sql2 = "UPDATE publixher.TBL_CONNECTOR SET SEQ_ANONY=:SEQ_ANONY WHERE SEQ_USER=:SEQ_USER";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
        $prepare2->bindValue(':SEQ_ANONY', $seq, PDO::PARAM_STR);
        $prepare2->execute();
        echo "<meta http-equiv='refresh' content='0;url=../profile.php?id=${userseq}'>";
    } else {
        echo "<script>alert('익명은 한글,영문,숫자조합 2~10글자만 가능합니다');history.go(-1)</script>";
    }
} elseif ($_GET['action'] == 'profileswap') {
    $isnick = $userinfo->getISNICK();
    $referer=$_SERVER['HTTP_REFERER'];
    if ($isnick == 'Y') {
        //익명일때 실명으로 새로 로그인
        $sql = "SELECT SEQ_USER FROM publixher.TBL_CONNECTOR WHERE SEQ_ANONY=:SEQ_ANONY";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':SEQ_ANONY', $userseq, PDO::PARAM_STR);
        $prepare->execute();
        $userseq = $prepare->fetch(PDO::FETCH_ASSOC);
        $sql2 = "SELECT * FROM publixher.TBL_USER WHERE SEQ=:SEQ";
        $prepare2 = $db->prepare($sql2);
        $prepare2->bindValue(':SEQ', $userseq['SEQ_USER'], PDO::PARAM_STR);
        $prepare2->execute();
        $result = $prepare2->fetchObject(User);
        $_SESSION['user'] = $result;
        setcookie('cid', $result->getSEQ(), time() + 3600 * 24 * 365, '/','publixher.com',false,true);
    } elseif ($isnick == 'N') {
        //실명일때 익명으로 새로 로그인
        $sql = "SELECT SEQ_ANONY FROM publixher.TBL_CONNECTOR WHERE SEQ_USER=:SEQ_USER";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
        $prepare->execute();
        $anonyseq = $prepare->fetchColumn();
        if (is_null($anonyseq)) {
            echo '<script type="text/javascript">alert("먼저 프로필설정에서 익명계정을 만들어주세요.")</script>';
            echo "<meta http-equiv='refresh' content='0;url=${referer}'>";
        } else {
            $sql2 = "SELECT * FROM publixher.TBL_USER WHERE SEQ=:SEQ";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':SEQ', $anonyseq, PDO::PARAM_STR);
            $prepare2->execute();
            $result = $prepare2->fetchObject(User);
            $_SESSION['user'] = $result;
            setcookie('cid', $result->getSEQ(), time() + 3600 * 24 * 365, '/','publixher.com',false,true);
        }
    }
    echo "<meta http-equiv='refresh' content='0;url=${referer}'>";
} elseif ($_POST['action'] == 'newfolder') {
    $sql = "INSERT INTO publixher.TBL_FORDER(SEQ_USER,DIR) VALUES(:SEQ_USER,:DIR)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ_USER', $userseq, PDO::PARAM_STR);
    $prepare->bindValue('DIR', $_POST['folder'], PDO::PARAM_STR);
    $prepare->execute();
    echo "<meta http-equiv='refresh' content='0;url=../forderConfig.php?id=${userseq}'>";
} elseif ($_POST['action'] == 'deletefolder') {
    if ($userseq == $_POST['userseq']) {
        $folderid = $_POST['folderid'];
        $sql = "DELETE FROM publixher.TBL_FORDER WHERE SEQ=:SEQ";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':SEQ', $folderid, PDO::PARAM_STR);
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
    $q="UPDATE publixher.TBL_USER SET WRITEAUTH=:WRITEAUTH WHERE SEQ=:SEQ";
    $p=$db->prepare($q);
    $p->bindValue(':WRITEAUTH',$_POST['radioValue']);
    $p->bindValue(':SEQ',$_POST['userseq']);
    $p->execute();
}elseif($_POST['action']=='expAuth'){
    $q="UPDATE publixher.TBL_USER SET EXPAUTH=:EXPAUTH WHERE SEQ=:SEQ";
    $p=$db->prepare($q);
    $p->bindValue(':EXPAUTH',$_POST['checkValue']);
    $p->bindValue(':SEQ',$_POST['userseq']);
    $p->execute();
}
?>