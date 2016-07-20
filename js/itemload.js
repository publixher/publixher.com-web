/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
function itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag, pin,knocked) {
    write = '<div class="item card" id="';
    write += ID;
    write += '"><div class="header">';
    write += '<div class="item-profile-wrap"><img src="' + pic + '" class="profilepic"></div>';
    write += '<div class="writer"><a href="/profile/' + writer + '">'
    write += name + '</a>&nbsp;'
    write += '<span class="content-date">'+date + '</span>&nbsp;'
    switch (expose) {
        case "0":
            write += '<span class="content-expose">나만보기</span>';
            break;
        case "1":
            write += '<span class="content-expose">친구에게</span>';
            break;
        case "2":
            write += '<span class="content-expose">전체공개</span>';
            break;
    }
    if (targetID) {
        write += ' <a href="/profile/' + targetID + '">' + targetname + '</a>에게 '
    }
    if (folderID) {
        write += '<span class="content-folder"><a href="/folder/' + folderID + '">' + foldername + '</a></span>&nbsp;';
    }

    write += '</div> <div class="conf">';
    if (pin.indexOf(ID) != -1) {   //핀에 아이디가 있을경우
        write += '<a class="pin-a pubico pico-Pin_002 pinned"></a>';
    } else {
        write += '<a class="pin-a pubico pico-Pin_002"></a>';
    }
    write += '<div class="btn-group"> <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span> </button> '

    if (mid == writer || level == 99) {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod"><span class="pubico pico-content-edit"></span>수정</a></li><li><a class="itemDel"><span class="pubico pico-content-remove"></span>삭제</a></li><li><a class="itemTop"><span class="pubico pico-kkuk-up"></span>최상단 컨텐츠로</a></li> </ul></div><br>'
    } else if(mid==targetID){
        write+='<ul class="dropdown-menu" role="menu"><li><a class="itemDel"><span class="pubico pico-content-remove"></span>삭제</a></li><li><a class="itemReport"><span class="pubico pico-alert"></span>신고</a></li></ul></div>'
    }else {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport"><span class="pubico pico-alert"></span>신고</a></li></ul></div><br>'
    }

    write += '</div></div> <div class="body">'
    write += preview + '</div>';
    if (tag) {
        write += '<div class="content-body-rep-wrap">';
        for (var i = 0; i < tag.length; i++) {
            write += ' <a href="/tag/' + tag[i] + '" class="body-tag">' + tag[i] + '</a>'
        }
        write+='</div>';
    }
    write += '<div class="tail"> <table><tr><td class="tknock"><span class="knock"><span class="pubico pico-knock"></span><a>노크</a><span class="badgea"> ';
    write += knock;
    write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
    write += comment + '</span></span></td>'
    write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
    if (more == '1') {
        write += '<td class="tprice"><span class="price bought"><a><span class="pubico pico-down-tri"></span> 더보기</a></span></td></tr></table></div> </div>';
    } else {
        write += '<td class="blank"></td> </tr></table></div> </div>';
    }
    if(knocked==1){
        write=$(write);
        write.find('.pico-knock').addClass('knocked');
        write.find('img').addClass('knocked-image');
    }
    return write;
}

