<!DOCTYPE html>
<html lang="ko">
<head>
    <!--    회원가입 막아놓은것-->
    <!--    <meta http-equiv='refresh' content='0;url=/php/login.php'>-->
    <!--    <meta charset="utf-8">-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
    <title>analograph</title>
    <!-- 부트스트랩 -->
    <link href="/https/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/https/publixherico/style.css">
    <link rel="stylesheet" href="/https/login.css">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
    <script src="/https/jquery.min.js" type="text/javascript"></script>
    <script src="https://static.nid.naver.com/js/naverLogin_implicit-1.0.2-min.js"></script>
    <!-- 모든 컴파일된 플러그인을 포함합니다 (아래), 원하지 않는다면 필요한 각각의 파일을 포함하세요 -->
    <script src="/https/bootstrap.min.js"></script>
    <script src="/https/naver.js"></script>
    <script src="/https/plugins.js"></script>
</head>
<body>

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

    function facebooklogin() {
        FB.getLoginStatus(function (response) {
            statusChangeCallback(response);
        });
    }
    function statusChangeCallback(response) {
        if (response.status === 'connected') {
            // 페이스북을 통해서 로그인이 되어있다.
            FB.api('/me?fields=email', function (response) {
                $.ajax({
                    url: "/php/data/api_login.php",
                    type: "POST",
                    data: {email: response.email, api: "facebook", action: "login"},
                    dataType: 'json',
                    success: function (res) {
                        location.href = '/';
                    }, error: function (request, status, error) {
                        console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error)
                        alert('작업중 문제가 생겼습니다.');
                        location.href = '/https/login.php';
                    }
                })
            });
        } else if (response.status === 'not_authorized') {
            // 페이스북에는 로그인 했으나, 앱에는 로그인이 되어있지 않다.
            FB.login(function (response) {
                FB.api('/me?fields=id,name,picture.width(160).height(160).as(profile_picture),email,gender,birthday,locale', {locale: 'ko_KR'}, function (response) {
                    function replaceAll(str, searchStr, replaceStr) {
                        return str.split(searchStr).join(replaceStr);
                    }

                    var gender = response.gender == '남성' ? 'M' : 'F';
                    var profile_image = replaceAll(response.profile_picture.data.url, "\"", "");
                    var format_date;
                    if (response.birthday) {
                        var date = new Date(response.birthday);
                        var d = date.getDate();
                        var m = date.getMonth() + 1;
                        var y = date.getFullYear();
                        format_date = y + '-' + m + '-' + d;
                    } else {
                        format_date = '1800-01-01';
                    }
                    $.ajax({
                        url: "/php/data/api_login.php",
                        type: "POST",
                        data: {
                            id: response.id,
                            email: response.email,
                            birthday: format_date,
                            gender: gender,
                            image: profile_image,
                            name: response.name,
                            locale: response.locale,
                            api: "facebook",
                            action: 'reg'
                        },
                        dataType: 'json',
                        success: function (res) {
                            location.href = '/';
                        }, error: function () {
                            alert('작업중 문제가 생겼습니다.');
                            location.href = '/https/login.php';
                        }
                    })
                });
            }, {scope: 'public_profile,email,user_birthday,user_friends'});
        } else {
            // 페이스북에 로그인이 되어있지 않다. 따라서, 앱에 로그인이 되어있는지 여부가 불확실하다.
            FB.login(function (response) {
            }, {scope: 'public_profile,email,user_birthday,user_friends'});
        }
    }
</script>

