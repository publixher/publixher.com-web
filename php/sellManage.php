<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>프로필</title>
    <!-- 부트스트랩 -->
    <link href="/plugins/bootstrap-3.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
    <link rel="stylesheet" href="/plugins/Bootstrap-Image-Gallery-master/css/bootstrap-image-gallery.min.css">
    <link rel="stylesheet" href="/css/publixherico/style.css">
    <?php
    function isMobile(){
        $arr_browser = array ("iphone", "android", "ipod", "iemobile", "mobile", "lgtelecom", "ppc", "symbianos", "blackberry", "ipad");
        $httpUserAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        // 기본값으로 모바일 브라우저가 아닌것으로 간주함
        $mobile_browser = false;
        $mobilesize=count($arr_browser);
        // 모바일브라우저에 해당하는 문자열이 있는 경우 $mobile_browser 를 true로 설정
        for($indexi = 0 ; $indexi < $mobilesize ; $indexi++){
            if(strpos($httpUserAgent, $arr_browser[$indexi]) == true){
                $mobile_browser = true;
                break;
            }
        }
        return $mobile_browser;
    }

    if(isMobile()){
        echo '<link href="/css/profile.mobile.css" rel="stylesheet">';
    }else{
        echo '<link href="/css/profile.css" rel="stylesheet">';
    }
    ?>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
    <script src="/plugins/bootstrap-3.3.2/dist/js/bootstrap.min.js"></script>
    <script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
    <script src="/plugins/Bootstrap-Image-Gallery-master/js/bootstrap-image-gallery.min.js"></script>
    <!--    달력 플러그인-->
    <script src="/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="/plugins/bootstrap-datepicker-master/dist/locales/bootstrap-datepicker.kr.min.js"></script>
    <link rel="stylesheet" href="/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css">
    <!--그래프 플러그인-->
    <script src="/plugins/jqPlot-master/src/jquery.jqplot.js"></script>
    <script src="/plugins/jqPlot-master/src/plugins/jqplot.dateAxisRenderer.js"></script>
    <script src="/plugins/jqPlot-master/src/plugins/jqplot.cursor.js"></script>
    <script src="/plugins/jqPlot-master/src/plugins/jqplot.highlighter.js"></script>
    <link rel="stylesheet" href="/plugins/jqPlot-master/src/jquery.jqplot.css">

    <script src="/js/sellManage.js"></script>
    <script src="/js/plugins.js"></script>
    <script src="/js/errorReport.js"></script>

</head>
<body>
<div id="wrap">
    <?php
    require_once '../conf/User.php';
    require_once '../conf/database_conf.php';
    session_start();

    require_once "../lib/loginchk.php";
    //토큰
    //$userinfo는 현재 접속한 유저
    $userinfo = $_SESSION['user'];
    $userID = $userinfo->getID();
    $_GET['id'] = $userID;
    require "profile_left.php";
    //중간
    ?>
    <div id="middle">
        <!--        버튼 3개 선택-->
        <div id="button-list">
            <button class="btn btn-default" type="button" id="late-btn">최신순</button>
            <button class="btn btn-default" type="button" id="sell-btn">판매순</button>
            <button class="btn btn-default" type="button" id="money-btn">매출순</button>
        </div>
        <!--        정렬별 최고 순위-->
        <div id="most-content">

        </div>
        <!--        아이템 한개 세부 판매내역-->
        <div id="cms-item">

        </div>
        <!--        기간별 cms-->
        <div id="cms-date-wrapper">
            <div id="cms-date">
                <!--            datepicker-->
                <div class="span5 col-md-5" id="sandbox-container">
                    <div class="input-daterange input-group" id="datepicker">
                        <input type="text" class="input-sm form-control" name="start" id="start_date">
                        <span class="input-group-addon">to</span>
                        <input type="text" class="input-sm form-control" name="end" id="end_date">
                    </div>
                </div>
                <div id="cms-result">

                </div>
            </div>
        </div>
    </div>
    <?php
    //오른쪽
    require "right.php";
    ?>
    <!--    구글 애널리틱스-->
    <script>
        const mid='<?=$userID?>';
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)   })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');    ga('create', 'UA-73277050-2', 'auto');   ga('send', 'pageview');  </script>
</div>
</body>
<script>
    //달력 소스
    $('#sandbox-container .input-daterange').datepicker({
        language: "kr",
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy년 mm월 dd일'
    });
</script>
</html>
