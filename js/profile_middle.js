/**
 * Created by gangdong-gyun on 2016. 2. 24..
 */
$(document).ready(function () {
    function itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername,pic) {
        write = '<div class="item card" id="';
        write += seq;
        write += '"><div class="header">';
        write += '<img src="'+pic+'" class="profilepic">';
        write += '<div class="writer"><a href="/php/profile.php?id=' + writer + '">'
        write += name + '</a>&nbsp;'
        if (folderseq) {
            write += date + '&nbsp;<a href="/php/foldercon.php?fid=' + folderseq + '">' + foldername + '</a>&nbsp;';
        } else {
            write += date + '&nbsp;비분류&nbsp;';
        }
        write += '<a href="#">대상</a>'
        write += '</div> <div class="conf"><a>핀</a>'
        write += '<div class="btn-group"> <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">설정<span class="caret"></span> </button> '
        if (mid == writer) {
            write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod">수정</a></li><li><a class="itemDel">삭제</a></li><li><a class="itemTop">최상단 컨텐츠로</a></li> </ul></div><br>'
        } else {
            write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li><li><a>궁금궁금</a></li> </ul></div><br>'
        }
        write += '</div></div> <div class="body">'
        write += preview;
        write += '</div> <div class="tail"> <table><tr><td class="tknock"><span class="knock"><a>노크</a><span class="badgea"> ';
        write += knock;
        write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
        write += comment + '</span></span></td>'
        write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
        write += '<td class="tprice"><span class="price bought"><a>더보기</a></span></td></tr></table></div> </div>';
        return write;
    }

    function itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername,pic) {
        write = '<div class="item-for-sale card" id="';
        write += seq;
        write += '"><div class="header">';
        write += '<img src="'+pic+'" class="profilepic">';
        write += '<div class="writer"><a href="/php/profile.php?id=' + writer + '">'
        write += name + '</a>&nbsp;'
        if (folderseq) {
            write += date + '&nbsp;<a href="/php/foldercon.php?fid=' + folderseq + '">' + foldername + '</a>&nbsp;';
        } else {
            write += date + '&nbsp;비분류&nbsp;';
        }
        write += '<a href="#">대상</a>'
        write += '</div> <div class="conf"><a>핀</a>'
        write += '<div class="btn-group"> <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">설정<span class="caret"></span> </button> '
        if (mid == writer) {
            write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod">수정</a></li><li><a class="itemDel">삭제</a></li><li><a class="itemTop">최상단 컨텐츠로</a></li> </ul></div><br>'
        } else {
            write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li><li><a>궁금행</a></li> </ul></div><br>'
        }
        write += '</div><div class="title">';
        write += title;
        write += '</div></div> <div class="body">'
        write += preview;
        write += '</div> <div class="tail"> <table><tr><td class="tknock"><span class="knock"><a>노크</a><span class="badgea"> ';
        write += knock;
        write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
        write += comment + '</span></span></td>'
        write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
        if (bought) {
            write += '<td class="tprice"><span class="price bought"><a>더보기</a></span></td></tr></table></div> </div>';
        }
        else {
            write += '<td class="tprice"><span class="price"><a class="value">' + price + '</a>&nbsp;<a>Pigs</a></span></td></tr></table></div> </div>';
        }
        return write;
    }

    //페이지 로드 끝나면 아이템카드 불러오기
    var page = 0;
    var loadOption = {seq: mid, nowpage: page, profile: targetseq};
    $.ajax({
        url: "/php/data/getContent.php",
        type: "GET",
        data: loadOption,
        dataType: 'json',
        success: function (res) {
            var times = Math.min(9,res.length-1);
            for (var i = times; i >= 0; i--) {
                if (res[i]['USER_NAME'] != null) {
                    if (res[i]['FOR_SALE'] == "N") {
                        var write = '';
                        var seq = res[i]['SEQ'];
                        var writer = res[i]['SEQ_WRITER'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['WRITE_DATE'];
                        var knock = res[i]['KNOCK'];
                        var comment = res[i]['COMMENT'];
                        var preview = res[i]['PREVIEW'];
                        var pic = res[i]['PIC'];
                        var folderseq = null;
                        var foldername = null;
                        if (res[i]['FOLDER'] != null) {
                            folderseq = res[i]['FOLDER'];
                            foldername = res[i]['FOLDER_NAME'];
                        }
                        write = itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername,pic);
                        $('#topcon').after(write);
                    } else {
                        var write = '';
                        var seq = res[i]['SEQ'];
                        var writer = res[i]['SEQ_WRITER'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['WRITE_DATE'];
                        var title = res[i]['TITLE'];
                        var knock = res[i]['KNOCK'];
                        var price = res[i]['PRICE'];
                        var comment = res[i]['COMMENT'];
                        var bought = res[i]['BOUGHT'];
                        var preview = res[i]['PREVIEW'];
                        var pic = res[i]['PIC'];
                        var folderseq = null;
                        var foldername = null;
                        if (res[i]['FOLDER'] != null) {
                            folderseq = res[i]['FOLDER'];
                            foldername = res[i]['FOLDER_NAME'];
                        }
                        write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername,pic);
                        $('#topcon').after(write);

                    }
                }
            }
            page = page + 1;
            loadOption['nowpage'] = page;
        }, error: function (request) {
            alert('request.responseText');
        }
    })
    //탑컨텐츠 탐색
    $.ajax({
        url: "/php/data/getOne.php",
        type: "GET",
        data: loadOption,
        dataType: 'json',
        success: function (res) {
            if(res['result']=='N' && res['reason']=='no top'){
                return;
            }else if(res['result']=='N' && res['reason']=='deleted'){
                return;
            }else{
                if (res['FOR_SALE'] == "N") {
                    var write = '';
                    var seq = res['SEQ'];
                    var writer = res['SEQ_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var knock = res['KNOCK'];
                    var comment = res['COMMENT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var folderseq = null;
                    var foldername = null;
                    if (res['FOLDER'] != null) {
                        folderseq = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    write = itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername,pic);
                    $('#topcon').append(write);
                } else {
                    var write = '';
                    var seq = res['SEQ'];
                    var writer = res['SEQ_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var title = res['TITLE'];
                    var knock = res['KNOCK'];
                    var price = res['PRICE'];
                    var comment = res['COMMENT'];
                    var bought = res['BOUGHT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var folderseq = null;
                    var foldername = null;
                    if (res['FOLDER'] != null) {
                        folderseq = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername,pic);
                    $('#topcon').append(write);
                }
            }
        }, error: function (request) {
            alert(request.responseText);
        }
    })
    //무한스크롤
    $(document).scroll(function () {
        var maxHeight = $(document).height();
        var currentScroll = $(window).scrollTop() + $(window).height();
        if (maxHeight <= currentScroll + 400) {
            $.ajax({
                url: "/php/data/getContent.php",
                type: "GET",
                data: loadOption,
                dataType: 'json',
                success: function (res) {
                    for (var i = 0; i < res.length; i++) {
                        if (res[i]['USER_NAME'] != null) {
                            if (res[i]['FOR_SALE'] == "N") {
                                var write = '';
                                var seq = res[i]['SEQ'];
                                var writer = res[i]['SEQ_WRITER'];
                                var name = res[i]['USER_NAME'];
                                var date = res[i]['WRITE_DATE'];
                                var knock = res[i]['KNOCK'];
                                var comment = res[i]['COMMENT'];
                                var preview = res[i]['PREVIEW'];
                                var pic = res[i]['PIC'];
                                var folderseq = null;
                                var foldername = null;
                                if (res[i]['FOLDER'] != null) {
                                    folderseq = res[i]['FOLDER'];
                                    foldername = res[i]['FOLDER_NAME'];
                                }
                                write = itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername,pic);
                                $('.card:last-child').after(write);
                            } else {
                                var write = '';
                                var seq = res[i]['SEQ'];
                                var writer = res[i]['SEQ_WRITER'];
                                var name = res[i]['USER_NAME'];
                                var date = res[i]['WRITE_DATE'];
                                var title = res[i]['TITLE'];
                                var knock = res[i]['KNOCK'];
                                var price = res[i]['PRICE'];
                                var comment = res[i]['COMMENT'];
                                var bought = res[i]['BOUGHT'];
                                var preview = res[i]['PREVIEW'];
                                var pic = res[i]['PIC'];
                                var folderseq = null;
                                var foldername = null;
                                if (res[i]['FOLDER'] != null) {
                                    folderseq = res[i]['FOLDER'];
                                    foldername = res[i]['FOLDER_NAME'];
                                }
                                write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername,pic);
                                $('.card:last-child').after(write);
                            }
                        }
                    }
                    page = page + 1;
                    loadOption['nowpage'] = page;
                }, error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
        }
    })

    //노크버튼 동작
    $(document).on("click", ".knock", function () {
        var thisitemID = $(this).parents()[5].id;
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {seq: thisitemID, action: "knock", userseq: mid,token:token,age:age},
            dataType: 'json',
            success: function (res) {
                if (res['result'] != 'N') {
                    $('#' + thisitemID + ' .knock .badgea').text(res['KNOCK']);
                } else if (res['reason'] == 'already') {
                    alert('이미 노크하신 게시물입니다.');
                }
            }, error: function (request, status, error) {
                alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
            }
        })
    });
    //코멘트 버튼 동작(처음 댓글 불러오기)
    $(document).on("click", ".comment", function () {
        var thisitemID = $(this).parents()[5].id;
        var tail = $('#' + thisitemID + ' .tail');
        $(this).parent().css('background-color', '#f4f4f4');
        tail.css('margin-bottom', '10px');
        if (!tail.hasClass('opend')) {
            var word = '<div role="tabpanel">'
            //정렬별 선택패널
            word += '<ul class="nav nav-tabs" role="tablist" id="repnav-"' + thisitemID + '>'
            word += '<li role="presentation" class="active"><a class="bestrep" href="#best-' + thisitemID + '" aria-controls="best-' + thisitemID + '" role="tab" data-toggle="tab">베스트 댓글</a></li>'
            word += '<li role="presentation"><a class="timerep" href="#time-' + thisitemID + '" aria-controls="time-' + thisitemID + '" role="tab" data-toggle="tab">시간순 댓글</a></li>'
            word += '<li role="presentation"><a class="frierep" href="#frie-' + thisitemID + '" aria-controls="frie-' + thisitemID + '" role="tab" data-toggle="tab">내 친구가 쓴 댓글</a></li>'
            word += '</ul>'
            //정렬별 댓글
            word += '<div class="tab-content">'
            word += '      <div role="tabpanel" class="tab-pane active" id="best-' + thisitemID + '"></div>'
            word += '   <div role="tabpanel" class="tab-pane" id="time-' + thisitemID + '"></div>'
            word += '    <div role="tabpanel" class="tab-pane" id="frie-' + thisitemID + '"></div>'
            word += '    </div></div>'
            tail.append(word);
            tail.append('<input type="text" class="commentReg form-control" placeholder="작성자 && 다른 사람과 신명나는 키배한판!!" style="width: 510px;height: 25px;">');

            $.ajax({
                url: "/php/data/itemAct.php",
                type: "GET",
                data: {seq: thisitemID, action: "comment", sort: "first", userseq: mid, token: token, age: age},
                dataType: 'json',
                success: function (res) {
                    function registRep(res, where) {
                        var list = $('#' + thisitemID + ' .tail ' + '#' + where);
                        list.html('');
                        for (var i = 0; i < Object.keys(res).length - 1; i++) {
                            var write = '';
                            var seq = res[i]['SEQ'];
                            var name = res[i]['USER_NAME'];
                            var date = res[i]['REPLY_DATE'];
                            var reply = res[i]['REPLY'];
                            var knock = res[i]['KNOCK'];
                            write += '<div class=commentReply id="rep-' + seq + '">';
                            write += '<table style="margin-top: 10px;"><tr><td style="width: 54px;height: 34px;"><img src="' + res[i]['PIC'] + '" class="profilepic"></td>';
                            write += '<td class="rep"><span class="writer"> <a href="/php/profile.php?id=' + res[i]['SEQ_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;">' + reply + '<span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">대댓글</a></span></span></td></tr></table></div>';
                            list.append(write);
                            var ind = parseInt(list.attr('index')) + 1;
                            list.attr('index', ind);
                        }
                        list.append('<div style="height: 20px;text-align: center" class="cursor"><span class="caret repbtn" style="cursor: pointer;"></span></div>')
                    }

                    //각 패널에 인덱스 붙이기
                    if (!$('#best-' + thisitemID).attr('index')) {
                        $('#best-' + thisitemID).attr('index', '0');
                    }
                    if (!$('#time-' + thisitemID).attr('index')) {
                        $('#time-' + thisitemID).attr('index', '0');
                    }
                    if (!$('#frie-' + thisitemID).attr('index')) {
                        $('#frie-' + thisitemID).attr('index', '0');
                    }

                    if (res['sort'] == 'best') {//각 탭별로 인덱스 심어서 페이지 나누기함
                        registRep(res, 'best-' + thisitemID);
                    } else if (res['sort'] == 'time') {
                        var word = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;">아직 베스트 댓글이 없어요 >,.<;;</div>';
                        $('#best-' + thisitemID).append(word);
                        registRep(res, 'time-' + thisitemID);
                    } else if (res['result'] == 'NO') {
                        var word = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;">아직 베스트 댓글이 없어요 >,.<;;</div>';
                        var word2 = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;">아직 댓글이 없어요 >,.<;;</div>';
                        $('#best-' + thisitemID).append(word);
                        $('#time-' + thisitemID).append(word2);

                    }
                }, error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
            tail.addClass('opend');
        }
    });
    //각 탭 버튼 동작
    $(document).on('click','.bestrep,.timerep,.frierep', function () {
        var target=$(this).attr('aria-controls');
        var index=$('#'+target).attr('index');
        var sort = target.substring(0, 4);
        var num = target.substring(5);
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "GET",
            data: {seq: num, action: "comment", userseq: mid, index: index, sort: sort, token: token, age: age},
            dataType: 'json',
            success: function (res) {
                function registRep(res, where) {
                    var list = $('#' + num + ' .tail ' + '#' + where);
                    for (var i = 0; i < Object.keys(res).length - 1; i++) {
                        var write = '';
                        var seq = res[i]['SEQ'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REPLY'];
                        var knock = res[i]['KNOCK'];
                        write += '<div class=commentReply id="rep-' + seq + '">';
                        write += '<table style="margin-top: 10px;"><tr><td style="width: 54px;height: 34px;"><img src="' + res[i]['PIC'] + '" class="profilepic"></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/php/profile.php?id=' + res[i]['SEQ_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;">' + reply + '<span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">대댓글</a></span></span></td></tr></table></div>';
                        list.append(write);
                        var ind = parseInt(list.attr('index')) + 1;
                        list.attr('index', ind);
                    }
                    list.append('<div style="height: 20px;text-align: center" class="cursor"><span class="caret repbtn" style="cursor: pointer;"></span></div>')
                }
                if(parseInt(index)==0 && res['result']!='NO') {
                    registRep(res, sort + '-' + num);
                }
            }, error: function (request, status, error) {
                alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
            }
        })
    })
//댓글에서 각 탭에 화살표버튼 새로운 코멘트들을 불러오는거
    $(document).on('click', '.repbtn', function (e) {
        var target = $(this).parents()[1].id;
        var sort = target.substring(0, 4);
        var num = target.substring(5);
        var panel = $('#' + sort + '-' + num);
        var index = panel.attr('index');
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "GET",
            data: {seq: num, action: "more_comment", userseq: mid, index: index, sort: sort, token: token, age: age},
            dataType: 'json',
            success: function (res) {
                if(res['result']=='NO'){
                    $('#' + sort + '-' + num + ' .cursor').remove();
                    return;
                }
                function registRep(res, where) {
                    var list = $('#' + num + ' .tail ' + '#' + where);
                    var btn = $('#' + sort + '-' + num + ' .cursor');
                    for (var i = 0; i < Object.keys(res).length - 1; i++) {
                        var write = '';
                        var seq = res[i]['SEQ'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REPLY'];
                        var knock = res[i]['KNOCK'];
                        write += '<div class=commentReply id="rep-' + seq + '">';
                        write += '<table style="margin-top: 10px;"><tr><td style="width: 54px;height: 34px;"><img src="' + res[i]['PIC'] + '" class="profilepic"></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/php/profile.php?id=' + res[i]['SEQ_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;">' + reply + '<span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">대댓글</a></span></span></td></tr></table></div>';
                        btn.before(write);
                        var ind = parseInt(list.attr('index')) + 1;
                        list.attr('index', ind);
                    }
                }

                registRep(res, sort + '-' + num);
            }, error: function (request, status, error) {
                alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
            }
        })
    })
    //코멘트 등록 동작
    $(document).on("keydown", ".commentReg", function (e) {
        if (e.keyCode == 13) {
            var thisitemID = $(this).parents()[1].id;
            var reply = $('#' + thisitemID + ' .tail .form-control').val();
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {seq: thisitemID, action: "commentreg", userseq: mid, comment: reply,token:token,age:age},
                dataType: 'json',
                success: function (res) {
                    $('#' + thisitemID + ' .comment .badgea').text(res['COMMENT']);
                    $('#' + thisitemID + ' .comment').trigger('click');
                    $('#' + thisitemID + ' .tail .form-control').val('');
                    $('#' + thisitemID + ' .tail .form-control').blur();
                }, error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
        }
    });
    //구매버튼(가격표시)동작
    var previewarr = [];
    $(document).on("click", ".price", function () {
        var thisitemID = $(this).parents()[5].id;
        var priceSpan = $('#' + thisitemID + ' .tail .price')
        //안산상태에서 한번 눌려졌을때 한번 더누르면 구매됨
        if (priceSpan.hasClass('buyConfirm')) {
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {
                    seq: thisitemID,
                    action: "buy",
                    userseq: mid,
                    price: $('#' + thisitemID + ' .tail .price .value').text(),token:token,age:age
                },
                dataType: 'json',
                success: function (res) {
                    if (res['buy'] == 'f') {
                        alert('구매 실패 : ' + res['reason']);
                    } else {
                        priceSpan.html('<a>더보기</a>');
                        priceSpan.removeClass('buyConfirm').addClass('bought');
                    }
                }
                , error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
        } else if (priceSpan.hasClass('bought')) {
            //산상태에서는 더보기 버튼의 역할을 함
            var body = $('#' + thisitemID + ' .body');
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "GET",
                data: {seq: thisitemID, action: "more", userseq: mid,token:token,age:age},
                dataType: 'json',
                success: function (res) {
                    previewarr['' + thisitemID] = $('#' + thisitemID + ' .body').html();
                    body.html('<div id="links' + thisitemID + '">' + res['BODY'] + '</div>');
                    priceSpan.html('<a>접기</a>');
                    priceSpan.removeClass('bought').addClass('expanded');
                }
                , error: function (request) {
                    alert(request.responseText);
                }
            })
        } else if (priceSpan.hasClass('expanded')) {
            //확장된상태에서는 접기역할을 함
            var body = $('#' + thisitemID + ' .body');
            body.html(previewarr['' + thisitemID]);
            priceSpan.html('<a>더보기</a>');
            priceSpan.removeClass('expanded').addClass('bought');
        } else {
            //사지도 않고 클릭도 안했을땐 구매하기 문자열을 추가하고 구매확정 확인 클래스를 넣음
            priceSpan.append('&nbsp;<a>구매하기?</a>')
            priceSpan.addClass('buyConfirm');
        }
    });
//삭제버튼 동작
    $(document).on("click", ".itemDel", function (e) {
        var thisitemID = $(this).parents()[5].id;
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {seq: thisitemID, action: "del",token:token,age:age},
            dataType: 'json',
            success: function (res) {
                if (res['result'] == 'Y') {
                    $('#' + thisitemID).remove();
                    alert('게시물이 삭제되었습니다.');
                } else {
                    alert('게시물이 삭제되는 도중 오류가 생겼습니다. 관리자에게 문의해 주세요.');
                }
            }, error: function (request) {
                alert(request.responseText);
            }
        })
    });
    //최상단컨텐츠 버튼 동작
    $(document).on("click", ".itemTop", function (e) {
        var thisitemID = $(this).parents()[5].id;
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {seq: thisitemID, action: "top", mid: mid,token:token,age:age},
            dataType: 'json',
            success: function (res) {
                if (!res['result'] == 'Y') alert('오류가 생겼습니다. 관리자에게 문의해 주세요.');
            }, error: function (request) {
                alert(request.responseText);
            }
        })
    });
    //댓글 노크클릭시의 동작
    $(document).on("click", ".repknock", function (e) {
        var thisrepID = $(this).parents()[6].id;
        var thisrepnum = thisrepID.substring(4);
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {seq: thisrepnum, action: "repknock",mid:mid,token:token,age:age},
            dataType: 'json',
            success: function (res) {
                if(res['result']=='N' && res['reason']=='already'){ alert('이미 노크하신 댓글입니다.');}
                else{
                    $('#'+thisrepID+' .repknockbad').text(res['knock']);
                }
            }, error: function (request) {
                alert(request.responseText);
            }
        })
    });
    //글쓸때 버튼 클릭할때의 동작
    $('#sendButton').on('click', function () {
        var $btn = $(this).button('loading');
        if ($('#sendBody').html().length > 0) {
            $.ajax({
                url: "/php/data/uploadContent.php",
                type: "POST",
                data: {body: $('#sendBody').html(),
                    seq_writer: mid,
                    folder: $folderseq,
                    token:token,age:age,
                    tag:$('#taginputs').val(),
                    expose:expose
                },
                dataType: 'json',
                success: function (res) {
                    var write = '';
                    var seq = res['SEQ'];
                    var writer = res['SEQ_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var knock = res['KNOCK'];
                    var comment = res['COMMENT'];
                    var preview = res['PREVIEW'];
                    var pic=res['PIC'];
                    var folderseq = null;
                    var foldername = null;
                    if (res['FOLDER'] != null) {
                        folderseq = res['FOLDER'];
                        foldername = res['DIR'];
                    }
                    write = itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername,pic);
                    $('#upform').after(write);
                    $('#sendBody').html("").trigger('keyup');
                },
                error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
        }
        $btn.button('reset');
    })
    //publixh 버튼 내용
    $('#publixhButton').on('click', function () {
        var $btn = $(this).button('loading');
        if ($('#publiBody').html().length > 0 && $('#saleTitle').val().length > 0) {
            $.ajax({
                url: "/php/data/uploadContent.php",
                type: "POST",
                data: {
                    body: $('#publiBody').html(), seq_writer: mid,
                    for_sale: "Y", price: $('#contentCost').val(),
                    category: category,sub_category:sub_category, age: $('#adult').is(':checked'), ad: $('#ad').is(':checked'),
                    title: $('#saleTitle').val(), folder: $folderseq,token:token,age:age,tag:$('#tag-inputp').val(),
                    expose:expose
                },
                dataType: 'json',
                success: function (res) {
                    var write = '';
                    var seq = res['SEQ'];
                    var writer = res['SEQ_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var title = res['TITLE'];
                    var knock = res['KNOCK'];
                    var price = res['PRICE'];
                    var comment = res['COMMENT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var folderseq = null;
                    var foldername = null;
                    if (res['FOLDER'] != null) {
                        folderseq = res['FOLDER'];
                        foldername = res['DIR'];
                    }
                    write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, true, preview, writer, folderseq, foldername,pic);
                    $('#upform').after(write);
                    $('#saleTitle').val("");
                    $('#contentCost').val("");
                    $('#publiBody').html("").trigger('keyup');
                },
                error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
        }else{
            console.log('본문과 제목을 입력해 주세요.')
        }
        $btn.button('reset');
    })
    //공개설정 버튼
    var expose=3;   //기본값 전체공개
    $('#expSublist li').click(function () {
        var exptarget = $(this).text()
        $('#exposeSettingSub').text(exptarget);
        switch (exptarget) {
            case '나만보기':expose=0;
                break;
            case '친구에게 공개':expose=1;
                break;
            case '팔로워에게 공개':expose=2;
                break;
            case '전체공개':expose=3;
                break;
        }
    })
    //폴더설정 버튼
    var $folderseq;
    $('#dirSublist li').click(function () {
        $('#directorySettingSub').text($(this).text());
        $folderseq = $(this).attr('folderid');
    })
    //카테고리 리스트 버튼
    var category=null;
    $('#categorySelect li').click(function () {
        $('#category').text($(this).text());
        category = $(this).text();
        function subwrite(sub){
            $('#subcategorySelect').html('');
            var write='';
            for(var i=0;i<sub.length;i++){
                write+='<li><a>'+sub[i]+'</a></li>'
            }
            $('#subcategorySelect').html(write);
        }
        switch (category) {
            case '만화':
                var sub = ['로맨스', '판타지', '개그', '미스터리', '호러', 'SF', '무협', '스포츠'];
                subwrite(sub);
                break;
            case '사진':var sub = ['일상','모델','행사','자연','여행','동식물','스포츠','아트','야경','별사진'];
                subwrite(sub);
                break;
            case '일러스트':var sub = ['일반','캐릭터'];
                subwrite(sub);
                break;
            case 'e-book':var sub = ['소설','시','에세이','인문','자기개발','교육'];
                subwrite(sub);
                break;
            case '매거진':var sub = ['IT','게임','뷰티','패션','반려동물','소품','DIY'];
                subwrite(sub);
                break;
            case 'CAD':var sub = ['3D프린팅'];
                subwrite(sub);
                break;
            case 'VR':var sub = ['일상','행사','자연','여행','스포츠','야경'];
                subwrite(sub);
                break;
            case '맛집':var sub = [];
                subwrite(sub);
                break;
            case '여행':var sub = ['국내','제주도','일본','동남아','유럽','남미','북미','동북아','오세아니아','아프리카','극지방','중앙아시아'];
                subwrite(sub);
                break;
        }
    })
    //하위 카테고리 리스트 버튼
    var sub_category;
    $(document).on('click',"#subcategorySelect li", function () {
        $('#sub-category').text($(this).text());
        sub_category = $(this).text();
    })
    //가격입력 숫자 검사
    var checkNum = /^[0-9]*$/;
    var costvali = false;
    //가격 입력 검사
    $('#contentCost').on('change', function () {
        var contentCost = $('#contentCost');
        if (!checkNum.test(contentCost.val())) {
            alert('가격은 숫자로 입력해 주세요.');
            $('#contentCost').focus();
            costvali = false;
        } else if (contentCost.val().parseint > 65535) {
            alert('65535픽 이상은 입력되지 않습니다.');
            costvali = false;
        } else {
            costvali = true;
        }
    })

    //파일 업로드시 동작
    $('#fileuploads,#fileuploadp').fileupload({
        dataType: 'json',
        imageMaxWidth: 500,
        imageMaxHeight: 750,
        imageCrop: true,
        add: function (e, data) {
            var uploadFile = data.files[0];
            var isValid = true;
            if (!(/png|jpe?g|gif/i).test(uploadFile.name)) {
                alert('png, jpg, gif 만 가능합니다');
                isValid = false;
            } else if (uploadFile.size > 10000000) { // 10mb
                alert('파일 용량은 10메가를 초과할 수 없습니다.');
                isValid = false;
            }
            if (isValid) {
                data.submit();
            }
        },
        done: function (e, data) {
            if (this == $('#fileuploads')[0]) {
                $('#sendBody').html($('#sendBody').html() + "<img src='/img/" + data.result['files']['file_crop'] + "' class='BodyPic'><br><br>");
                $('#sendBody').height($('#sendBody').height() + data.result['files']['file_height']+8);
            } else if (this == $('#fileuploadp')[0]) {
                $('#publiBody').html($('#publiBody').html() + "<img src='/img/" + data.result['files']['file_crop'] + "' class='BodyPic'><br><br>");
                $('#publiBody').height($('#publiBody').height() + data.result['files']['file_height']+8);

            }
        }, fail: function (e, data) {
            // data.errorThrown
            // data.textStatus;
            // data.jqXHR;
            console.log('서버와 통신 중 문제가 발생했습니다');
            console.log('e : ' + e);
            console.log('data : ' + data);
        }
    })
});
//텍스트에이리어 입력시 자동 크기조정
function resize(obj) {
    obj.style.height = "1px";
    obj.style.height = (23 + obj.scrollHeight) + "px";
}