<script src="/https/regist.js"></script>
<div id="mask">
</div>
<div id="center">
    <div id="login-form">
    <form method='post' action='/https/loginConfirm.php'>
        <table>
            <tr>
                <td>
                    <input type='text' name='email' tabindex='1' class='form-control' placeholder="email"/>
                </td>

            </tr>
            <tr>
                <td><input type='password' name='pass' tabindex='2' class='form-control' placeholder="password"/></td>
            </tr>
            <tr>
                <td><input type='submit' tabindex='3' value='로그인' class="btn btn-default"/></td>
            </tr>
        </table>
    </form>
    <button type="button" id="find-id" class="btn btn-default">비밀번호 찾기</button>
    <br>
    <div id="api_login">
        <div id="naver_id_login"></div>
        <script>
            var naver_id_login = new naver_id_login("OJ9jBISrQELVlxFNyHlz", "http://analograph.com/php/naver_login.php");
            naver_id_login.setButton("white", 3, 40)
            //            naver_id_login.setPopup();
            naver_id_login.setDomain(".analograph.com");
            naver_id_login.setState("");
            naver_id_login.init_naver_id_login();
        </script>
        <div onclick="facebooklogin()" id="facebook_id_login"><img src="/img/facebook.png"></div>
    </div>
    </div>
    <div id="r-div">
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
                            <option value="M">남자</option>
                            <option value="F">여자</option>
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
                <tr>
                    <td>
                        <input type="checkbox" name="community">커뮤니티로 생성하기
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="가입하기" id="submit" class="btn btn-default" tabindex="13"
                                           class='form-control'></td>
                </tr>

            </table>
        </form>
        <p>회원가입과 동시에 사용자는 analograph의 <a data-toggle="modal" data-target="#Terms-of-Use">이용약관</a>과
            <a data-toggle="modal" data-target="#Privacy-Statement">개인정보취급방침</a>
            및 <a data-toggle="modal" data-target="#Electronic-banking-agreement">전자금융거래약관</a>에
            동의한 것으로 간주됩니다.</p>
    </div>