function itemForSaleLoad(write, ID, name, date, title, knock, price, comment, bought, preview, writer, folderID, foldername, pic, expose, more, tag, pin,category,sub_category,knocked) {
    write = '<div class="item-for-sale card" id="';
    write += ID;
    write += '"><div class="header">';
    write += '<div class="item-profile-wrap"><img src="' + pic + '" class="profilepic"></div>';
    write += '<div class="writer"><a href="/profile/' + writer + '">'
    write += name + '</a>&nbsp;'
    write += '<span class="content-date">'+date + '</span>&nbsp;';
    switch (expose) {
        case "0":
            write += '<span class="content-expose">나만보기</span>';
            break;
        case "1":
            write += '<span class="content-expose">친구에게</span>';
            break;
        case "2":
            write += '<span class="content-expose">전체공개</span>';
            break;
    }
    if (folderID) {
        write += '<span class="content-folder"><a href="/folder/' + folderID + '">' + foldername + '</a></span>&nbsp;';
    }

    //카테고리 표시부분
    if(category!='SNS') {
        write+='<span class="content-category"><span class="item-category">'+category+'</span>';
        if(sub_category!=null){
            write+='<span class="pubico pico-kkuk"></span><span class="item-sub_category">'+sub_category+'</span>';
        }
        write+='</span>';
    }
    write += '</div> <div class="conf">';
    if (pin.indexOf(ID) != -1) {   //핀에 아이디가 있을경우
        write += '<a class="pin-a pubico pico-Pin_002 pinned"></a>';
    } else {
        write += '<a class="pin-a pubico pico-Pin_002"></a>';
    }
    write += '<div class="btn-group"> <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span> </button> '
    if (mid == writer || level == 99) {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemMod"><span class="pubico pico-content-edit"></span>수정</a></li><li><a class="itemDel"><span class="pubico pico-content-remove"></span>삭제</a></li><li><a class="itemTop"><span class="pubico pico-kkuk-up"></span>최상단 컨텐츠로</a></li> </ul></div><br>'
    } else {
        write += '<ul class="dropdown-menu" role="menu"><li><a class="itemReport"><span class="pubico pico-alert"></span>신고</a></li></ul></div><br>'
    }
    write += '</div><div class="title">';
    write += title;
    write += '</div></div> <div class="body">'
    write += preview + '</div>';
    if (tag) {
        write += '<div class="content-body-rep-wrap">';
        for (var i = 0; i < tag.length; i++) {
            write += ' <a href="/tag/' + tag[i] + '" class="body-tag">' + tag[i] + '</a>'
        }
        write+='</div>'
    }
    write += '<div class="tail"> <table><tr><td class="tknock"><span class="knock"><span class="pubico pico-knock"></span><a>노크</a><span class="badgea"> ';
    write += knock;
    write += '</span></span></td> <td class="tcomment"><span class="comment"><a>코멘트</a><span class="badgea"> '
    write += comment + '</span></span></td>'
    write += '<td class="tshare"><span class="share"><a>공유하기</a></span></td>'
    if (bought) {
        if (more == '1') {
            write += '<td class="tprice"><span class="price bought"><a><span class="pubico pico-down-tri"></span> 더보기</a></span></td></tr></table></div> </div>';
        } else {
            write += '<td class="blank"></td></tr></table></div> </div>';
        }
    }
    else {
        write += '<td class="tprice"><span class="price"><a class="value" data-price="'+price+'">구매</a></span></td></tr></table></div> </div>';
    }
    if(knocked==1){
        write=$(write);
        write.find('.pico-knock').addClass('knocked');
        write.find('img').addClass('knocked-image');
    }
    return write;
}

