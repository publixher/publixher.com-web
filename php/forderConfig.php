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
</head>
<body>
<script>
    $(document).on("click", ".deletefolder", function (e) {
        var thisfolder = $(this).attr('folderid');
        $.ajax({
            url: "/php/data/profileChange.php",
            type: "POST",
            data: { action: "deletefolder", userseq: mid,folderid:thisfolder},
            dataType: 'json',
            success: function (res) {
                alert(res['message']);
                location.reload();
            }, error: function (request) {
                alert(request.responseText);
            }
        })
    });
</script>
<div id="wrap">
    <?php

    require_once'../conf/getTarget.php';
    if (($_COOKIE['cid'] != '')) {
        setcookie('cid', $_COOKIE['cid'], time() + 3600 * 24 * 365, '/');
        //쿠키있으면 로그인
        include_once '../conf/database_conf.php';
        $loginsql = "SELECT * FROM publixher.TBL_USER WHERE SEQ=:SEQ";
        $loginprepare=$db->prepare($loginsql);
        $loginprepare->bindValue(':SEQ',$_COOKIE['cid'],PDO::PARAM_STR);
        $loginprepare->execute();
        $user = $loginprepare->fetchObject(User);

        $_SESSION['user'] = $user;
        //세션토큰 생성(CSRF등 대책)
        if(!isset($_SESSION['token'])){
            $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
        }
    }
    //세션에 mid가 없으면 로그인페이지로 넘기고 있으면 유저 등록
    if (!isset($_SESSION['user'])) {
        echo "<meta http-equiv='refresh' content='0;url=/php/login.php'>";
        exit;
    }
    $userseq = $userinfo->getSEQ();
    //왼쪽
    include "profile_left.php";
    //중간
    echo '<div id="middle">';
    //폴더목록 가져오기
    $sql1 = "SELECT CONTENT_NUM,DIR,SEQ FROM publixher.TBL_FORDER WHERE SEQ_USER=:SEQ_USER";
    $prepare1 = $db->prepare($sql1);
    $prepare1->bindValue(':SEQ_USER', $targetid, PDO::PARAM_STR);
    $prepare1->execute();
    $forder = $prepare1->fetchAll(PDO::FETCH_ASSOC);
    echo '폴더목록<ul>';

    for ($i = 0; $i < count($forder); $i++) {
        echo '<li><a href="foldercon.php?fid=' .$forder[$i]['SEQ']. '">' . $forder[$i]['DIR'] . '</a>(' . $forder[$i]['CONTENT_NUM'] . ')  <button class="btn btn-danger deletefolder" folderid="' . $forder[$i]['SEQ'] . '" style="width:15px;padding:0 0 0 0;">X</button></li>';
    }
    echo '</ul>';
    ?>
    <form method='post' action='/php/data/profileChange.php'>
        <input type="hidden" name="action" value="newfolder">
        <input type="text" class="form-control" placeholder="새 폴더" name="folder">
        <input class="btn btn-primary" type="submit" value="새 분류 만들기">
    </form>
</div>
<?
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