</div>
<!-- 이용약관 -->
<div class="modal fade" id="Terms-of-Use" tabindex="-1" role="dialog" aria-labelledby="ToU-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ToU-Label">analograph 이용약관</h4>
            </div>
            <div class="modal-body">

                <p>1. 목적</p>

                <p>본 약관은 analograph 서비스를 제공하는 회사와 서비스를 이용하는 회원간 서비스 제공과 이용에 관련하여 회사와 회원의 권리와 의무, 책임과 한계, 기타 중요 사항을 규정하기 위하여 작성되었습니다.</p>
                <br>
                <p>2. 정의</p>

                <p>a. ‘회사’란 analograph 서비스를 개발, 공급하는 주식회사 아날로그래프를 의미합니다.</p>

                <p>b. ‘회원’이란 analograph 서비스에 가입한 사용자를 의미합니다.</p>

                <p>c. ‘서비스’란 주식회사 아날로그래프가 제공하는 analograph를 의미합니다.</p>

                <p>d. ‘콘텐츠’란 analograph 서비스에 업로드 된 텍스트, 사진을 포함한 모든 종류의 자료를 의미합니다.</p>
                <br>
                <p>3. 이용계약</p>

                <p>사용자는 회원가입과 동시에 본 이용약관에 동의한 것으로 간주됩니다. </p>

                <p>회원가입은 사용자가 자신의 이름과 ID 및 password를 직접 입력한 후 회원가입 버튼을 클릭 혹은 탭 하거나 페이스북과 네이버를 포함한 타 서비스로 로그인 버튼을 최초로 클릭 혹은 탭 하여 analograph와 해당 타 서비스가 요청하는 개인정보 제공에 동의한 시점에서 완료됩니다.</p>
                <br>
                <p>4. 개인정보 정책</p>

                <p>회사는 서비스 운영을 위해 필요한 최소한의 개인정보를 회원 가입시 사용자의 동의 아래 수집합니다. 회사는 이렇게 수집한 회원의 개인정보 보호를 위하여 끊임 없이 노력할 것이며 적법한 절차를 동반한 수사기관의 요청 이외의 경우 회원의 개인정보를 공개하거나 타인에게 제공하지 않을 것입니다.</p>

                <p>다만 회원 또한 패스워드 등의 회원 본인의 개인정보를 타인이 알지 못 하게 관리하여야 할 의무와 타인에 의한 계정의 부당 이용 등을 발견하면 즉시 회사에 알려야 할 의무가 있습니다. 또한 회원의 관리 부주의로 인한 모든 피해는 회사가 책임지지 아니합니다.</p>

                <p>또한 타인에게 계정을 양도하거나 타인의 계정을 이용하여 서비스를 이용할 수 없으며 이러한 이용으로 회사에게 손해가 발생될 경우 손해에 대한 보상이 요구될 수 있습니다.</p>
                <br>
                <p>5. 서비스의 제공 및 변경</p>

                <p>a. 회사는 서비스를 이용하기 위한 양도 불가능하며 독점적이지 아니한 세계적 라이선스를 회원에게 제공합니다. </p>

                <p>b. 회사는 회원에게 서비스 이용을 위한 라이선스를 제공하지만 서비스와 서비스를 제공하기 위한 로고, 상호, 소프트웨어 등 모든 수단은 회사 고유의 지적 재산입니다.</p>

                <p>c. 회사는 서비스 내용을 약관에 부합하고 합법적인 범위 내에서 독자적으로 변경할 수 있습니다.</p>
                <br>
                <p>6. 유저 콘텐츠</p>

                <p>서비스에 존재하는 콘텐츠의 저작권과 그에 대한 책임은 콘텐츠를 서비스에 업로드 한 회원 본인에게 있습니다. 다만 회사는 서비스의 개발과 홍보 및 추천 목적에 한정하여 콘텐츠를 일부 사용, 저장, 복제, 수정, 송신, 배포, 전시 등의 방법으로 무상 이용할 수 있는 라이선스를 이 약관을 통해 사용자로부터 취득하게 됩니다. 사용자는 이러한 콘텐츠에 대하여 이용중지, 삭제를 요청할 수 있는 권리를 가지고 있습니다.</p>
                <br>
                <p>7. 지적재산권법</p>

                <p>회원은 지적재산권법에 위배되지 않게 서비스를 사용해야 할 의무가 있습니다. 지적재산권법에 위배되는 콘텐츠를 공유함으로 발생되는 모든 법적 분쟁과 손해는 회원에게 있습니다.</p>

                <p>또한 회원은 이러한 지적재산권의 침해를 알았을 경우 회사에게 이를 알려야 하며 회사는 사전 고지 없이 지적재산권을 침해한 콘텐츠를 삭제 할 수 있고 회원의 계정의 공유 기능을 차단하거나 본 이용계약을 파기하여 계정을 삭제 할 수 있습니다.</p>
                <br>
                <p>8. 부적절한 콘텐츠</p>

                <p>회사는 아래와 같은 부적절한 콘텐츠를 사전 고지 없이 삭제 할 수 있습니다. 또한 사전 고지 없이 부적절한 콘텐츠를 공유한 회원의 계정의 공유 기능을 중지하거나 본 이용계약을 파기하여 계정을 삭제 할 수 있고 회원의 정보를 적법한 절차를 동반한 수사기관의 요청에 제공 할 수 있습니다.</p>

                <p>a. 타인에게 모욕감을 주는 내용을 포함한 콘텐츠</p>

                <p>b. 사회 통념상 명백히 음란한 내용을 포함한 콘텐츠</p>

                <p>c. 회사가 제공하지 않는 광고를 포함한 콘텐츠</p>

                <p>d. 대한민국의 현행법에 위배되는 내용을 포함한 콘텐츠</p>

                <p>e. 불법을 장려하는 콘텐츠</p>

                <p>f. 대한민국의 현행법에 위배되는 콘텐츠</p>

                <p>g. 혹은 이러한 콘텐츠의 링크를 포함한 콘텐츠</p>

                <p>9. 부적절한 사용</p>

                <p>회사는 아래와 같은 서비스의 부적절하게 사용된 계정에 대하여 사전 고지 없이 계정의 공유 기능을 중지하거나 본 이용계약을 파기하여 계정을 삭제 할 수 있고 회원의 정보를 적법한 절차를 동반한 수사기관의 요청에 제공 할 수 있습니다.</p>

                <p>a. 여러 계정을 생성하여 순위를 조작하는 행위</p>

                <p>b. 같은 내용의 콘텐츠를 다수 공유하거나 지속적으로 공유하는 행위</p>

                <p>c. 위법한 목적으로 운영하는 행위</p>

                <p>d. 서비스의 안전과 안정을 저하해는 행위</p>
                <br>
                <p>10. 약관 위반</p>

                <p>회사는 약관 위반이 의심되는 경우 회원의 계정과 사용 내역을 조사 할 수 있으며 회원이 본 약관을 위반하였다고 판단될 경우 회원이 공유한 콘텐츠를 삭제 혹은 비활성화 하고 회원의 이용약관을 파기해 계정을 삭제하거나 계정을 비활성화 할 수 습니다.</p>

                <p>이렇게 삭제 혹은 비활성화 된 콘텐츠와 계정으로 인해 발생한 손해는 전적으로 회원의 책임입니다.</p>
                <br>
                <p>11. 제휴사</p>

                <p>제휴 광고 혹은 기타 제휴 서비스는 회사에서 관리하는 대상이 아니며 해당 제휴 광고와 제휴 서비스를 이용함에 있어 발생되는 모든 문제는 회사의 책임 범위가 아닙니다.</p>
                <br>
                <p>12. 서비스 보증</p>

                <p>회사가 회원에게 제공하는 서비스는 어떠한 보증도 포함하고 있지 않습니다. 서비스의 오류, 변경, 중단 및 기타 서비스의 변동으로 인한 어떠한 위험 요소와 손해도 회사는 책임지지 않습니다.</p>
                <br>
                <p>13. 책임과 한계</p>

                <p>회사는 본 이용약관을 준수하고 합법적인 서비스를 제공할 의무가 있습니다. 만일 회사가 이용약관을 준수하지 않거나 불법적인 서비스를 제공하여 회원에게 손해가 발생하였을 경우 회사는 회원에게 손해를 배상함은 물론 이용약관을 준수하고 합법적인 서비스를 제공하기 위한 모든 개선의 조치에 책임을 다 할 것입니다.</p>
                <br>
                <p>14. 관할</p>

                <p>본 약관은 대한민국의 법을 따르며, 회사와의 모든 법적 분쟁은 대한민국에서 이루어집니다.</p>
                <br>
                <p>15. 약관의 분리</p>

                <p>어떠한 사유로 본 약관의 일부가 효력을 상실하거나 개정, 추가 되더라도 이외의 약관의 효력은 지속됩니다. </p>
                <br>
                <p>16. 권리의 행사</p>

                <p>약관을 위배한 행위에 대하 회사가 약관에 규정된 제재를 취하기 않았다 하더라도 약관에 규정된 권리의 행사를 포기하거나 위반 내역에 대한 제재가 면제된 것은 아닙니다.</p>
                <br>
                <p>17. 약관의 수정</p>

                <p>회사는 서비스의 개선과 발전, 혹은 법률 등 주변 상황의 변화에 대응하기 위해 약관을 변경 할 수 있습니다. 약관이 변경될 경우 회사는 가입시 등록한 이메일 혹은 서비스 내의 알림 기능을 통하여 회원에게 이 사실과 변경 내용을 안내합니다.</p>

                <p>회원은 변경된 약관을 안내 받은 즉시 내용을 검토하여야 하며 동의하지 않을 시 회원 탈퇴를 통해 이용계약을 해지 할 수 있습니다. 변경된 약관을 검토한 이후 서비스를 계속 이용한다면 변경된 이용 약관에 동의한 것으로 간주됩니다.</p>
            </div>
        </div>
    </div>
