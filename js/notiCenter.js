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
            data: {action: "confnotireq",nowpage:page},
            dataType: 'json',
            success: function (res) {
                //알림문장
                for (var i = 0; i < res.length; i++) {
                    switch (res[i]['ACT']) {
                        case '1':
                            var word = res[i]['USER_NAME']+"님이 회원님의 \""+res[i]['TITLE']+"\"게시물을 구매했습니다.";
                            $('#listul').append('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                            break;
                        case '2':
                            var word = res[i]['USER_NAME'] + '님이 회원님과 친구가 되고싶어 합니다.';
                            $('#listul').append('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/profile/' + mid + '">' + word + '</a></li>');
                            break;
                        case '3':
                            var word = res[i]['USER_NAME']+"님이 \"" + res[i]['TITLE'] + "\" 게시물에 새로운 댓글을 달았습니다.\""+res[i]['REPLY']+"\"";
                            $('#listul').append('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                            break;
                        case '4':
                            var word = res[i]['USER_NAME']+"님이 \"" + res[i]['TITLE'] + "\" 게시물에 노크 했습니다.";
                            $('#listul').append('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                            break;
                        case '7':
                            var word = "회원님이 다신 \""+res[i]['REPLY']+"\" 댓글에 "+res[i]['USER_NAME']+"님이 \""+res[i]['SUB_REPLY']+"\" 라고 댓글을 달았습니다.";
                            $('#listul').append('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                            break;
                        case '8':
                            var word = res[i]['USER_NAME']+"님이 \""+res[i]['TITLE']+"\" 게시물에 회원님을 소환했습니다.";
                            $('#listul').append('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                            break;
                        case '9':
                            var word=res[i]['USER_NAME']+"님이 \""+res[i]['REPLY']+"\" 댓글에 회원님을 소환했습니다.";
                            $('#listul').append('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                            break;
                    }
                }
                page = page + 1;
            },error:function(xhr,status,error){
            errorReport("confnotireg",{action: "confnotireq",nowpage:page},status,error);
                alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
            }
        })
    }
    //로딩 끝나면 알림 받아오기
    getnoti();
    $('#notimore').on('click', function () {
        getnoti();
    });
});