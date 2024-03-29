<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>analograph</title>
    <!-- 부트스트랩 -->
    <link href="/plugins/bootstrap-3.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
    <link rel="stylesheet" href="/plugins/Bootstrap-Image-Gallery-master/css/bootstrap-image-gallery.min.css">
    <link rel="stylesheet" href="/css/publixherico/style.css">
    <link rel="stylesheet" href="/plugins/loader-master/loaders.min.css">
    <?php
    function isMobile()
    {
        $arr_browser = array("iphone", "android", "ipod", "iemobile", "mobile", "lgtelecom", "ppc", "symbianos", "blackberry", "ipad");
        $httpUserAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        // 기본값으로 모바일 브라우저가 아닌것으로 간주함
        $mobile_browser = false;
        $mobilesize = count($arr_browser);
        // 모바일브라우저에 해당하는 문자열이 있는 경우 $mobile_browser 를 true로 설정
        for ($indexi = 0; $indexi < $mobilesize; $indexi++) {
            if (strpos($httpUserAgent, $arr_browser[$indexi]) == true) {
                $mobile_browser = true;
                break;
            }
        }
        return $mobile_browser;
    }

    if (isMobile()) {
        echo '<link href="/css/main.mobile.css" rel="stylesheet">';
        echo '<meta name="theme-color" content="#' . dechex(rand(0x000000, 0xFFFFFF)) . '">';
    } else {
        echo '<link href="/css/main.css" rel="stylesheet">';
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
    <script src="https://static.nid.naver.com/js/naverLogin_implicit-1.0.1-min.js"></script>
    <script src="/js/plugins.js"></script>
    <script src="/js/errorReport.js"></script>

</head>
<body>

<div id="wrap">
    <?php
    require_once "../conf/User.php";
    session_start();
    require_once "../lib/loginchk.php";
    require "left.php";
    require "middle.php";
    require "right.php";
    require "itemModModal.php";
    require "ImageGallery.php";
    ?>

    <!--    구글 애널리틱스-->
    <script>   (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o), m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-73277050-2', 'auto');
        ga('send', 'pageview');  </script>
    <button id="call-left">subscription</button>
    <button id="call-right">popular</button>
    <script>
        $("#call-left").on('touchstart', function () {
            $('.market').css('display', 'none');
            $('#call-right').removeClass('right-called');
            if($(this).hasClass("left-called")){
                $('#left').css('display', 'none');
                $('#middle').css('display', 'block');
                $(this).removeClass('left-called');
            }else {
                $('#left').css('display', 'block');
                $('#middle').css('display', 'none');
                $(this).addClass('left-called');
            }
        })
        $("#call-right").on('touchstart', function () {
            $('#left').css('display', 'none');
            $('#call-left').removeClass('left-called');
            if($(this).hasClass("right-called")){
                $('.market').css('display', 'none');
                $('#middle').css('display', 'block');
                $(this).removeClass('right-called');
            }else {
                $('.market').css('display', 'block');
                $('#middle').css('display', 'none');
                $(this).addClass('right-called');
            }
        })
    </script>
</div>
</body>
</html>