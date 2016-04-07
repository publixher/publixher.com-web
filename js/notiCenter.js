/**
 * Created by gangdong-gyun on 2016. 3. 12..
 */
$(document).ready(function () {
    var page=0;
    //알림 받아오는 함수 정의
    function getnoti(){
        $.ajax({
            url: "/php/data/getNoti.php",
            type: "GET",
            data: {action: "noticenter",nowpage:page},
            dataType: 'json',
            success: function (res) {
                console.log(res)
                var listul = $('#listul');
                var snsreg = /SNS-[0-9]*/;
                //알림문장
                if (res[1]) {
                    var title = Object.keys(res[1]);
                    for (var i = 0; i < title.length; i++) {
                        var word = "'" + title[i] + "' 게시물에 " + res[1][title[i]]['count'] + '회의 신규 구매가 있었습니다.'
                        listul.append('<li><a href="/php/getItem.php?iid=' + res[1][title[i]][0]['SEQ_CONTENT'] + '">' + word + '</a><span class="notidate">'+res[1][title[i]]['date']+'</span></li>');
                    }
                }
                if (res[2]) {
                    for (var i = 0; i < res[2].length; i++) {
                        var word = res[2][i]['USER_NAME'] + '님이 회원님과 친구가 되고싶어 합니다.'
                        listul.append('<li><a href="/php/profile.php?id='+mid+'">' + word + '</a></li>')
                    }
                }
                if (res[3]) {
                    var title = Object.keys(res[3]);
                    for (var i = 0; i < title.length; i++) {
                        if (snsreg.test(title[i])) {
                            var word = '회원님의 게시물에 ' + res[3][title[i]]['count'] + '개의 신규 댓글이 있습니다.'
                        } else {
                            var word = "'" + title[i] + "' 게시물에 " + res[3][title[i]]['count'] + '개의 신규 댓글이 있습니다.'
                        }
                        listul.append('<li><a href="/php/getItem.php?iid=' + res[3][title[i]][0]['SEQ_CONTENT'] + '">' + word + '</a><span class="notidate">'+res[3][title[i]]['date']+'</span></li>');
                    }
                }
                if (res[4]) {
                    var title = Object.keys(res[4]);
                    for (var i = 0; i < title.length; i++) {
                        if (snsreg.test(title[i])) {
                            var word = '회원님의 게시물에 ' + res[4][title[i]]['count'] + '개의 신규 노크가 있습니다.'
                        } else {
                            var word = "'" + title[i] + "' 게시물에 " + res[4][title[i]]['count'] + '개의 신규 노크가 있습니다.'
                        }
                        listul.append('<li><a href="/php/getItem.php?iid=' + res[4][title[i]][0]['SEQ_CONTENT'] + '">' + word + '</a><span class="notidate">'+res[4][title[i]]['date']+'</span></li>');
                    }
                }
                if (res[6]) {
                    var reply = Object.keys(res[6]);
                    for (var i = 0; i < reply.length; i++) {
                        var word = "회원님의 '" + reply[i] + "' 댓글에 " + res[6][reply[i]]['count'] + '개의 신규 노크가 있습니다.';
                        listul.append('<li><a href="/php/getItem.php?iid='+ res[6][reply[i]][0]['SEQ_CONTENT'] + '">' + word + '</a><span class="notidate">'+res[6][reply[i]]['date']+'</span></li>');
                    }
                }
                if (res[7]) {
                    var reply = Object.keys(res[7]);
                    for (var i = 0; i < reply.length; i++) {
                        var word = "회원님의 '" + reply[i] + "' 댓글에 " + res[7][reply[i]]['count'] + '개의 새로운 대댓글이 있습니다.';
                        listul.append('<li><a href="/php/getItem.php?iid='+ res[7][reply[i]][0]['SEQ_CONTENT'] + '">' + word + '</a><span class="notidate">'+res[7][reply[i]]['date']+'</span></li>');
                    }
                }
                page=page+1;
            }
        })
    }
    //로딩 끝나면 알림 받아오기(100개 단위)
    getnoti();
    $('#notimore').on('click', function () {
        getnoti();
    });
});