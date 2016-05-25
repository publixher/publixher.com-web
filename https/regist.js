/**
 * Created by gangdong-gyun on 2016. 2. 5..
 */
$(document).ready(function () {
//생년월일 입력 형식만드는 스크립트
    $(function () {

        //populate our years select box
        for (i = new Date().getFullYear(); i > 1900; i--) {
            $('#years').append($('<option />').val(i).html(i));
        }
        //populate our months select box
        for (i = 1; i < 13; i++) {
            if (i < 10) {
                $('#months').append($('<option />').val('0' + i).html('0' + i));
            } else {
                $('#months').append($('<option />').val(i).html(i));
            }
        }
        updateNumberOfDays();

        $('#years, #months').change(function () {
            updateNumberOfDays();
        });

    });
    function updateNumberOfDays() {
        $('#days').html('');
        month = $('#months').val();
        year = $('#years').val();
        days = daysInMonth(month, year);

        for (i = 1; i < days + 1; i++) {
            if (i < 10) {
                $('#days').append($('<option />').val('0' + i).html('0' + i));
            } else {
                $('#days').append($('<option />').val(i).html(i));
            }
        }
    }

    function daysInMonth(month, year) {
        return new Date(year, month, 0).getDate();
    }

    //항목별 적합성 검사
    var idvali = false;
    var dupidchk = false;
    var pwvali = false;
    var pwconfirm = false;
    var namevali = false;
//id로 사용할 이메일 형식 체크하기
    var mid = $('#mid');
    var idwrong = $('#idwrong');
    var regEmail = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;

    mid.on("input", function () {
        if (!regEmail.test(mid.val())) {
            idwrong.css('display', 'block');
            idwrong.text('이메일을 입력해 주세요.');
            idwrong.removeClass('alert-success');
            idwrong.addClass('alert-danger');
        } else {
            idwrong.css('display', 'block');
            idwrong.removeClass('alert-danger');
            idwrong.text('훌륭합니다.');
            idwrong.addClass('alert-success');
        }
    });
//아이디 입력 다하면 서버로 아이디가 있는지 검사
    mid.on("change", function () {
        $.ajax({
            url: "/https/isId.php",
            type: "GET",
            data: mid,
            dataType: 'json',
            success: function (da) {
                //결과
                if (da.id == "is") {
                    dupidchk = false;
                    idwrong.css('display', 'block');
                    idwrong.text('');
                    idwrong.text('이미 등록된 이메일입니다.');
                    idwrong.removeClass('alert-success');
                    idwrong.addClass('alert-danger');
                } else {
                    dupidchk = true;
                }
            }
        })
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

    mpass.on("input", function () {
        if (!CheckPW()) {
            pwwrong.css('display', 'block');
            pwwrong.removeClass('alert-success');
            pwwrong.addClass('alert-danger');
        } else {
            pwwrong.css('display', 'block');
            pwwrong.removeClass('alert-danger');
            pwwrong.text('훌륭합니다.');
            pwwrong.addClass('alert-success');
        }
    });
    mpassCheck.on("input", function () {
        if (mpass.val() != mpassCheck.val()) {
            pwcheckwrong.css('display', 'block');
            pwcheckwrong.text('비밀번호와 다릅니다.');
            pwcheckwrong.removeClass('alert-success');
            pwcheckwrong.addClass('alert-danger');
        } else {
            pwcheckwrong.css('display', 'block');
            pwcheckwrong.removeClass('alert-danger');
            pwcheckwrong.text('훌륭합니다.');
            pwcheckwrong.addClass('alert-success');
        }
    })

    //이름 유효성 검사
    var namewrong = $('#namewrong');
    var mname = $('#mname');
    var regHName = /^[가-힣ㄱ-ㅎㅏ-ㅣ]{2,5}$/;
    var regEName = /^[a-zA-Z]{2,10}\s[a-zA-Z]{2,10}$/;
    mname.on("input", function () {
        if (regHName.test(mname.val())) {
            namewrong.css('display', 'block');
            namewrong.text('훌륭합니다.');
            namewrong.removeClass('alert-danger');
            namewrong.addClass('alert-success');
        } else if (regEName.test(mname.val())) {
            namewrong.css('display', 'block');
            namewrong.text('훌륭합니다.');
            namewrong.removeClass('alert-danger');
            namewrong.addClass('alert-success');
        }
        else {
            namewrong.css('display', 'block');
            namewrong.text('이름을 입력해 주세요.');
            namewrong.removeClass('alert-success');
            namewrong.addClass('alert-danger');
        }
    })

    $('#submit').click(function (e) {
        var regEmail = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;
        var pwcheck = /^(?=.*[a-zA-Z])(?=.*[0-9]).{6,16}$/;
        var regHName = /^[가-힣ㄱ-ㅎㅏ-ㅣ]{2,5}$/;
        var regEName = /^[a-zA-Z]{2,10}\s[a-zA-Z]{2,10}$/;
        if (regEmail.test(mid.val())) idvali = true;
        if ((mpass.val() == mpassCheck.val()) && pwcheck.test(mpass.val())) pwvali = true;
        if (regHName.test(mname.val())) {
            namevali = true;
        } else if (regEName.test(mname.val())) {
            namevali = true;
        }
        else {
            namevali = true;
        }
        if (idvali && dupidchk && pwvali && namevali) {
            e.preventDefault();
            var formData = $('#rf').serialize();
            $.ajax({
                url: "/php/data/registConfirm.php",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function (res) {
                    if (res['result'] == 'reg') {
                        alert('회원가입이 완료되었습니다.이메일의 링크를 눌러 인증을 해주세요.');
                    } else if (res['result'] == 'server error') {
                        alert('서버 에러입니다. 다시 시도해 주세요')
                    } else if (res['result'] == 'check value') {
                        alert('입력값이 잘못되었습니다. 입력값을 확인해 주세요')
                    }
                }
            })
        } else {
            alert('아이디,비밀번호,이름을 확인해 주세요');
            return false;
        }
        $('#rf').fadeOut(function () {
            $('#rf').replaceWith('<div>인증메일을 보냈습니다.<br> 메일을 통해 ID를 인증해주세요.</div>').fadeIn();
        })
    });

    //비밀번호 찾기 버튼
    $('#find-id').click(function () {
        $('#r-div').fadeOut(function () {
            $('#r-div').remove();
            var findBtn = $('#find-id');
            findBtn.after(
                $('<div>').attr('id', 'find-id-div').append(
                    $('<p>').addClass('alert alert-info').text('email을 입력하세요')
                    , $('<input>').attr('type', 'text').addClass('form-control')
                    , $('<button>').addClass('btn btn-default pass-find-btn').text('확인').attr('type','button').on('click', function () {
                        $.ajax({
                            url:'/php/api/findUser.php',
                            dataType:'json',
                            type:'POST',
                            data:{email:$(this).siblings('input').val(),action:"find_pass"},
                            success:function(res){
                                if(res['status']==1) {
                                    alert('입력된 이메일로 임시 비밀번호가 발급되었습니다.');
                                }else if(res['status']==-2){
                                    alert('이메일을 입력해 주세요.');
                                }else if(res['status']==0){
                                    alert('해당 이메일로 가입한 회원이 없습니다.');
                                }
                                window.location.reload()
                            }
                        })
                    })
                ).fadeIn()
            )
        })
    });
});
