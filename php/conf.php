<div class="conf">
    <script src="/js/conf.js"></script>
    <div id="logo"><img src="/img/logo.png" onclick="location.href='/'"></div>


    <?php

    $mid = $_SESSION['user']->getID();
    $mname = $_SESSION['user']->getUSERNAME();
    $mage = $_SESSION['user']->getBIRTH();
    $mpic=str_replace('profile','crop24',$_SESSION['user']->getPIC());
    $mpin=$_SESSION['user']->getPIN();
    $mlevel=$_SESSION['user']->getLEVEL();
    $token = $_SESSION['token'];    //토큰
    //나이구하기
    $birthday = date("Y", strtotime($mage)); //생년월일
    $nowday = date('Y'); //현재날짜
    $age = floor($nowday - $birthday); //만나이
    ?>

    <script>
        const token = "<?=$token?>";
        var pin = "<?=$mpin?>";
        const level=<?=$mlevel?>;

    </script>
    <div id="controller">
        <a id="usrpic" href='/profile/<?= $mid ?>'><div class="usrpic-wrap"><img src="<?=$mpic?>"></div></a>
        <!--이름 및 상태전환버튼-->
            <div class="btn-group" id="usr">
                <button type="button" id="username" class="btn btn-danger" role="group" onclick="location.href='/profile/<?= $mid ?>'" style="padding: 0;text-align: center;"><?=$mname?></button>
                <button class="btn btn-danger" role="group" onclick="location.href='/php/data/profileChange.php?action=profileswap'"><span class="pubico pico-swap"></span></button>
            </div>
        <!--노티,핀,설정버튼-->
        <!-- 노티버튼 -->
        <div class="btn-group" id="noti-drop">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" id="notibtn"
                    aria-expanded="false">
                <span class="pubico pico-bell"></span>
            </button>
            <ul class="dropdown-menu" role="menu" id="notilist">
                <li id="li-noticenter"><a href="/notiCenter/<?=$mid?>" style="text-align: center;">알림센터 바로가기</a></li>
            </ul>
        </div>
        <!--핀버튼-->
        <div class="btn-group" id="pin-drop">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" id="pinbtn"
                    aria-expanded="false">
                <span class="pubico pico-Pin_002"></span>
            </button>
            <ul class="dropdown-menu" role="menu" id="pinlist">
                <li></li>
            </ul>
        </div>
        <!--설정버튼-->
        <button type="button" id="configbtn" class="btn btn-default" onclick="location.href='/php/logout.php'">
            <span class="caret"></span>
        </button>
    </div>
    <input id="gsearch" type="text" placeholder="사람, 커뮤니티, 컨텐츠를 검색해 보세요.">
    <span class="pubico pico-search"></span>
    <div class="dropdown">
        <button type="button" id="searchbtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                style="display: none;"></button>
        <ul class="dropdown-menu" id="searchResult" role="menu" aria-labelledby="searchbtn">
            <li class="menu" id="search-body"><a>본문검색으로 바로가기</a></li>
            <li class="menu" id="friendResult"></li>
            <li class="menu" id="contResult"></li>
            <li class="menu" id="nameResult"></li>
            <li class="menu" id="tagResult"></li>
        </ul>
    </div>
</div>
