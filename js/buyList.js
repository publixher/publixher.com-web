/**
 * Created by gangdong-gyun on 2016. 2. 29..
 */
$(document).ready(function(){
    function itemLoad(write, seq, name, date, knock, comment, writer, folderseq, foldername,pic) {
        write = '<div class="item card" id="';
        write += seq;
        write += '"><div class="header">';
        write += '<img src="'+pic+'" class="profilepic">';
        write += '<div class="writer"><a href="/php/profile.php?id='+writer+'">'
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
            write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod">수정</a></li><li><a class="itemDel">삭제</a></li> </ul></div><br>'
        } else {
            write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li><li><a>궁금궁금</a></li> </ul></div><br>'
        }
        write += '</div></div>'
        write += '<div class="tail"> <table><tr><td class="tknock"><span class="knock"><a>노크</a><span class="badgea"> ';
        write += knock;
        write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
        write += comment + '</span></span></td>'
        write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
        write += '<td class="tprice"><span class="price bought"><a>더보기</a></span></td></tr></table></div> </div>';
        return write;
    }

    function itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, writer, folderseq, foldername,pic) {
        write = '<div class="item-for-sale card" id="';
        write += seq;
        write += '"><div class="header">';
        write += '<img src="'+pic+'" class="profilepic">';
        write += '<div class="writer"><a href="/php/profile.php?id='+writer+'">'
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
            write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod">수정</a></li><li><a class="itemDel">삭제</a></li> </ul></div><br>'
        } else {
            write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li><li><a>궁금행</a></li> </ul></div><br>'
        }
        write += '</div><div class="title">';
        write += title;
        write += '</div></div> '
        write += ' <div class="tail"> <table><tr><td class="tknock"><span class="knock"><a>노크</a><span class="badgea"> ';
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
    var loadOption={seq:mid,nowpage:page,buylist:"Y"};
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
                        var pic = res[i]['PIC'];
                        var folderseq=null;
                        var foldername=null;
                        if (res[i]['FOLDER'] != null) {
                            folderseq = res[i]['FOLDER'];
                            foldername = res[i]['FOLDER_NAME'];
                        }
                        write = itemLoad(write, seq, name, date, knock, comment, writer, folderseq, foldername,pic);
                        $('#prea').after(write);

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
                        var pic = res[i]['PIC'];
                        var folderseq=null;
                        var foldername=null;
                        if (res[i]['FOLDER'] != null) {
                            folderseq = res[i]['FOLDER'];
                            foldername = res[i]['FOLDER_NAME'];
                        }
                        write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, writer, folderseq, foldername,pic);
                        $('#prea').after(write);
                    }
                }
            }
            page=page+1;
            loadOption['nowpage']=page;
        }, error: function (request) {
            alert('request.responseText');
        }
    })

    //무한스크롤
    $(document).scroll(function () {
        maxHeight = $(document).height();
        currentScroll = $(window).scrollTop() + $(window).height();
        if (maxHeight <= currentScroll + 400) {
            $.ajax({
                url: "/php/data/getContent.php",
                type: "GET",
                data: loadOption,
                dataType: 'json',
                success: function (res) {
                    if(res!=null) {
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
                                    var pic = res[i]['PIC'];
                                    var folderseq = null;
                                    var foldername = null;
                                    if (res[i]['FOLDER'] != null) {
                                        folderseq = res[i]['FOLDER'];
                                        foldername = res[i]['FOLDER_NAME'];
                                    }
                                    write = itemLoad(write, seq, name, date, knock, comment, writer, folderseq, foldername,pic);
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
                                    var pic = res[i]['PIC'];
                                    var folderseq = null;
                                    var foldername = null;
                                    if (res[i]['FOLDER'] != null) {
                                        folderseq = res[i]['FOLDER'];
                                        foldername = res[i]['FOLDER_NAME'];
                                    }
                                    write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, writer, folderseq, foldername,pic);
                                    $('.card:last-child').after(write);
                                }
                            }
                        }
                    }
                    page=page+1;
                    loadOption['nowpage']=page;
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
                if(res['result']!='N') {
                    $('#' + thisitemID + ' .knock .badgea').text(res['KNOCK']);
                }else if(res['reason']=='already'){
                    alert('이미 노크하신 게시물입니다.');
                }
            }, error: function (request, status, error) {
                alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
            }
        })
    });
    //코멘트 버튼 동작
    $(document).on("click", ".comment", function () {
        var thisitemID = $(this).parents()[5].id;
        if (!$('#' + thisitemID + ' .tail .commentlist').length > 0) {
            $('#' + thisitemID + ' .tail').append('<div class="commentlist"></div><input type="text" class="commentReg form-control" placeholder="작성자 && 다른 사람과 신명나는 키배한판!!" style="width: 510px;height: 25px;">');
        }
        $('#' + thisitemID + ' .tail').css('margin-bottom','10px');
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "GET",
            data: {seq: thisitemID, action: "comment", userseq: mid,token:token,age:age},
            dataType: 'json',
            success: function (res) {
                if (res['comment'] != 'N') {
                    $('#' + thisitemID + ' .tail .commentlist').html('');
                    for (var i = 0; i < res.length; i++) {
                        var write = '';
                        var seq = res[i]['SEQ'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REPLY'];
                        var knock = res[i]['KNOCK'];
                        write += '<div class=commentReply id="rep-' + seq + '">';
                        write += '<table style="margin-top: 10px;"><tr><td style="width: 54px;height: 34px;"><img src="'+res[i]['PIC']+'" class="profilepic"></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/php/profile.php?id='+res[i]['SEQ_USER']+'">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;">' + reply + '<span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">'+knock+'</span> <a class="repreply">대댓글</a></span></span></td></tr></table></div>';
                        $('#' + thisitemID + ' .tail .commentlist').append(write);
                    }
                }
            }, error: function (request, status, error) {
                alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
            }
        })
    });
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
                    price: $('#' + thisitemID + ' .tail .price .value').text(),
                    token:token,age:age
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
