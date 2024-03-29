$(document).ready(function () {
    //검색창에 글자를 입력할때마다 db에서 검색결과 가져옴
    $('#gsearch').on('input', function () {
        //스피너
        var spinner = $('<div>')
            .attr('data-loader', 'spinner')
            .addClass('load-item search-load')
        //통합검색
        var search_word = $('#gsearch').val();
        if (search_word.length > 1) {
            $('#searchResult').css('display', 'block').append(spinner);
            function search(target) {
                var data = {searchword: search_word, target: target, mid: mid, name: search_word}
                $.ajax({
                    url: "/php/data/nameFind.php",
                    type: "GET",
                    data: data,
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        var Result
                        switch (target) {
                            case 'friend':
                                Result = $('#friendResult');
                                break;
                            case 'name':
                                Result = $('#nameResult');
                                break;
                            case 'title':
                                Result = $('#contResult');
                                break;
                            case 'tag':
                                Result = $('#tagResult');
                                break;
                        }
                        spinner.detach();
                        if (res.length > 0) {
                            Result.css('display', 'block');
                            var SearchRes = '';
                            for (var i = 0; i < res.length; i++) {
                                switch (target) {
                                    case 'friend':
                                        for (var i = 0; i < res.length; i++) {
                                            SearchRes += '<li><div><img src="' + res[i]['PIC'] + '"></div><a href="/profile/' + res[i]['ID'] + '">' + res[i]['USER_NAME'] + '>>>>친구</a></li>';
                                        }
                                        break;
                                    case 'name':
                                        if (res[i]['COMMUNITY'] == 1) {
                                            if (res[i]['IS_NICK'] == 'Y') {
                                                SearchRes += '<li><div><img src="' + res[i]['PIC'] + '"></div><a href="/profile/' + res[i]['ID'] + '">' + res[i]['USER_NAME'] + '>>>>커뮤니티 익명</a></li>';
                                            } else {
                                                SearchRes += '<li><div><img src="' + res[i]['PIC'] + '"></div><a href="/profile/' + res[i]['ID'] + '">' + res[i]['USER_NAME'] + '>>>>커뮤니티</a></li>';
                                            }
                                        } else {
                                            if (res[i]['IS_NICK'] == 'Y') {
                                                SearchRes += '<li><div><img src="' + res[i]['PIC'] + '"></div><a href="/profile/' + res[i]['ID'] + '">' + res[i]['USER_NAME'] + '>>>>익명</a></li>';
                                            } else {
                                                SearchRes += '<li><div><img src="' + res[i]['PIC'] + '"></div><a href="/profile/' + res[i]['ID'] + '">' + res[i]['USER_NAME'] + '>>>>이름</a></li>';
                                            }
                                        }
                                        break;
                                    case 'title':
                                        for (var i = 0; i < res.length; i++) {
                                            SearchRes += '<li><div><img src="' + res[i]['PIC'] + '"></div><a href="/content/' + res[i]['ID'] + '">' + res[i]['TITLE'] + '>>>>아이템</a></li>';
                                        }
                                        break;
                                    case 'tag':
                                        for (var i = 0; i < res.length; i++) {
                                            SearchRes += '<li><a href="/tag/' + res[i]['TAG'] + '">' + res[i]['TAG'] + '>>>>태그</a></li>';
                                        }
                                        break;
                                }

                            }
                            Result.html(SearchRes);
                        } else {
                            Result.html('');
                            Result.css('display', 'none');
                        }
                    }, error: function (xhr, status, error) {
                        //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                        errorReport("search", data, status, error)
                    }
                });
            }

            search('friend');
            search('name');
            search('title');
            search('tag');
        } else {
            $('#searchResult').css('display', 'none');
            $('#friendResult').html('');
            $('#contResult').html('');
            $('#nameResult').html('');
            $('#tagResult').html('');
        }
    })
    //본문검색
    $('#search-body').on('click', function () {
        var search_body = $('#gsearch').val();
        location.href = '/body/' + search_body;
    })
    //페이지 로딩이 끝나면 알림의 개수를 받아온다
    $.ajax({
        url: "/php/data/getNoti.php",
        type: "GET",
        data: {action: "confonload"},
        dataType: 'json',
        success: function (res) {
            if (res['COUNT'] > 0) {
                $('#notibtn').append('<span class="badge">' + res['COUNT'] + '</span>');
            }
        }, error: function (xhr, status, error) {
            errorReport("getNoti", {action: "confonload"}, status, error)
            //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
        }
    })
    //알림을 받아오는 함수
    var notiPage = 0;
    var notiLoading = false;

    function getNoti() {
        if (!notiLoading) {
            notiLoading = true;
            var spinner = $('<div>')
                .attr('data-loader', 'spinner')
                .addClass('load-item noti-load')
            $('#notilist').append(spinner);
            $.ajax({
                url: "/php/data/getNoti.php",
                type: "GET",
                data: {action: "confnotireq", nowpage: notiPage},
                dataType: 'json',
                success: function (res) {
                    spinner.detach()
                    //알림문장
                    for (var i = 0; i < res.length; i++) {
                        switch (res[i]['ACT']) {
                            case '1':
                                var word = res[i]['USER_NAME'] + "님이 회원님의 \"" + res[i]['TITLE'] + "\"게시물을 구매했습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="' + res[i]['PIC'] + '"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">' + res[i]['NOTI_DATE'] + '</span></li>');
                                break;
                            case '2':
                                var word = res[i]['USER_NAME'] + '님이 회원님과 친구가 되고싶어 합니다.';
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="' + res[i]['PIC'] + '"></div><a href="/profile/' + mid + '">' + word + '</a><span class="noti-date">' + res[i]['NOTI_DATE'] + '</span></li>');
                                break;
                            case '3':
                                var word = res[i]['USER_NAME'] + "님이 \"" + res[i]['TITLE'] + "\" 게시물에 코멘트를 남겼습니다.\"" + res[i]['REPLY'] + "\"";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="' + res[i]['PIC'] + '"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">' + res[i]['NOTI_DATE'] + '</span></li>');
                                break;
                            case '4':
                                var word = res[i]['USER_NAME'] + "님이 \"" + res[i]['TITLE'] + "\" 게시물에 노크 했습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="' + res[i]['PIC'] + '"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">' + res[i]['NOTI_DATE'] + '</span></li>');
                                break;
                            case '7':
                                var word = "회원님의 \"" + res[i]['REPLY'] + "\" 코멘트에 " + res[i]['USER_NAME'] + "님이 코멘트를 남겼습니다. \"" + res[i]['SUB_REPLY'] + "\"" ;
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="' + res[i]['PIC'] + '"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">' + res[i]['NOTI_DATE'] + '</span></li>');
                                break;
                            case '8':
                                var word = res[i]['USER_NAME'] + "님이 \"" + res[i]['TITLE'] + "\" 게시물에 회원님을 태그했습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="' + res[i]['PIC'] + '"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">' + res[i]['NOTI_DATE'] + '</span></li>');
                                break;
                            case '9':
                                var word = res[i]['USER_NAME'] + "님이 \"" + res[i]['REPLY'] + "\" 댓글에 회원님을 태그했습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="' + res[i]['PIC'] + '"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">' + res[i]['NOTI_DATE'] + '</span></li>');
                                break;
                            case 'a':
                                var word = res[i]['USER_NAME'] + "님과 친구가 되었습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="' + res[i]['PIC'] + '"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">' + res[i]['NOTI_DATE'] + '</span></li>');
                                break;
                        }
                    }
                    if (!$('#notilist').hasClass('loaded')) {
                        $('#notilist').addClass('loaded');
                    }
                    if ($('#notibtn .badge').length > 0) {
                        $('#notibtn .badge').remove();
                    }
                    notiPage = notiPage + 1;
                    notiLoading = false;
                }, error: function (xhr, status, error) {
                    errorReport("getNoti", {action: "confnotireq", nowpage: notiPage}, status, error)
                    //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                }
            })
        }
    }

    //알림버튼 누를때 서버에서 그룹화된거 다 불러오기
    $('#noti-drop').on('show.bs.dropdown', function () {
        $('#notilist').html();
        getNoti();
    })
    $('#notilist').scroll(function () {
        notilist = $('#notilist');
        var maxHeight = notilist[0].scrollHeight;
        var currentScroll = notilist.scrollTop() + notilist.height();
        if (maxHeight <= currentScroll + 100) {
            getNoti();
        }
    })
//이름크기 바꾸기
    var fontResize = function () {
        var username = $('#username');
        var size = (84 / username.text().length) > 14 ? 14 : (84 / username.text().length)
        username.css('font-size', size);
    }
    fontResize();
    //핀받아오는 함수
    var pinPage = 0;
    var pinLoading = false;

    function getPin() {
        if (!pinLoading) {
            pinLoading = true;
            var spinner = $('<div>')
                .attr('data-loader', 'spinner')
                .addClass('load-item pin-load')
            $('#pinlist').append(spinner);
            $.ajax({
                url: "/php/data/getPin.php",
                type: "GET",
                data: {action: "loadpin", nowpage: pinPage},
                dataType: 'json',
                success: function (res) {
                    spinner.detach();
                    for (var i = 0; i < res.length; i++) {
                        $('<li>').addClass('pin-list').append(
                            $('<div>').append(
                                $('<img>').attr({
                                    src: res[i]['WRITER_PIC'],
                                    onclick: 'location.href="/profile/' + res[i]['ID_WRITER'] + '"'
                                }).addClass('pin-pic')).addClass('pin-pin-wrap')
                            , $('<a>').addClass('pin-body').attr('href', '/content/' + res[i]['ID_CONTENT']).text(res[i]['BODY'])
                            , $('<span>').text(res[i]['KNOCK']).addClass('pin-knock')
                            , $('<span>').text(res[i]['REPLY']).addClass('pin-reply')
                            , $('<span>').addClass('pubico pico-Pin_002 del-pin')
                        ).insertAfter($('#pinlist li:last-child'))
                        pinPage = pinPage + 1;
                    }
                    if (!$('#pinlist').hasClass('loaded')) {
                        $('#pinlist').addClass('loaded');
                    }
                    pinLoading = false;
                }, error: function (xhr, status, error) {
                    errorReport("getPin", {action: "loadpin", nowpage: pinPage}, status, error)
                    //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                }
            })
        }
    }

    //핀 리스트에서 del-pin 클릭시 핀 리스트에서 삭제
    $(document).on('click', '.del-pin', function (e) {
        e.stopPropagation();
        var ID = $(this).siblings('a').attr('href').replace('/content/', '');
        var li = $(this).parent();
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {ID: ID, token: token, action: "delPin", userID: mid},
            dataType: 'json',
            success: function (res) {
                if (res['result'] == 'Y') {
                    li.fadeOut(function () {
                        li.remove();
                    })
                } else {
                    alert('작업중 문제가 생겼습니다.')
                }
            }, error: function (xhr, status, error) {
                errorReport("delPin", {ID: ID, token: token, action: "delPin", userID: mid}, status, error);
                //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
            }
        })
    });
    //핀리스트
    $('#pin-drop').on('show.bs.dropdown', function () {
        $('#pinlist').html();
        getPin();
    })
    //무한 휠!!
    $('#pinlist').scroll(function () {
        pinlist = $('#pinlist');
        var maxHeight = pinlist[0].scrollHeight;
        var currentScroll = pinlist.scrollTop() + pinlist.height();
        if (maxHeight <= currentScroll + 100) {
            getPin();
        }
    })

})
