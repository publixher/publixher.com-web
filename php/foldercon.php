<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>프로필</title>
    <!-- 부트스트랩 -->
    <link href="/plugins/bootstrap-3.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
    <link rel="stylesheet" href="/plugins/Bootstrap-Image-Gallery-master/css/bootstrap-image-gallery.min.css">
    <link href="/css/profile.css" rel="stylesheet">
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
    <script src="/js/plugins.js"></script>
    <script src="/js/foldercon.js"></script>
    <!--    <script src="../js/regist.js"></script>-->
</head>
<body>
<div id="wrap">
    <?php
    require_once'../conf/User.php';
    require_once'../conf/database_conf.php';
    session_start();

    include_once "../lib/loginchk.php";
    //세션에 mid가 없으면 로그인페이지로 넘기고 있으면 유저 등록
    if (!isset($_SESSION['user'])) {
        echo "<meta http-equiv='refresh' content='0;url=/php/login.php'>";
        exit;
    }
    //$userinfo는 현재 접속한 유저
    $userinfo = $_SESSION['user'];
    $fid = $_GET['fid'];
    echo "<script>var fid=${fid};</script>";
    //폴더의 소유자 찾아오기
    $sql1 = "SELECT SEQ_USER FROM publixher.TBL_FORDER WHERE SEQ=:SEQ";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue('SEQ', $fid, PDO::PARAM_STR);
    $prepare1->execute();
    $folderuser = $prepare1->fetch(PDO::FETCH_ASSOC);
    $userseq = $userinfo->getSEQ();
    $_GET['id']=$folderuser['SEQ_USER'];
    include "profile_left.php";

    //중간
    echo '<div id="middle"><span id="prea"></span>';
    //폴더 내용물들 가져오기(j쿼리를 이용해서 비동기식으로 가져온다)
    echo '</div>';
    //오른쪽
    include "right.php";
    ?>
    <!--    구글 애널리틱스-->
    <script>(function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o), m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-73277050-1', 'auto');
        ga('send', 'pageview');</script>
</div>
</body>
</html>
