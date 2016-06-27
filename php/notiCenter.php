<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>analograph</title>
    <!-- 부트스트랩 -->
    <link href="/plugins/bootstrap-3.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/publixherico/style.css">
    <link href="/css/profile.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
    <script src="/plugins/bootstrap-3.3.2/dist/js/bootstrap.min.js"></script>
    <script src="/js/plugins.js"></script>
    <script src="/js/notiCenter.js"></script>
    <script src="/js/errorReport.js"></script>

</head>
<body>
<div id="wrap">
    <?php
    require_once'../conf/User.php';
    require_once'../conf/database_conf.php';
    session_start();
    require_once '../lib/loginchk.php';
    //$userinfo는 현재 접속한 유저
    $userinfo = $_SESSION['user'];
    $userID = $userinfo->getID();
    require "profile_left.php";
    //중간
    echo '<div id="middle"><ul id="listul"></ul><div style="text-align: center"><a id="notimore" style="cursor: pointer;">더 보기</a></div>';
    echo '</div>';
    //오른쪽
    require "right.php";
    ?>
    <!--    구글 애널리틱스-->
    <script>   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)   })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');    ga('create', 'UA-73277050-2', 'auto');   ga('send', 'pageview');  </script>
</div>
</body>
</html>
