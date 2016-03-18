<div id="left">
    <?php
    require_once'../conf/getTarget.php';
    $profilepic = $target['PIC'] ? $target['PIC'] : '/sample/12742491_1065271350203874_7743765745963739525_n.jpg';
    $birth = date("Y년m월d일", strtotime($target['BIRTH']));
    $userinfo=$_SESSION['user'];
    $userseq = $userinfo->getSEQ();
    echo "<div id='profilepic' style='width: 160px; height: 160px;background-image: url(${profilepic});background-size: 160px 160px'></div>";
    //스크립트 올리기?>
    <script>
        var targetid =<?=$targetid?>;
        var myseq =<?=$userseq?>;
        var mid=myseq;
    </script>
    <script src="/js/profile_left.js"></script>
<p id="name"><?=$target['USER_NAME']?></p>
<ul class="list-unstyled" id="profile">
        <li>생일 : <?=$birth?></li>
        <li>대학교 : <?=$target['UNIV']?></li>
        <li>고등학교 : <?=$target['H_SCHOOL']?></li>
    <?php
    if ($userinfo->getSEQ() == $targetid) {
        //현재 접속자와 타겟 유저가 같을때의 동작
        echo "<a href='profileConfig.php?id=${targetid}' id='profileMod'>정보 수정하기</a></ul><hr>";
        //친구요청(SEQ_FRIEND에 내 아이디가 들어가 있고 ALLOWED가 N인것들의 수와 목록을 보여주는것)
        try {
            $sql2 = "SELECT SEQ_USER,SEQ FROM publixher.TBL_FRIENDS WHERE (SEQ_FRIEND=:SEQ_FRIEND AND ALLOWED='N')";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':SEQ_FRIEND', $targetid, PDO::PARAM_STR);
            $prepare2->execute();
            $friendrequest = $prepare2->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $frequestnum = count($friendrequest);
        ?>
<button class="dropdown-toggle" data-toggle="dropdown" href="#"
                role="button" aria-expanded="false" class="btn btn-default">
            <span id="frequestul">친구신청 목록(<span id="frequestnum"><?=$frequestnum?></span>)</span> <span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu" id="frequestli" style="top:321px;">
<?php
        if($frequestnum==0){
            echo '<li>친구요청이 없습니다</li>';
        }elseif($frequestnum>6){
            $fsql = "SELECT USER_NAME FROM publixher.TBL_USER WHERE SEQ=:SEQ";
            $fprepare=$db->prepare($fsql);
            for($i=0;$i<5;$i++){
                $fprepare->bindValue(':SEQ',$friendrequest[$i]['SEQ_USER'],PDO::PARAM_STR);
                $fprepare->execute();
                $reqname=$fprepare->fetch(PDO::FETCH_ASSOC);
                echo "<li><a href='profile.php?id=".$friendrequest[$i]['SEQ_USER']."' class='nameuser'>".$reqname['USER_NAME']."</a> <a requestid='".$friendrequest[$i]['SEQ']."' fid='".$friendrequest[$i]['SEQ_USER']."' class='friendok'>O</a> <a requestid='".$friendrequest[$i]['SEQ']."' class='friendno'>X</a></li>";
            }
            echo "<li><a href='friendRequest.php'>더보기</a></li>";
        }else{
            $fsql = "SELECT USER_NAME FROM publixher.TBL_USER WHERE SEQ=:SEQ";
            $fprepare=$db->prepare($fsql);
            for($i=0;$i<$frequestnum;$i++){
                $fprepare->bindValue(':SEQ',$friendrequest[$i]['SEQ_USER'],PDO::PARAM_STR);
                $fprepare->execute();
                $reqname=$fprepare->fetch(PDO::FETCH_ASSOC);
                echo "<li><a href='profile.php?id=".$friendrequest[$i]['SEQ_USER']."' class='nameuser'>".$reqname['USER_NAME']."</a> <a requestid='".$friendrequest[$i]['SEQ']."' fid='".$friendrequest[$i]['SEQ_USER']."' class='friendok'>O</a> <a requestid='".$friendrequest[$i]['SEQ']."' class='friendno'>X</a></li>";
            }
        }
            echo '</ul>';
            ?>
        <?php
        //가진돈 찾기위해 커넥터를 통해 캐쉬를 찾음
        $sql1 = '';
        if ($userinfo->getISNICK() == "N") {
            $sql1 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE SEQ_USER=:SEQ_TARGET";
        } else if ($userinfo->getISNICK() == "Y") {
            $sql1 = "SELECT CASH_POINT FROM publixher.TBL_CONNECTOR WHERE SEQ_ANONY=:SEQ_TARGET";
        }
        $prepare1 = $db->prepare($sql1);
        try {
            $prepare1->bindValue(':SEQ_TARGET', $userinfo->getSEQ(), PDO::PARAM_STR);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $prepare1->execute();
        $cash = $prepare1->fetch(PDO::FETCH_ASSOC);
        $cash = $cash['CASH_POINT'];

?>
<ul class="list-unstyled" id="activity">
        <li><a><?=$cash?> pigs</a></li>
        <li><a>충전 &middot 결제정보</a></li>
        <li><a href="/php/buyList.php?id=${userseq}">구매목록</a></li>
        <li><a>판매관리</a></li>
        </ul>
<?php
        //폴더목록 가져오기
        $sql1 = "SELECT CONTENT_NUM,DIR,SEQ FROM publixher.TBL_FORDER WHERE SEQ_USER=:SEQ_USER";
        $prepare1 = $db->prepare($sql1);
        $prepare1->bindValue(':SEQ_USER', $targetid, PDO::PARAM_STR);
        $prepare1->execute();
        $forder = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        echo '<hr>폴더목록<ul>';

        for ($i = 0; $i < count($forder); $i++) {
            echo '<li><a href="foldercon.php?fid=' . $forder[$i]['SEQ'] . '">' . $forder[$i]['DIR'] . '</a>(' . $forder[$i]['CONTENT_NUM'] . ')</li>';
        }
        echo "<a href='forderConfig.php?id=${targetid}'>폴더 관리</a>";
    } else {
        //다를때
        //친구신청
        $sql2="SELECT ALLOWED FROM publixher.TBL_FRIENDS WHERE (SEQ_FRIEND=:SEQ_FRIEND AND SEQ_USER=:SEQ_USER)";
        $prepare2=$db->prepare($sql2);
        $prepare2->bindValue('SEQ_FRIEND',$targetid,PDO::PARAM_STR);
        $prepare2->bindValue('SEQ_USER',$userseq,PDO::PARAM_STR);
        $prepare2->execute();
        $allowed=$prepare2->fetch(PDO::FETCH_ASSOC);
        if(!$allowed['ALLOWED']) {
            echo '<a id="friendRequest" href="#">친구가 되고싶어!!</a><hr>폴더목록<ul>';
        }elseif($allowed['ALLOWED']=='N'){
            echo '<a id="alreadyRequest">이미 친구신청을 했네요</a><hr>폴더목록<ul>';
        }
        //폴더목록 보기
        $sql1 = "SELECT CONTENT_NUM,DIR FROM publixher.TBL_FORDER WHERE SEQ_USER=:SEQ_USER";
        $prepare1 = $db->prepare($sql1);
        $prepare1->bindValue(':SEQ_USER', $targetid, PDO::PARAM_STR);
        $prepare1->execute();
        $forder = $prepare1->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < count($forder); $i++) {
            echo '<li><a href="#">' . $forder[$i]['DIR'] . '</a>(' . $forder[$i]['CONTENT_NUM'] . ')</li>';
        }
        echo '</ul>';
    }

    ?>

</div>