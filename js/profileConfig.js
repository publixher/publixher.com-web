/**
 * Created by gangdong-gyun on 2016. 3. 3..
 */
$(document).ready(function () {
    //파일 업로드시 동작
    $('#fileuploads').fileupload({
        dataType: 'json',
        add: function (e, data) {
            var uploadFile = data.files[0];
            var isValid = true;
            if (!(/png|jpe?g|gif/i).test(uploadFile.name)) {
                alert('png, jpg, gif 만 가능합니다');
                isValid = false;
            } else if (uploadFile.size > 30000000) { // 30mb
                alert('파일 용량은 30메가를 초과할 수 없습니다.');
                isValid = false;
            }
            if (isValid) {
                data.submit();
            }
        },
        done: function (e, data) {
            $('.file-input-img').attr('src', '/img/' + data.result['files']['file_profile']);
        }, fail: function (e, data) {
            // data.errorThrown
            // data.textStatus;
            // data.jqXHR;
            console.log('서버와 통신 중 문제가 발생했습니다');
            console.log('e : ' + e);
            console.log('data : ' + data);
        }
    })
    //비밀번호 유효성 검사
    var pwwrong = $('#pwwrong');
    var pwcheckwrong = $('#pwcheckwrong');
    var mpass = $('#mpass');
    var mpassCheck = $('#mpasscheck');


    function CheckPW() {
        var msg = "";
        var check = /^(?=.*[a-zA-Z])(?=.*[0-9]).{6,16}$/;
        if (mpass.val().length < 6 || mpass.val().length > 16) {
            msg = "비밀번호는 6 ~ 16 자리로 입력해주세요.";
            pwwrong.text('');
            pwwrong.text(msg);
            return false;
        }
        if (!check.test(mpass.val())) {
            msg = "비밀번호는 문자, 숫자의 조합으로 입력해주세요.";
            pwwrong.text('');
            pwwrong.text(msg);
            return false;
        }
        return true;
    }
    pwvali=true;
    pwconfirm=true;
    mpass.on("change", function () {
        if (mpass.val().length > 0) {
            if (!CheckPW()) {
                pwwrong.css('display', 'block');
                pwwrong.removeClass('alert-success');
                pwwrong.addClass('alert-danger');
                pwvali = false;
            } else {
                pwwrong.css('display', 'block');
                pwwrong.removeClass('alert-danger');
                pwwrong.text('훌륭합니다.');
                pwwrong.addClass('alert-success');
                pwvali = true;
            }
        }
    });
    mpassCheck.on("change", function () {
        if(mpassCheck.val().length>0) {
            if (mpass.val() != mpassCheck.val()) {
                pwcheckwrong.css('display', 'block');
                pwcheckwrong.text('비밀번호와 다릅니다.');
                pwcheckwrong.removeClass('alert-success');
                pwcheckwrong.addClass('alert-danger');
                pwconfirm = false;
            } else {
                pwcheckwrong.css('display', 'block');
                pwcheckwrong.removeClass('alert-danger');
                pwcheckwrong.text('훌륭합니다.');
                pwcheckwrong.addClass('alert-success');
                pwconfirm = true;
            }
        }
    })

    $('#submit').click(function () {
        if(pwvali&&pwconfirm) {
            $('#rf').submit(function () {
                console.log('submit중');
            });
        }else{
            alert('비밀번호를 확인해 주세요');
            return false;
        }
    });
});