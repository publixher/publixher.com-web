/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
function itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag,pin) {
    write = '<div class="item card" id="';
    write += ID;
    write += '"><div class="header">';
    write += '<div class="item-profile-wrap"><img src="' + pic + '" class="profilepic"></div>';
    write += '<div class="writer"><a href="/profile/' + writer + '">'
    write += name + '</a>&nbsp;'
    if (targetID) {
        write += '>>> <a href="/profile/' + targetID + '">' + targetname + '</a> '
    }
    if (folderID) {
        write += date + '&nbsp;<a href="/folder/' + folderID + '">' + foldername + '</a>&nbsp;';
    } else {
        write += date + '&nbsp;비분류&nbsp;';
    }
    switch (expose) {
        case 0:
            write += '나만보기';
            break;
        case 1:
            write += '친구에게 공개';
            break;
        case 2:
            write += '전체공개';
            break;
    }
    write += '</div> <div class="conf">';
    if(pin.indexOf(ID) !=-1){   //핀에 아이디가 있을경우
        write += '<a class="pin-a pubico pico-pin2 pinned">핀</a>';
    }else {
        write += '<a class="pin-a">핀</a>';
    }
    write += '<div class="btn-group"> <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">설정<span class="caret"></span> </button> '

    if (mid == writer || level==99) {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod">수정</a></li><li><a class="itemDel">삭제</a></li><li><a class="itemTop">최상단 컨텐츠로</a></li> </ul></div><br>'
    } else {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li></ul></div><br>'
    }

    write += '</div></div> <div class="body">'
    write += preview + '</div>';
    if (tag) {
        write += '<br><br>'
        for (var i = 0; i < tag.length; i++) {
            write += ' <a href="/tag/' + tag[i] + '" class="body-tag">' + tag[i] + '</a>'
        }
    }
    write += '<div class="tail"> <table><tr><td class="tknock"><span class="knock"><span class="pubico pico-knock"></span><a>노크</a><span class="badgea"> ';
    write += knock;
    write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
    write += comment + '</span></span></td>'
    write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
    if (more == '1') {
        write += '<td class="tprice"><span class="price bought"><a>더보기</a></span></td></tr></table></div> </div>';
    } else {
        write += '<td class="blank"></td> </tr></table></div> </div>';
    }
    return write;
}