</div>
<!-- 개인정보취급방침 -->
<div class="modal fade" id="Privacy-Statement" tabindex="-1" role="dialog" aria-labelledby="PS-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="PS-Label">개인정보취급방침</h4>
            </div>
            <div class="modal-body">
                <ol>
                    <li>개인정보의 수집ㆍ이용 목적, 수집하는 개인정보의 항목 및 수집방법</li>
                </ol>
                <p>analograph는 회원의 식별과 회원 인증 및 컨텐츠의 구입과 환불 그리고 콘텐츠와 사용자 추천과 사용자 편의기능을 위해 회원의 개인정보를 수집ㆍ처리하고 있습니다.</p>
                <p>수집되는 개인정보는 회원의 이메일과 암호화 된 비밀번호, 성별, 생년월일, 학교, 페이스북의 고유 아이디 또 회원의 판매 및 구매 항목, 응원 내역과 응원 받은 내역, 서비스 이용내역과 마지막 접속 기록 및 쿠키입니다.</p>
                <p>이메일과 비밀번호, 성별, 생년월일은 회원가입 절차를 통해 수집하며 학교는 회원이 직접 개인정보 수정을 통해 입력할 수 있습니다. 페이스북의 고유 아이디는 페이스북을 통한 회원가입 혹은 친구추천 기능을 이용할 때 수집됩니다. 구매 및 판매 항목은 회원이 콘텐츠를 구매하거나 회원이 공유한 판매용 콘텐츠가 판매되었을 때 수집되며 응원 내역과 응원 받은 내역은 회원이 포인트를 응원하거나 응원 받았을 때 수집됩니다.</p>
                <p>&nbsp;</p>
                <ol start="2">
                    <li>개인정보의 보유 및 이용 기간, 개인정보의 파기절차 및 파기방법</li>
                </ol>
                <p>analograph가 회원의 개인정보를 보유하는 기간은 회원의 탈퇴 혹은 회사를 통한 이용계약 해지 후 14일까지 입니다. 다만 &lsquo;전자상거래 등에서의 소비자보호에 관한 법률&rsquo;에 따라 표시&middot;광고에 관한 기록은 6개월, 계약 또는 청약철회 등에 관한 기록은 5년, 대금결제 및 재화 등의 공급에 관한 기록은 5년, 소비자의 불만 또는 분쟁처리에 관한 기록은 3년 동안 보존됨을 유의해주시기 바랍니다.</p>
                <p>보유 기간이 경과된 개인정보는 analograph의 서버에서 복원 될 수 없도록 완전 삭제 처리됩니다.</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <ol start="3">
                    <li>개인정보를 제3자에게 제공하는 경우 제공받는 자의 성명, 제공받는 자의 이용 목적과 제공하는 개인정보의 항목</li>
                </ol>
                <p>analograph는 별도의 회원 동의 혹은 적법한 절차를 동반한 수사기관의 요청이 있는 경우를 제외한 어떠한 경우에도 제 3자에게 회원의 개인정보를 제공하지 않습니다.</p>
                <ol start="4">
                    <li>개인정보 취급위탁을 하는 업무의 내용 및 수탁자</li>
                </ol>
                <p>analograph는 더 안전하고 편리한 서비스를 제공하기 위해 다음과 같이 개인정보를 취급위탁 하고 있습니다.</p>
                <p><strong>향후 추가</strong></p>
                <ol start="5">
                    <li>이용자 및 법정대리인의 권리와 그 행사방법</li>
                </ol>
                <p>analograph의 회원 혹은 회원의 법정 대리인은 아래와 같은 권리를 행사 할 수 있습니다.</p>
                <ol>
                    <li>개인정보 열람 요청</li>
                    <li>개인정보에 대한 정정 요청</li>
                    <li>개인정보 삭제 요청</li>
                </ol>
                <p>개인정보 열람과 정정 요청은 서비스 내의 개인정보 페이지 혹은 고객센터를 통해 진행 할 수 있으며 개인정보 삭제 요청은 회원 탈퇴를 통해 진행 할 수 있습니다. 회원 탈퇴 요청이 접수되면 도용으로 인한 피해를 방지하기 위해 14일의 유예기간 이후 회원의 개인정보가 삭제됩니다.</p>
                <ol start="6">
                    <li>인터넷 접속정보파일 등 개인정보를 자동으로 수집하는 장치의 설치ㆍ운영 및 그 거부에 관한 사항</li>
                </ol>
                <p>analograph는 웹 브라우저의 쿠키를 통해 회원의 개인정보를 자동으로 수집하고 있습니다. 하지만 회원은 언제든 쿠키 사용에 대한 거부권을 가지고 있습니다.</p>
                <p>쿠키를 통한 개인정보 제공을 거부하고자 할 때에는 웹 브라우저의 설정을 통해 쿠키 설치 여부를 선택하실 수 있으며 자세한 방법은 회원이 사용하는 웹브라우저의 매뉴얼을 참고해주시기 바랍니다.</p>
                <p>쿠키를 통한 개인정보 제공을 중단할 경우 자동 로그인 기능과 공개범위 설정 및 최근 이용한 폴더 저장 기능을 이용하실 수 없습니다.</p>
                <ol start="7">
                    <li><strong>개인정보보호를 위한 기술적/관리적 대책</strong></li>
                </ol>
                <p><strong>analograph</strong><strong>는 회원의 개인정보 보호를 위해 서버에 저장되는 비밀번호와 사용자와 서버 사이의 모든 통신을 암호화하여 처리하고 있습니다.</strong></p>
                <p><strong>향후 추가</strong></p>
                <ol start="8">
                    <li>개인정보 관리책임자의 성명과 연락처</li>
                </ol>
                <p>개인정보 보호 책임자</p>
                <p>성명: 이성호</p>
                <p>직위: 대표</p>
                <p>e-mail: s.h.lee@analograph.com</p>
            </div>
        </div>
    </div>
</div>
<!-- 전자금융거래약관 -->
<div class="modal fade" id="Electronic-banking-agreement" tabindex="-1" role="dialog" aria-labelledby="Eba-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="Eba-Label">전자금융거래약관</h4>
            </div>
            <div class="modal-body">
                업데이트 예정
            </div>
        </div>
    </div>
</div>
</body>
</html>