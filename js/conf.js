$(document).ready(function () {
    //검색창에 글자를 입력할때마다 db에서 검색결과 가져옴
    $('#gsearch').on('input', function () {
        //통합검색
        var search_word = $('#gsearch').val();
        if (search_word.length > 1) {
            $('#searchResult').css('display', 'block');
            function search(target){
                $.ajax({
                    url: "/php/data/nameFind.php",
                    type: "GET",
                    data: {searchword: search_word,target:target},
                    dataType: 'json',
                    success: function (res) {
                        var Result;
                        switch(target){
                            case 'name':Result=$('#nameResult');break;
                            case 'title':Result=$('#contResult');break;
                            case 'tag':Result=$('#tagResult');break;
                        }
                        if (res.length > 0) {
                            Result.css('display', 'block');
                            var SearchRes = '';
                            for (var i = 0; i < res.length; i++) {
                                switch(target){
                                    case 'name':if (res[i]['IS_NICK'] == 'Y') {
                                        SearchRes += '<li><a href="/php/profile.php?id=' + res[i]['ID'] + '">' + res[i].USER_NAME + '>>>>익명</a></li>';
                                    } else {
                                        SearchRes += '<li><a href="/php/profile.php?id=' + res[i]['ID'] + '">' + res[i].USER_NAME + '>>>>이름</a></li>';
                                    }break;
                                    case 'title':for (var i = 0; i < res.length; i++) {
                                        SearchRes += '<li><a href="/php/getItem.php?iid=' + res[i]['ID'] + '">' + res[i].TITLE + '>>>>아이템</a></li>';
                                    }break;
                                    case 'tag':for (var i = 0; i < res.length; i++) {
                                        SearchRes += '<li><a href="/php/Search.php?type=tag&tag=' + res[i]['TAG'] + '">' + res[i].TAG + '>>>>태그</a></li>';
                                    }break;
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

    //페이지 로딩이 끝나면 알림의 개수를 받아온다
    $.ajax({
        url: "/php/data/getNoti.php",
        type: "GET",
        data: {action: "confonload"},
        dataType: 'json',
        success: function (res) {
            if (res > 0) {
                $('#notibtn').append('<span class="badge">' + res + '</span>');
            }
        }
    })
    //알림을 받아오는 함수
    var page = 0;

    function getNoti() {
        $.ajax({
            url: "/php/data/getNoti.php",
            type: "GET",
            data: {action: "confnotireq", nowpage: page},
            dataType: 'json',
            success: function (res) {
                //알림문장
                for (var i = 0; i < res.length; i++) {
                    switch (res[i]['ACT']) {
                        case '1':
                            var word = "" + res[i]['TITLE'] + " 게시물에 신규 구매가 있습니다.";
                            $('#notilist li:last-child').after('<li><a href="/php/getItem.php?iid=' + res[i]['ID_CONTENT'] + '">' + word + '</a></li>');
                            break;
                        case '2':
                            var word = res[i]['USER_NAME'] + '님이 회원님과 친구가 되고싶어 합니다.';
                            $('#notilist li:last-child').after('<li><a href="/php/profile.php?id=' + mid + '">' + word + '</a></li>');
                            break;
                        case '3':
                            res[i]['TITLE'] = res[i]['TITLE'] ? res[i]['TITLE'] : '회원님의';
                            var word = "" + res[i]['TITLE'] + " 게시물에 새로운 댓글이 있습니다.";
                            $('#notilist li:last-child').after('<li><a href="/php/getItem.php?iid=' + res[i]['ID_CONTENT'] + '">' + word + '</a></li>');
                            break;
                        case '4':
                            res[i]['TITLE'] = res[i]['TITLE'] ? res[i]['TITLE'] : '회원님의';
                            var word = "" + res[i]['TITLE'] + " 게시물에 새로운 노크가 있습니다.";
                            $('#notilist li:last-child').after('<li><a href="/php/getItem.php?iid=' + res[i]['ID_CONTENT'] + '">' + word + '</a></li>');
                            break;
                        case '6':
                            var word = "회원님의 '" + res[i]['REPLY'] + "' 댓글에 새로운 노크가 있습니다.";
                            $('#notilist li:last-child').after('<li><a href="/php/getItem.php?iid=' + res[i]['ID_CONTENT'] + '">' + word + '</a></li>');
                            break;
                        case '7':
                            var word = "회원님의 '" + res[i]['REPLY'] + "' 댓글에 새로운 대댓글이 있습니다.";
                            $('#notilist li:last-child').after('<li><a href="/php/getItem.php?iid=' + res[i]['ID_CONTENT'] + '">' + word + '</a></li>');
                            break;
                    }
                }
                if (!$('#notilist').hasClass('loaded')) {
                    $('#notilist').addClass('loaded');
                }
                if ($('#notibtn .badge').length > 0) {
                    $('#notibtn .badge').remove();
                }
                page = page + 1;
            }
        })
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
    var fontResize= function () {
        var username=$('#username');
        var size=(84/username.text().length)>14?14:(84/username.text().length)
        username.css('font-size',size);
    }
    fontResize();
})