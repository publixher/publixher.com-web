<!DOCTYPE html>
<html lang="ko">
<script src="/plugins/jquery.min.js" type="text/javascript"></script>
<script src="https://static.nid.naver.com/js/naverLogin_implicit-1.0.2-min.js"></script>
<script>
    var naver_id_login = new naver_id_login("OJ9jBISrQELVlxFNyHlz", "http://analograph.com/php/naver_login.php");
    naver_id_login.get_naver_userprofile();
    var email = naver_id_login.getProfileData('email');
    var age = naver_id_login.getProfileData('age');
    var birthday = naver_id_login.getProfileData('birthday');
    var gender = naver_id_login.getProfileData('gender');
    var profile_image = naver_id_login.getProfileData('profile_image');
    var name = naver_id_login.getProfileData('name');
    $.ajax({
        url: "/php/data/api_login.php",
        type: "POST",
        data: {
            email: email,
            age: age,
            birthday: birthday,
            gender: gender,
            image: profile_image,
            name: name,
            api: "naver"
        },
        dataType: 'json',
        success: function (res) {
            location.href = '/';
        }, error: function () {
            alert('작업중 문제가 생겼습니다.');
            location.href = '/https/login.php';
        }
    })
</script>
</html>