function itemForSaleLoad(write, ID, name, date, title, knock, price, comment, bought, preview, writer, folderID, foldername, pic, expose, more, tag,pin) {
    write = '<div class="item-for-sale card" id="';
    write += ID;
    write += '"><div class="header">';
    write += '<div class="item-profile-wrap"><img src="' + pic + '" class="profilepic"></div>';
    write += '<div class="writer"><a href="/profile/' + writer + '">'
    write += name + '</a>&nbsp;'
    if (folderID) {
        write += date + '&nbsp;<a href="/folder/' + folderID + '">' + foldername + '</a>&nbsp;';
    } else {
        write += date + '&nbsp;비분류&nbsp;';
    }
    switch (expose) {
        case 0:
            write += '나만보기';
            break;
        case 1:
            write += '친구에게 공개';
            break;
        case 2:
            write += '전체공개';
            break;
    }
    write += '</div> <div class="conf">';
    if(pin.indexOf(ID) !=-1){   //핀에 아이디가 있을경우
        write += '<a class="pin-a pubico pico-pin2 pinned">핀</a>';
    }else {
        write += '<a class="pin-a">핀</a>';
    }
    write += '<div class="btn-group"> <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">설정<span class="caret"></span> </button> '
    if (mid == writer || level==99) {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod">수정</a></li><li><a class="itemDel">삭제</a></li><li><a class="itemTop">최상단 컨텐츠로</a></li> </ul></div><br>'
    } else {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport">신고</a></li></ul></div><br>'
    }
    write += '</div><div class="title">';
    write += title;
    write += '</div></div> <div class="body">'
    write += preview + '</div>';
    if (tag) {
        write += '<br><br>'
        for (var i = 0; i < tag.length; i++) {
            write += ' <a href="/tag/' + tag[i] + '" class="body-tag">' + tag[i] + '</a>'
        }
    }
    write += '<div class="tail"> <table><tr><td class="tknock"><span class="knock"><span class="pubico pico-knock"></span><a>노크</a><span class="badgea"> ';
    write += knock;
    write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
    write += comment + '</span></span></td>'
    write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
    if (bought) {
        if (more == '1') {
            write += '<td class="tprice"><span class="price bought"><a>더보기</a></span></td></tr></table></div> </div>';
        } else {
            write += '<td class="blank"></td></tr></table></div> </div>';
        }
    }
    else {
        write += '<td class="tprice"><span class="price"><a class="value">' + price + '</a>&nbsp;<a>Pigs</a></span></td></tr></table></div> </div>';
    }
    return write;
}
var spinner = '<div class="load-item" data-loader="spinner"></div>'
$(document).ready(function () {
    //페이지 로드 끝나면 아이템카드 불러오기
    if ($('#topcon').length > 0) {
        $('#topcon').after(spinner);
    } else if ($('#upform').length > 0) {
        $('#upform').after(spinner);
    } else {
        $('#prea').after(spinner);
    }
    $.ajax({
        url: "/php/data/getContent.php",
        type: "get",
        data: loadOption,
        dataType: 'json',
        tryCount: 0,
        retryLimit: 3,
        success: function (res) {
            if(res.length==0){
                write='<div>결과가 없네요 >,.<;;</div>'
                if ($('#topcon').length > 0) {
                    $('#topcon').after(write);
                } else if ($('#upform').length > 0) {
                    $('#upform').after(write);
                } else {
                    $('#prea').after(write);
                }
            }else {
                $('.load-item').remove();
                var times = Math.min(9, res.length - 1);
                for (var i = times; i >= 0; i--) {
                    if (res[i]['USER_NAME'] != null) {
                        if (res[i]['FOR_SALE'] == "N") {
                            var write = '';
                            var ID = res[i]['ID'];
                            var writer = res[i]['ID_WRITER'];
                            var name = res[i]['USER_NAME'];
                            var date = res[i]['WRITE_DATE'];
                            var knock = res[i]['KNOCK'];
                            var comment = res[i]['COMMENT'];
                            var preview = res[i]['PREVIEW'];
                            var pic = res[i]['PIC'];
                            var targetID = res[i]['ID_TARGET'];
                            var targetname = res[i]['TARGET_NAME'];
                            var folderID = null;
                            var foldername = null;
                            var expose = res[i]['EXPOSE'];
                            var more = res[i]['MORE'];
                            if (res[i]['FOLDER'] != null) {
                                folderID = res[i]['FOLDER'];
                                foldername = res[i]['FOLDER_NAME'];
                            }
                            var tag = res[i]['TAG'] ? res[i]['TAG'].split(' ') : null;
                            write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag, pin);
                            if ($('#topcon').length > 0) {
                                $('#topcon').after(write);
                            } else if ($('#upform').length > 0) {
                                $('#upform').after(write);
                            } else {
                                $('#prea').after(write);
                            }

                        } else {
                            var write = '';
                            var ID = res[i]['ID'];
                            var writer = res[i]['ID_WRITER'];
                            var name = res[i]['USER_NAME'];
                            var date = res[i]['WRITE_DATE'];
                            var title = res[i]['TITLE'];
                            var knock = res[i]['KNOCK'];
                            var price = res[i]['PRICE'];
                            var comment = res[i]['COMMENT'];
                            var bought = res[i]['BOUGHT'];
                            var preview = res[i]['PREVIEW'];
                            var pic = res[i]['PIC'];
                            var folderID = null;
                            var foldername = null;
                            var expose = res[i]['EXPOSE'];
                            var more = res[i]['MORE']
                            if (res[i]['FOLDER'] != null) {
                                folderID = res[i]['FOLDER'];
                                foldername = res[i]['FOLDER_NAME'];
                            }
                            var tag = res[i]['TAG'] ? res[i]['TAG'].split(' ') : null;
                            write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, bought, preview, writer, folderID, foldername, pic, expose, more, tag, pin);
                            if ($('#topcon').length > 0) {
                                $('#topcon').after(write);
                            } else if ($('#upform').length > 0) {
                                $('#upform').after(write);
                            } else {
                                $('#prea').after(write);
                            }
                        }
                    }
                }
            }
            page = page + 1;
            loadOption['nowpage'] = page;
        }, error: function (xhr, textStatus, errorThrown) {
            if (textStatus == 'timeout') {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    //try again
                    $.ajax(this);
                    return;
                }
                return;
            }
            if (xhr.status == 500) {
                console.log('서버 오류! 관리자에게 문의하기')
            } else {
                console.log('몰랑몰랑')
            }
        }
    })

    //무한스크롤
    var loading = false;
    $(document).scroll(function () {
        var maxHeight = $(document).height();
        var currentScroll = $(window).scrollTop() + $(window).height();
        if (maxHeight <= currentScroll + 400) {
            if (loading == false) {
                loading = true;
                if ($('.card:last-child').length > 0) {
                    $('.card:last-child').after(spinner);
                } else {
                    $('#prea').after(spinner);
                }
                $.ajax({
                    url: "/php/data/getContent.php",
                    type: "get",
                    data: loadOption,
                    dataType: 'json',
                    tryCount: 0,
                    retryLimit: 3,
                    success: function (res) {
                        $('.load-item').remove();
                        for (var i = 0; i < res.length; i++) {
                            if (res[i]['USER_NAME'] != null) {
                                if (res[i]['FOR_SALE'] == "N") {
                                    var write = '';
                                    var ID = res[i]['ID'];
                                    var writer = res[i]['ID_WRITER'];
                                    var name = res[i]['USER_NAME'];
                                    var date = res[i]['WRITE_DATE'];
                                    var knock = res[i]['KNOCK'];
                                    var comment = res[i]['COMMENT'];
                                    var preview = res[i]['PREVIEW'];
                                    var pic = res[i]['PIC'];
                                    var targetID = res[i]['ID_TARGET'];
                                    var targetname = res[i]['TARGET_NAME'];
                                    var folderID = null;
                                    var foldername = null;
                                    var expose = res[i]['EXPOSE']
                                    var more = res[i]['MORE']
                                    if (res[i]['FOLDER'] != null) {
                                        folderID = res[i]['FOLDER'];
                                        foldername = res[i]['FOLDER_NAME'];
                                    }
                                    var tag = res[i]['TAG'] ? res[i]['TAG'].split(' ') : null;
                                    write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag,pin);
                                    if ($('.card:last-child').length > 0) {
                                        $('.card:last-child').after(write);
                                    } else {
                                        $('#prea').after(write);
                                    }
                                } else {
                                    var write = '';
                                    var ID = res[i]['ID'];
                                    var writer = res[i]['ID_WRITER'];
                                    var name = res[i]['USER_NAME'];
                                    var date = res[i]['WRITE_DATE'];
                                    var title = res[i]['TITLE'];
                                    var knock = res[i]['KNOCK'];
                                    var price = res[i]['PRICE'];
                                    var comment = res[i]['COMMENT'];
                                    var bought = res[i]['BOUGHT'];
                                    var preview = res[i]['PREVIEW'];
                                    var pic = res[i]['PIC'];
                                    var folderID = null;
                                    var foldername = null;
                                    var expose = res[i]['EXPOSE']
                                    var more = res[i]['MORE']
                                    if (res[i]['FOLDER'] != null) {
                                        folderID = res[i]['FOLDER'];
                                        foldername = res[i]['FOLDER_NAME'];
                                    }
                                    var tag = res[i]['TAG'] ? res[i]['TAG'].split(' ') : null;
                                    write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, bought, preview, writer, folderID, foldername, pic, expose, more, tag,pin);
                                    if ($('.card:last-child').length > 0) {
                                        $('.card:last-child').after(write);
                                    } else {
                                        $('#prea').after(write);
                                    }
                                }
                            }
                        }
                        loading = false;
                        page = page + 1;
                        loadOption['nowpage'] = page;
                    }, error: function (xhr, textStatus, errorThrown) {
                        if (textStatus == 'timeout') {
                            this.tryCount++;
                            if (this.tryCount <= this.retryLimit) {
                                //try again
                                $.ajax(this);
                                return;
                            }
                            return;
                        }
                        if (xhr.status == 500) {
                            console.log('서버 오류! 관리자에게 문의하기')
                        } else {
                            console.log('몰랑몰랑')
                        }
                    }
                })
            }
        }
    })
});