function getCards() {
    //스피너
    var spinner = $('<div>')
        .attr('data-loader', 'spinner')
        .addClass('load-item content-load')
    //페이지 로드 끝나면 아이템카드 불러오기
    if ($('#topcon').length > 0) {
        $('#topcon').after(spinner);
    } else if($('.notice').length>0){
        $('.notice').after(spinner);
    }else if ($('#upform').length > 0) {
        $('#upform').after(spinner);
    } else {
        $('#prea').after(spinner);
    }
    $.ajax({
        url: "/php/data/getContent.php",
        type: "get",
        data: loadOption,
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        tryCount: 0,
        retryLimit: 3,
        success: function (res) {
            
            if (res.length == 0) {
                spinner.detach();
                write = '<div class="card item">포스트가 없습니다. 친구를 만들거나 보내기와 출판해보세요!</div>'
                if ($('#topcon').length > 0) {
                    $('#topcon').after(write);
                } else if($('.notice').length>0){
                    $('.notice').after(write);
                } else if ($('#upform').length > 0) {
                    $('#upform').after(write);
                } else {
                    $('#prea').after(write);
                }
            } else {
                spinner.detach();
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
                            var targetID = res[i]['TARGET_ID'];
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
                            var knocked=res[i]['KNOCKED'];
                            write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag, pin,knocked);
                            if ($('#topcon').length > 0) {
                                $('#topcon').after(write);
                                $('#'+ID).hide().fadeIn()
                                    .find('.gif').gifplayer({wait:true});
                            } else if($('.notice').length>0){
                                $('.notice').after(write);
                                $('#'+ID).hide().fadeIn()
                                    .find('.gif').gifplayer({wait:true});
                            } else if ($('#upform').length > 0) {
                                $('#upform').after(write);
                                $('#'+ID).hide().fadeIn()
                                    .find('.gif').gifplayer({wait:true});
                            } else {
                                $('#prea').after(write);
                                $('#'+ID).hide().fadeIn()
                                    .find('.gif').gifplayer({wait:true});
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
                            var knocked=res[i]['KNOCKED'];
                            write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, bought, preview, writer, folderID, foldername, pic, expose, more, tag, pin, res[i]['CATEGORY'], res[i]['SUB_CATEGORY'],knocked);
                            if ($('#topcon').length > 0) {
                                $('#topcon').after(write);
                                $('#'+ID).hide().fadeIn()
                                    .find('.gif').gifplayer({wait:true});
                            } else if($('.notice').length>0){
                                $('.notice').after(write);
                                $('#'+ID).hide().fadeIn()
                                    .find('.gif').gifplayer({wait:true});
                            }  else if ($('#upform').length > 0) {
                                $('#upform').after(write);
                                $('#'+ID).hide().fadeIn()
                                    .find('.gif').gifplayer({wait:true});
                            } else {
                                $('#prea').after(write);
                                $('#'+ID).hide().fadeIn()
                                    .find('.gif').gifplayer({wait:true});
                            }
                        }
                    }
                }
            }
            loadOption['nowpage'] = loadOption['nowpage'] + 1;
        }, error: function (xhr, textStatus, errorThrown) {
            if (textStatus == 'timeout') {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    //try again
                    $.ajax(this);
                    return;
                }
                spinner.detach();
                return;
            }
            errorReport("itemLoad",loadOption,textStatus,errorThrown)
            //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
        }
    })
}

$(document).ready(function () {
    getCards();
    //무한스크롤
    var loading = false;
    $(document).scroll(function () {
        //스피너
        var spinner = $('<div>')
            .attr('data-loader', 'spinner')
            .addClass('load-item content-load')
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
                        spinner.detach();
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
                                    write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag, pin,res[i]['KNOCKED']);
                                    if ($('.card:last-child').length > 0) {
                                        $('.card:last-child').after(write);
                                        $('#'+ID).hide().fadeIn()
                                            .find('.gif').gifplayer({wait:true});
                                    } else {
                                        $('#prea').after(write);
                                        $('#'+ID).hide().fadeIn()
                                            .find('.gif').gifplayer({wait:true});
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
                                    write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, bought, preview, writer, folderID, foldername, pic, expose, more, tag, pin,res[i]['CATEGORY'],res[i]['SUB_CATEGORY'],res[i]['KNOCKED']);
                                    if ($('.card:last-child').length > 0) {
                                        $('.card:last-child').after(write);
                                        $('#'+ID).hide().fadeIn()
                                            .find('.gif').gifplayer({wait:true});
                                    } else {
                                        $('#prea').after(write);
                                        $('#'+ID).hide().fadeIn()
                                            .find('.gif').gifplayer({wait:true});
                                    }
                                }
                            }
                        }
                        loading = false;
                        loadOption['nowpage'] = loadOption['nowpage'] + 1;
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
                        errorReport("loadOption_scroll",loadOption,textStatus,errorThrown)
                    }
                })
            }
        }
    })
});