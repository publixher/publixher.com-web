<div class="conf">
    <script src="/js/conf.js"></script>
    <div id="logo"><a href="/">로고</a></div>


    <?php

    $mid = $_SESSION['user']->getSEQ();
    $mname = $_SESSION['user']->getUSERNAME();
    $mage = $_SESSION['user']->getBIRTH();
    $mpic=$_SESSION['user']->getPIC();
    $token = $_SESSION['token'];    //토큰
    $agen = $_SESSION['age']; //브라우저 정보
    //나이구하기
    $birthday = date("Y", strtotime($mage)); //생년월일
    $nowday = date('Y'); //현재날짜
    $age = floor($nowday - $birthday); //만나이
    ?>

    <script>
        var mid = "<?=$mid?>"
        var token = "<?=$token?>";
        var age = "<?=$agen?>";
    </script>
    <div id="controller">
        <a id="usrpic" href='/php/profile.php?id=<?= $mid ?>'><img src="<?=$mpic?>" style="width: 40px; height: 40px;"></a>
        <!--이름 및 상태전환버튼-->
            <div class="btn-group" id="usr">
                <button type="button" id="username" class="btn btn-danger" role="group" onclick="location.href='/php/profile.php?id=<?= $mid ?>'" style="padding: 0;text-align: center;"><?=$mname?></button>
                <button class="btn btn-danger" role="group" onclick="location.href='/php/data/profileChange.php?action=profileswap'"><span class="pubico pico-swap"></span></button>
            </div>
        <!--노티,핀,설정버튼-->
        <!-- 노티버튼 -->
        <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" id="notibtn"
                    aria-expanded="false">
                <span class="pubico pico-bell"></span>
            </button>
            <ul class="dropdown-menu" role="menu" id="notilist">
                <li id="li-noticenter"><a href="/php/notiCenter.php?id=<?=$mid?>" style="text-align: center;">알림센터 바로가기</a></li>
            </ul>
        </div>
        <!--핀버튼-->
        <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"
                    aria-expanded="false">
                <span class="pubico pico-pin2"></span>
            </button>
            <ul class="dropdown-menu" role="menu" id="pinlist">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li class="seemore"><a href="#">더보기...</a></li>
            </ul>
        </div>
        <!--설정버튼-->
        <button type="button" id="configbtn" class="btn btn-default" onclick="location.href='/php/logout.php'">
            <span class="pubico pico-cog"></span>
        </button>
    </div>
    <input id="gsearch" type="text" placeholder="사람,커뮤니티,익명,컨텐츠를 검색해 보세요...">
    <div class="dropdown">
        <button type="button" id="searchbtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                style="display: none;"></button>
        <ul class="dropdown-menu" id="searchResult" role="menu" aria-labelledby="searchbtn">
            <li class="menu"><a>본문검색으로 바로가기</a></li>
            <li class="menu" id="contResult"></li>
            <li class="menu" id="nameResult"></li>
            <li class="menu" id="tagResult"></li>
        </ul>
    </div>
</div>
