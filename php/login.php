<!DOCTYPE html>
<html lang="ko">
<head>
    <!--    회원가입 막아놓은것-->
    <!--    <meta http-equiv='refresh' content='0;url=/php/login.php'>-->
    <!--    <meta charset="utf-8">-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>Publixher</title>
    <!-- 부트스트랩 -->
    <link href="/plugins/bootstrap-3.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="/plugins/jquery.min.js" type="text/javascript"></script>
    <!-- 모든 컴파일된 플러그인을 포함합니다 (아래), 원하지 않는다면 필요한 각각의 파일을 포함하세요 -->
    <script src="/plugins/bootstrap-3.3.2/dist/js/bootstrap.min.js"></script>
    <script src="/js/plugins.js"></script>
    <style>
        body{
            background-color: #e5e5e5;
        }
        #idwrong, #pwwrong, #pwcheckwrong, #namewrong {
            display: none;
            height: 26px;
            width: 340px;
            text-align: center;
            margin: 0;
            padding: 5px 5px 5px 5px;
        }

        #center {
            position: absolute;
            top: 50%;
            left: 50%;
            overflow: hidden;
            margin-top: -150px;
            margin-left: -100px;
        }
    </style>
</head>
<body>
<script src="/js/regist.js"></script>
<div id="center">
    <form method='post' action='/php/data/loginConfirm.php'>
        <table>
            <tr>
                <td>아이디</td>
                <td><?php if ($_COOKIE['cid'] != '') {
                        echo "<input type='text' name='email' tabindex='1' class='form-control'/>";
                    } else {
                        echo "<input type='text' name='email' tabindex='1' value='{$cmail}' class='form-control'/>";
                    } ?></td>
                <td><input type='submit' tabindex='3' value='로그인' class="btn btn-default"/></td>
            </tr>
            <tr>
                <td>비밀번호</td>
                <td><input type='password' name='pass' tabindex='2' class='form-control'/></td>
<!--                <td colspan="3"><input type="checkbox" name="dont_remem" tabindex="4">날 기억하지 마세요</td>-->
            </tr>
        </table>
    </form>
    <br>
    <form method='post' action='/php/data/registConfirm.php' id="rf">
        <table>
            <tr>
                <td>아이디<br>(이메일)</td>
                <td><input type='text' name='email' tabindex='5' id="mid" class='form-control'/></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="idwrong"></div>

                </td>
            </tr>
            <tr>
                <td>비밀번호</td>
                <td><input type='password' name='pass' tabindex='6' id="mpass" class='form-control'/></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="pwwrong"></div>
                </td>
            </tr>
            <tr>
                <td>비밀번호 확인</td>
                <td><input type="password" id="mpasscheck" tabindex="7" class='form-control'></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="pwcheckwrong"></div>
                </td>
            </tr>
            <tr>
                <td>이름</td>
                <td><input type='text' name='name' tabindex='8' id="mname" class='form-control'/></td>
                <td>
                    <div class="alert alert-danger" role="alert" id="namewrong"></div>
                </td>
            </tr>
            <tr>
                <td>성별</td>
                <td><select name='sex' tabindex="9" class='form-control'>
                        <option value="m">남자</option>
                        <option value="f">여자</option>
                    </select></td>
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
            <td colspan="2"><input type="submit" value="가입하기" id="submit" class="btn btn-default" tabindex="13"
                                   class='form-control'></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>