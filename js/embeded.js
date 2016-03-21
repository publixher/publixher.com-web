/**
 * Created by gangdong-gyun on 2016. 3. 1..
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
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li><li><a>궁금궁금</a></li> </ul></div><br>'
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
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li><li><a>궁금행</a></li> </ul></div><br>'
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
    var loadOption = {getItem: iid};
    $.ajax({
        url: "/php/data/getContent.php",
        type: "GET",
        data: loadOption,
        dataType: 'json',
        success: function (res) {
            if (res[0]['USER_NAME'] != null) {
                if (res[0]['FOR_SALE'] == "N") {
                    var write = '';
                    var seq = res[0]['SEQ'];
                    var writer = res[0]['SEQ_WRITER'];
                    var name = res[0]['USER_NAME'];
                    var date = res[0]['WRITE_DATE'];
                    var knock = res[0]['KNOCK'];
                    var comment = res[0]['COMMENT'];
                    var preview = res[0]['PREVIEW'];
                    var pic = res[0]['PIC'];
                    var folderseq = null;
                    var foldername = null;
                    if (res[0]['FOLDER'] != null) {
                        folderseq = res[0]['FOLDER'];
                        foldername = res[0]['FOLDER_NAME'];
                    }
                    write = itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername,pic);
                    $('#prea').after(write);

                } else {
                    var write = '';
                    var seq = res[0]['SEQ'];
                    var writer = res[0]['SEQ_WRITER'];
                    var name = res[0]['USER_NAME'];
                    var date = res[0]['WRITE_DATE'];
                    var title = res[0]['TITLE'];
                    var knock = res[0]['KNOCK'];
                    var price = res[0]['PRICE'];
                    var comment = res[0]['COMMENT'];
                    var bought = res[0]['BOUGHT'];
                    var preview = res[0]['PREVIEW'];
                    var pic = res[0]['PIC'];
                    var folderseq = null;
                    var foldername = null;
                    if (res[0]['FOLDER'] != null) {
                        folderseq = res[0]['FOLDER'];
                        foldername = res[0]['FOLDER_NAME'];
                    }
                    write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername,pic);
                    $('#prea').after(write);
                }
            }
        }, error: function (request) {
            alert(request.responseText);
        }
    })

    //노크버튼 동작
    $(document).on("click", ".knock", function () {
        var thisitemID = $(this).parents()[5].id;
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {seq: thisitemID, action: "knock",token:token,age:age},
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
                console.log(res)
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
                data: {seq: thisitemID, action: "commentreg", comment: reply,token:token,age:age},
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
                data: {seq: thisitemID, action: "more",token:token,age:age},
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
                console.log(res);
                if(res['result']=='N' && res['reason']=='already'){ alert('이미 노크하신 댓글입니다.');}
                else{
                    $('#'+thisrepID+' .repknockbad').text(res['knock']);
                }
            }, error: function (request) {
                alert(request.responseText);
            }
        })
    });

});
