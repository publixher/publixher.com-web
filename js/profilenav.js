/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
$(document).ready(function(){
    //드롭다운안에 클릭했을때 안닫히게 하려면 이렇게
    $('.hasInput,.hasSelect').click(function (e) {
        e.stopPropagation();
    });
    //드롭다운 안에 검색목록의 동작
    $('.hasInput input[type=text]').on('input', function () {
        var val = $(this).val();
        var list = $(this).parents()[1].id;
        var listarray = [];
        switch (list) {
            //친구신청목록에서
            case 'freqlist':
                listarray = freqvar;
                break;
            //친구목록에서
            case 'frielist':
                listarray = frievar;
                break;
            //구독목록에서
            case 'subslist':
                listarray = subsvar;
                break;
        }
        //grep으로 리스트에서 입력된것과 관련 없는걸 걸러냄
        var matched = $.grep(listarray, function (el) {
            return el.indexOf(val) > -1;
        });
        var not_matched = $.grep(listarray, function (el) {
            return el.indexOf(val) <= -1;
        });
        //matched는 배열

        $.each(matched, function (ind, val) {
            var lis = $('#' + list + ' .nameuser:contains(' + val + ')').eq(0).parent();
            lis.css('display', 'block');
        })
        $.each(not_matched, function (ind, val) {
            var lis = $('#' + list + ' .nameuser:contains(' + val + ')').eq(0).parent();
            lis.css('display', 'none');
        })

    });

    //친구요청
    $('#friequst').on('click', function () {
        $(this).attr('disabled', 'disabled');
        var action = $(this).hasClass('request') ? "request" : "endrelation";
        $.ajax({
            url: "/php/data/friend.php",
            type: "POST",
            data: {targetseq: targetid, myseq: myseq, action: action, token: token, age: age},
            dataType: 'json',
            success: function () {
                var btn = $('#friequst');
                if (btn.hasClass('request')) {
                    btn.html('친구신청중');
                } else if (btn.hasClass('onfriend')) {
                    btn.html('친구신청').addClass('btn-default').addClass('request').removeClass('btn-success').removeClass('onfriend').removeAttr('disabled');
                }
            }, error: function () {
                $(this).removeAttr('disabled');
            }
        });
    });
    //친구요청 응답
    $('.freqanswer').on('click', function () {
        var fid = $(this).attr('fid') ? $(this).attr('fid') : null;
        var requestid = $(this).attr('requestid');
        var pa = $(this).parent()[0];
        var action = $(this).hasClass('friendok') ? "friendok" : "friendno";
        $.ajax({
            url: "/php/data/friend.php",
            type: "POST",
            data: {targetseq: fid, requestid: requestid, action: action, myseq: myseq, token: token, age: age},
            dataType: 'json',
            success: function (res) {
                if (res['result'] == 'Y') {
                    pa.remove();
                    $('#frequestnum').text($('#frequestnum').text() - 1);
                    if ($('#frequestnum').text() == 0) {
                        $('#freqli').append("<li><a>친구요청이 없습니다</a></li>")
                    }
                }
            }
        })
    })

    //구독신청
    $('#subsbtn').on('click', function () {
        $(this).attr('disabled', 'disabled');
        var action = $(this).hasClass('subscribe') ? "subscribe" : "dis_subscribe";
        $.ajax({
            url: "/php/data/friend.php",
            type: "POST",
            data: {targetseq: targetseq, action: action, userseq: mid, token: token, age: age},
            dataType: 'json',
            success: function (res) {
                var btn = $('#subsbtn');
                if (btn.hasClass('subscribe')) {
                    btn.addClass('dis_subscribe').removeClass('subscribe').addClass('btn-info').removeClass('btn-default').html('구독중');
                } else {
                    btn.addClass('subscribe').removeClass('dis_subscribe').addClass('btn-default').removeClass('btn-info').html('구독하기');
                }
                btn.removeAttr('disabled');
            }, error: function () {
                $(this).removeAttr('disabled');
            }
        })
    })
    //글쓰기 권한 설정
    $("input:radio[name='writeAuth']").change(function () {
        var radioValue = $(this).val();
        $.ajax({
            url: "/php/data/profileChange.php",
            type: "POST",
            data: {action: "writeAuth", userseq: mid, radioValue: radioValue}
        })
    })
    $(".expAuth").change(function () {
        var checkValue = '';
        $(".expAuth:checked").each(function () {
            checkValue += $(this).val();
        })
        $.ajax({
            url: "/php/data/profileChange.php",
            type: "POST",
            data: {action: "expAuth", userseq: mid, checkValue: checkValue}
        })
    })
});