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
                $.ajax({
                    url: "/php/data/nameFind.php",
                    type: "GET",
                    data: {searchword: search_word, target: target},
                    dataType: 'json',
                    success: function (res) {
                        var Result;
                        switch (target) {
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
                                    case 'name':
                                        if (res[i]['IS_NICK'] == 'Y') {
                                            SearchRes += '<li><a href="/profile/' + res[i]['ID'] + '">' + res[i]['USER_NAME'] + '>>>>익명</a></li>';
                                        } else {
                                            SearchRes += '<li><a href="/profile/' + res[i]['ID'] + '">' + res[i]['USER_NAME'] + '>>>>이름</a></li>';
                                        }
                                        break;
                                    case 'title':
                                        for (var i = 0; i < res.length; i++) {
                                            SearchRes += '<li><a href="/content/' + res[i]['ID'] + '">' + res[i]['TITLE'] + '>>>>아이템</a></li>';
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
                    }
                });
            }

            search('name');
            search('title')
            search('tag')
        } else {
            $('#searchResult').css('display', 'none');
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
                                var word = res[i]['USER_NAME']+"님이 당신의 \""+res[i]['TITLE']+"\"게시물을 구매했습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                                break;
                            case '2':
                                var word = res[i]['USER_NAME'] + '님이 회원님과 친구가 되고싶어 합니다.';
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/profile/' + mid + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                                break;
                            case '3':
                                var word = res[i]['USER_NAME']+"님이 \"" + res[i]['TITLE'] + "\" 게시물에 새로운 댓글을 달았습니다.\""+res[i]['REPLY']+"\"";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                                break;
                            case '4':
                                var word = res[i]['USER_NAME']+"님이 \"" + res[i]['TITLE'] + "\" 게시물에 노크 했습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                                break;
                            case '7':
                                var word = "회원님이 다신 \""+res[i]['REPLY']+"\" 댓글에 "+res[i]['USER_NAME']+"님이 \""+res[i]['SUB_REPLY']+"\" 라고 댓글을 달았습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                                break;
                            case '8':
                                var word = res[i]['USER_NAME']+"님이 \""+res[i]['TITLE']+"\" 게시물에 회원님을 소환했습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
                                break;
                            case '9':
                                var word=res[i]['USER_NAME']+"님이 \""+res[i]['REPLY']+"\" 댓글에 회원님을 소환했습니다.";
                                $('#notilist li:last-child').after('<li><div class="noti-img-wrap"><img class="noti-img" src="'+res[i]['PIC']+'"></div><a href="/content/' + res[i]['ID_CONTENT'] + '">' + word + '</a><span class="noti-date">'+res[i]['NOTI_DATE']+'</span></li>');
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
                }
            })
        }
    }

    //알림버튼 누를때 서버에서 그룹화된거 다 불러오기
    $('#notibtn').on('click', function () {
        if (!$('#notilist').hasClass('loaded')) {
            getNoti();
        }
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
                        $('<li>')
                            .addClass('pin-list')
                            .append(
                                $('<div>')
                                    .append(
                                        $('<img>')
                                            .attr({
                                                src: res[i]['WRITER_PIC'],
                                                onclick: 'location.href="/profile/' + res[i]['ID_WRITER'] + '"'
                                            })
                                            .addClass('pin-pic')
                                    )
                                    .addClass('pin-pin-wrap')
                                , $('<a>')
                                    .addClass('pin-body')
                                    .attr('href', '/content/' + res[i]['ID_CONTENT'])
                                    .text(res[i]['BODY'])
                                , $('<span>')
                                    .text(res[i]['KNOCK'])
                                    .addClass('pin-knock')
                                , $('<span>')
                                    .text(res[i]['REPLY'])
                                    .addClass('pin-reply')
                            )
                            .insertAfter($('#pinlist li:last-child'))
                    }
                    if (!$('#pinlist').hasClass('loaded')) {
                        $('#pinlist').addClass('loaded');
                    }

                    pinPage = pinPage + 1;
                    pinLoading = false;
                }
            })
        }
    }

    //핀리스트
    $('#pinbtn').on('click', function () {
        if (!$('#pinlist').hasClass('loaded')) {
            getPin();
        }
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