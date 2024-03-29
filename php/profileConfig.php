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
    <link rel="stylesheet" href="/css/publixherico/style.css">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
    <script src="/plugins/bootstrap-3.3.2/dist/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.2/jquery.fileupload.min.js"></script>
    <script src="/js/plugins.js"></script>
    <script src="/js/profileConfig.js"></script>
    <script src="/js/errorReport.js"></script>

    <style>
        #idwrong, #pwwrong, #pwcheckwrong, #namewrong {
            display: none;
            height: 26px;
            width: 100%;
            text-align: center;
            margin: 0;
            padding: 5px 5px 5px 5px;
        }
    </style>
</head>
<body>
<div id="wrap">
    <?php
    require_once'../conf/getTarget.php';
    //왼쪽
    include_once "../lib/loginchk.php";
    require "profile_left.php";
    //중간
    echo '<div id="middle">';
    //폴더목록 가져오기
    $userID = $userinfo->getID();
    $usermail = $userinfo->getEMAIL();
    $userpw = $userinfo->getPASSWORD();
    $username = $userinfo->getUSERNAME();
    $usersex = $userinfo->getSEX();
    $userbirth = $userinfo->getBIRTH();
    $userregion = $userinfo->getREGION();
    $userhschool = $userinfo->getHSCHOOL();
    $useruniv = $userinfo->getUNIV();
    $userpic = $userinfo->getPIC();
    $usercom=$userinfo->getCOMMUNITY();
    $birth = date("Y-m-d", strtotime($target['BIRTH']));
    $birth=explode('-',$birth);
    if ($userinfo->getISNICK() == 'N') {
        $sql3 = "SELECT ID_ANONY FROM publixher.TBL_CONNECTOR WHERE ID_USER=:ID_USER";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue('ID_USER', $userID, PDO::PARAM_STR);
        $prepare3->execute();
        $anonyID = $prepare3->fetch(PDO::FETCH_ASSOC);
        $sql4 = "SELECT USER_NAME FROM publixher.TBL_USER WHERE ID=:ID";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue('ID', $anonyID['ID_ANONY'], PDO::PARAM_STR);
        $prepare4->execute();
        $nickname = $prepare4->fetch(PDO::FETCH_ASSOC);
        $nickname = $nickname['USER_NAME'];
    }
    ?>
    <script>
        var useryear='<?=$birth[0]?>';
        var usermonth='<?=$birth[1]?>';
        var userday='<?=$birth[2]?>';
    </script>

    <form method='post' action='/php/data/profileChange.php' id="pf">
        <input type="hidden" name="action" value="profilechange">
        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        <table>
            <tr>
                <td>아이디<br>(이메일)</td>
                <td><input type='text' class='form-control' name='email' tabindex='1' id="mid" value="<?= $usermail ?>"
                           disabled/></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="idwrong"></div>

                </td>
            </tr>
            <tr>
                <td>비밀번호</td>
                <td><input type='password' class='form-control' name='pass' tabindex='2' id="mpass"/></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="pwwrong"></div>
                </td>
            </tr>
            <tr>
                <td>비밀번호 확인</td>
                <td><input type="password" class='form-control' id="mpasscheck" tabindex="3"></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="pwcheckwrong"></div>
                </td>
            </tr>
            <tr>
                <td>이름</td>
                <td><input type='text' name='name' class='form-control' tabindex='4' id="mname" value="<?= $username ?>"
                           disabled/></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="namewrong"></div>
                </td>
            </tr>
            <tr>
                <td>생년월일<br>
                    <p style="font-size: 10px;">ex)19920318</p></td>
                <td><select name="byear" tabindex="10" id="years" class='form-control'
                            style="width: 65px;display:inline"></select>
                    <select name="bmonth" tabindex="11" id="months" class='form-control'
                            style="width: 50px;display:inline"></select>
                    <select name="bday" tabindex="12" id="days" class='form-control'
                            style="width: 50px;display:inline"></select>
                </td>
            </tr>
            <tr>
                <td>고등학교</td>
                <td><input type='text' name='hschool' class='form-control' tabindex='6' id="mhschool"
                           value="<?= $userhschool ?>"/></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="namewrong"></div>
                </td>
            </tr>
            <tr>
                <td>대학교</td>
                <td><input type='text' name='univ' class='form-control' tabindex='7' id="muniv"
                           value="<?= $useruniv ?>"/></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="namewrong"></div>
                </td>
            </tr>
            <tr>
                <td>국적</td>
                <td>
                    <select class="form-control" name='region' tabindex='8' id='mregion'>
                        <option value='KOR'>한국</option>
                        <option value='USA'>미국</option>
                        <option value='JPN'>일본</option>
                    </select>
                </td>
                <td>
                    <div class="alert alert-danger" role="alert" id="namewrong"></div>
                </td>
            </tr>
            <td colspan="2"><input type="submit" class="btn btn-default" value="수정하기" id="submit"></td>
            </tr>
            <tr style="margin-bottom: 10px;">
                <td>프로필 사진</td>
                <td id="picreg">
                    <img src="<?= $userpic ?>" class="file-input-img"/>
                    <input type="file" id="fileuploads" name="fileuploads[]" accept="image/*"
                           data-url="/php/data/fileUp.php"
                           class="fileupform"></td>
            </tr>
        </table>
    </form>
<?php
    if ($userinfo->getISNICK() == 'N' && $userinfo->getCOMMUNITY()==0) { ?>
        <form method='post' action='/php/data/profileChange.php'>
            <input type="hidden" name="action" value="anonyregist">
            <span>현재 익명 : </span><span><?= $nickname ?></span>
            <input type="text" class="form-control" name="nick" placeholder="익명은 한글,영문,숫자만으로 구성되어야 합니다."
                   style="width:60%;">
            <input type="submit" class="btn btn-default" value="지금 익명계정을 삭제하고 새로운 익명계정 생성">
        </form>

        <div class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            새로운 익명계정을 생성할 경우 기존의 익명계정으로 더이상 활동할 수 없습니다. 하지만 익명계정으로 작성한 모든 컨텐츠는 삭제되지 않습니다.
        </div>
    <?php }

    echo '</div>';
    //오른쪽
    require "right.php";
    ?>
    <!--    구글 애널리틱스-->
    <script>   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)   })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');    ga('create', 'UA-73277050-2', 'auto');   ga('send', 'pageview');  </script>
</div>
</body>
</html>
