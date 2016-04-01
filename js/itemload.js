/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
function itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername, pic,targetseq,targetname,expose,more) {
    write = '<div class="item card" id="';
    write += seq;
    write += '"><div class="header">';
    write += '<img src="' + pic + '" class="profilepic">';
    write += '<div class="writer"><a href="/php/profile.php?id=' + writer + '">'
    write += name + '</a>&nbsp;'
    if(targetseq){
        write+='>>> <a href="/php/profile.php?id=' + targetseq + '">'+targetname+'</a> '
    }
    if (folderseq) {
        write += date + '&nbsp;<a href="/php/foldercon.php?fid=' + folderseq + '">' + foldername + '</a>&nbsp;';
    } else {
        write += date + '&nbsp;비분류&nbsp;';
    }
    switch(expose){
        case 0:write+='나만보기';break;
        case 1:write+='친구에게 공개';break;
        case 2:write+='전체공개';break;
    }
    write += '</div> <div class="conf"><a>핀</a>'
    write += '<div class="btn-group"> <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">설정<span class="caret"></span> </button> '
    if (mid == writer) {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod">수정</a></li><li><a class="itemDel">삭제</a></li><li><a class="itemTop">최상단 컨텐츠로</a></li> </ul></div><br>'
    } else {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li><li><a>궁금궁금</a></li> </ul></div><br>'
    }
    write += '</div></div> <div class="body">'
    write += preview;
    write += '</div> <div class="tail"> <table><tr><td class="tknock"><span class="knock"><a class="pubico pico-knock">노크</a><span class="badgea"> ';
    write += knock;
    write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
    write += comment + '</span></span></td>'
    write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
    if(more) {
        write += '<td class="tprice"><span class="price bought"><a>더보기</a></span></td></tr></table></div> </div>';
    }else{
        write+='</tr></table></div> </div>';
    }
    return write;
}

function itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername, pic,expose,more) {
    write = '<div class="item-for-sale card" id="';
    write += seq;
    write += '"><div class="header">';
    write += '<img src="' + pic + '" class="profilepic">';
    write += '<div class="writer"><a href="/php/profile.php?id=' + writer + '">'
    write += name + '</a>&nbsp;'
    if (folderseq) {
        write += date + '&nbsp;<a href="/php/foldercon.php?fid=' + folderseq + '">' + foldername + '</a>&nbsp;';
    } else {
        write += date + '&nbsp;비분류&nbsp;';
    }
    switch(expose){
        case 0:write+='나만보기';break;
        case 1:write+='친구에게 공개';break;
        case 2:write+='전체공개';break;
    }
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
    write += '</div> <div class="tail"> <table><tr><td class="tknock"><span class="knock"><a class="pubico pico-knock">노크</a><span class="badgea"> ';
    write += knock;
    write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
    write += comment + '</span></span></td>'
    write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
    if (bought) {
        if(more) {
            write += '<td class="tprice"><span class="price bought"><a>더보기</a></span></td></tr></table></div> </div>';
        }else{
            write+='</tr></table></div> </div>';
        }
    }
    else {
        write += '<td class="tprice"><span class="price"><a class="value">' + price + '</a>&nbsp;<a>Pigs</a></span></td></tr></table></div> </div>';
    }
    return write;
}
$(document).ready(function(){
    //페이지 로드 끝나면 아이템카드 불러오기
    $.ajax({
        url: "/php/data//getContent.php",
        type: "GET",
        data: loadOption,
        dataType: 'json',
        success: function (res) {
            var times = Math.min(9, res.length - 1);
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
                        var targetseq = res[i]['SEQ_TARGET'];
                        var targetname = res[i]['TARGET_NAME'];
                        var folderseq = null;
                        var foldername = null;
                        var expose=res[i]['EXPOSE'];
                        var more=res[i]['MORE']
                        if (res[i]['FOLDER'] != null) {
                            folderseq = res[i]['FOLDER'];
                            foldername = res[i]['FOLDER_NAME'];
                        }
                        write = itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername, pic,targetseq,targetname,expose,more);
                        if($('#topcon').length>0){
                            $('#topcon').after(write);
                        } else if($('#upform').length>0) {
                            $('#upform').after(write);
                        }else{
                            $('#prea').after(write);
                        }

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
                        var expose=res[i]['EXPOSE'];
                        var more=res[i]['MORE']
                        if (res[i]['FOLDER'] != null) {
                            folderseq = res[i]['FOLDER'];
                            foldername = res[i]['FOLDER_NAME'];
                        }
                        write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername, pic,expose,more);
                        if($('#topcon').length>0){
                            $('#topcon').after(write);
                        } else if($('#upform').length>0) {
                            $('#upform').after(write);
                        }else{
                            $('#prea').after(write);
                        }
                    }
                }
            }
            page = page + 1;
            loadOption['nowpage'] = page;
        }
    })

    //무한스크롤
    $(document).scroll(function () {
        var maxHeight = $(document).height();
        var currentScroll = $(window).scrollTop() + $(window).height();
        if (maxHeight <= currentScroll + 400) {
            $.ajax({
                url: "/php/data/getContent.php",
                type: "get",
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
                                var targetseq = res[i]['SEQ_TARGET'];
                                var targetname = res[i]['TARGET_NAME'];
                                var folderseq = null;
                                var foldername = null;
                                var expose=res[i]['EXPOSE']
                                var more=res[i]['MORE']
                                if (res[i]['FOLDER'] != null) {
                                    folderseq = res[i]['FOLDER'];
                                    foldername = res[i]['FOLDER_NAME'];
                                }
                                write = itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername, pic,targetseq,targetname,expose,more);
                                if($('.card:last-child').length>0) {
                                    $('.card:last-child').after(write);
                                }else{
                                    $('#prea').after(write);
                                }
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
                                var expose=res[i]['EXPOSE']
                                var more=res[i]['MORE']
                                if (res[i]['FOLDER'] != null) {
                                    folderseq = res[i]['FOLDER'];
                                    foldername = res[i]['FOLDER_NAME'];
                                }
                                write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername, pic,expose,more);
                                if($('.card:last-child').length>0) {
                                    $('.card:last-child').after(write);
                                }else{
                                    $('#prea').after(write);
                                }
                            }
                        }
                    }
                }
            })
            page = page + 1;
            loadOption['nowpage'] = page;
        }
    })
});