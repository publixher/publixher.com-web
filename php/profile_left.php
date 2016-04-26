<div id="left">
    <?php
    require_once '../conf/getTarget.php';
    $profilepic = $target['PIC'];
    $birth = date("Y년m월d일", strtotime($target['BIRTH']));
    $userinfo = $_SESSION['user'];
    $userID = $userinfo->getID();
    echo "<div class='profile-wrap'><img id='profilepic' src='${profilepic}'></div>";
    //스크립트 올리기?>
    <script>
        var targetid = '<?=$targetid?>';
        var myID = '<?=$userID?>';
        var mid = myID;
    </script>
    <script src="/js/profile_left.js"></script>
    <p id="name"><?= $target['USER_NAME'] ?></p>
    <ul class="list-unstyled" id="profile">
        <li>생일 : <?= $birth ?></li>
        <li>대학교 : <?= $target['UNIV'] ?></li>
        <li>고등학교 : <?= $target['H_SCHOOL'] ?></li>
    </ul>
    <?php
    if ($userID == $targetid) {
        //현재 접속자와 타겟 유저가 같을때의 동작
        echo "<a href='/profileConfig/${targetid}' id='profileMod'>정보 수정하기</a></ul><hr>";

        //가진돈 찾기위해 커넥터를 통해 캐쉬를 찾음
        $sql1 = '';
        if ($userinfo->getISNICK() == "N") {
            $sql1 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE ID_USER=:ID_TARGET";
        } else if ($userinfo->getISNICK() == "Y") {
            $sql1 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE ID_ANONY=:ID_TARGET";
        }
        $prepare1 = $db->prepare($sql1);
        $prepare1->bindValue(':ID_TARGET', $userinfo->getID(), PDO::PARAM_STR);
        $prepare1->execute();
        $cash = $prepare1->fetch(PDO::FETCH_ASSOC);
        $cash = $cash['CASH_POINT'];

        ?>
        <ul class="list-unstyled" id="activity">
            <li><a><?= $cash ?> pigs</a></li>
            <li><a>충전 &middot 결제정보</a></li>
            <li><a href="/buyList/<?= $userID ?>">구매목록</a></li>
            <li><a>판매관리</a></li>
        </ul>
        <?php
    }
    //폴더목록 가져오기
    $sql1 = "SELECT CONTENT_NUM,DIR,ID FROM publixher.TBL_FOLDER WHERE ID_USER=:ID_USER";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':ID_USER', $targetid, PDO::PARAM_STR);
    $prepare1->execute();
    $FOLDER = $prepare1->fetchAll(PDO::FETCH_ASSOC);
    echo '<hr>폴더목록<ul>';
    for ($i = 0; $i < count($FOLDER); $i++) {
        echo '<li><a href="/folder/' . $FOLDER[$i]['ID'] . '">' . $FOLDER[$i]['DIR'] . '</a>(' . $FOLDER[$i]['CONTENT_NUM'] . ')</li>';
    }

    if ($userID == $targetid) {
        echo "<a href='/folderManage/${targetid}'>폴더 관리</a></ul>";
    } else echo '</ul>';
    if ($userinfo->getLEVEL() == 99) {
        echo <<<END
<hr>관리자 권한
<ul>
    <li id="id-ban-3"><a>로그인 제한(3일)</a></li>
    <li id="id-ban-7"><a>로그인 제한(7일)</a></li>
    <li id="id-ban-30"><a>로그인 제한(30일)</a></li>
    <li id="id-ban-cancel"><a>로그인 제한 풀기</a></li>
</ul>
END;
    }
    ?>

</div>