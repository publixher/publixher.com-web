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
    <script src="/js/plugins.js"></script>
    <script src="/js/errorReport.js"></script>
    <script>
        //페이스북 SDK 초기화
        window.fbAsyncInit = function () {
            FB.init({
                appId: '143041429433315',
                status: true,
                xfbml: true,
                version: 'v2.6'
            })
            ;
        };

        (function (d) {
            var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement('script');
            js.id = id;
            js.async = true;
            js.src = "//connect.facebook.net/ko_kr/all.js";
            ref.parentNode.insertBefore(js, ref);
        }(document));
        function getFacebookFriend() {
            FB.getLoginStatus(function (response) {
                statusChangeCallback(response);
            });
        }
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // 페이스북을 통해서 로그인이 되어있다.
                FB.api('/me/friends', function (response) {
                    friendSearch(response['data'])
                });
            } else if (response.status === 'not_authorized') {
                // 페이스북에는 로그인 했으나, 앱에는 로그인이 되어있지 않다.
                FB.login(function (response) {
                    FB.api('/me?fields=friends,id', function (response) {
                        console.log(response)
                        registFacebookId(response.id,mid,friendSearch,response['friends']['data'])
                    });
                }, {scope: 'user_friends'});
            } else {
                // 페이스북에 로그인이 되어있지 않다. 따라서, 앱에 로그인이 되어있는지 여부가 불확실하다.
                FB.login(function (response) {
                    FB.api('/me?fields=friends,id', function (response) {
                        console.log(response)
                        registFacebookId(response['id'],mid,friendSearch,response['friends']['data'])

                    });
                }, {scope: 'user_friends'});
            }
        }
        function friendSearch(list){
            $.ajax({
                url:'/php/data/friendSearch.php',
                type:'GET',
                data:{list:list,action:'recommend'},
                success:function(res){
                    var list=$('#recommended-friend');
                    for(var i=0;i<res.length;i++){
                        $('<li>').append(
                            $('<div>').addClass('friend-list-pic-wrap')
                                .append($('<img>').attr('src',res[i]['PIC']))
                            ,$('<a>').attr('href','/profile/'+res[i]['ID']).addClass('nameuser').text(res[i]['USER_NAME'])
                            ,$('<button>').text('친구신청')
                        ).appendTo(list)
                    }
                }
            })
        }
        function registFacebookId(facebookid,mid,callback,list){
            $.ajax({
                url:'/php/data/friendSearch.php',
                type:'POST',
                data:{facebook_id:facebookid,mid:mid,action:'registFacebookId'},
                success:function(){
                    if(callback!==undefined){
                        callback(list)
                    }
                }
            })
        }
    </script>
</head>
<body>
<div id="wrap">
    <?php
    include_once "../conf/User.php";
    session_start();
    include_once "../lib/loginchk.php";
    require_once "profile_left.php";
    require_once "profile_middle.php";
    require_once "itemModModal.php";
    require_once "right.php";
    require_once "ImageGallery.php";
    ?>
    <!--    구글 애널리틱스-->
    <script>   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)   })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');    ga('create', 'UA-73277050-2', 'auto');   ga('send', 'pageview');  </script>
</div>
</body>
</html>
