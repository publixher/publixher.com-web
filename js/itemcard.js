/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */


$(document).ready(function () {
    //아이템이 접히거나 다 봤을때 정보를 수집하기
    function readDone(item, user, time) {
        $.ajax({
            url: '/php/api/actionData.php',
            dataType: 'json',
            type: 'POST',
            data: {itemID: item, userID: user, time: time, action: "readDone"}
        })
    }

    //노크버튼 동작
    $(document).on("click", ".knock", function () {
        var knockbtn = $(this);
        var thisitemID = $(this).parents()[5].id;
        knockbtn.removeClass('knock');
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {ID: thisitemID, action: "knock", userID: mid, token: token},
            dataType: 'json',
            success: function (res) {
                knockbtn.addClass('knock');
                if (res['result'] != 'N') {
                    $('#' + thisitemID + ' .knock .badgea').text(res['KNOCK']);
                } else if (res['reason'] == 'already') {
                    alert('노크를 취소했습니다.');
                    $('#' + thisitemID + ' .knock .badgea').text(res['KNOCK']);
                }
            }, error: function (request, status, error) {
                alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                knockbtn.addClass('knock');
            }
        })
    });
    function getpoint(mid, callBack) {
        return $.ajax({
            url: '/php/api/profileInfo.php',
            dataType: 'json',
            type: 'GET',
            data: {userID: mid, action: 'point'},
            success: function (res) {
                callBack(res['result']['CASH_POINT']);
            }
        })
    }

    //코멘트 버튼 동작(처음 댓글 불러오기)
    $(document).on("click", ".comment", function () {
        var thisitemID = $(this).parents()[5].id;
        var tail = $('#' + thisitemID + ' .tail');
        tail.css('margin-bottom', '10px');
        if (!tail.hasClass('opend-comment')) {

            var spinner = $('<div>')
                .attr('data-loader', 'spinner')
                .addClass('load-item reply-load')

            if (tail.hasClass('opend-share')) {
                tail.removeClass('opend-share')
                $('#' + thisitemID + ' .tail .tab-share').remove();
                $('#' + thisitemID + ' .tail .tshare').css('background-color', 'white');
            }
            $(this).parent().css('background-color', '#f4f4f4');
            tail.append($('<div>').addClass('tab-comment'));
            var tab_comment = $('#' + thisitemID + ' .tail .tab-comment');
            var word = '<div role="tabpanel">'
            //정렬별 선택패널
            word += '<ul class="nav nav-tabs" role="tablist" id="repnav-"' + thisitemID + '>'
            word += '<li role="presentation" class="active"><a class="bestrep" href="#best-' + thisitemID + '" aria-controls="best-' + thisitemID + '" role="tab" data-toggle="tab">인기</a></li>'
            word += '<li role="presentation"><a class="timerep" href="#time-' + thisitemID + '" aria-controls="time-' + thisitemID + '" role="tab" data-toggle="tab">시간순</a></li>'
            word += '<li role="presentation"><a class="frierep" href="#frie-' + thisitemID + '" aria-controls="frie-' + thisitemID + '" role="tab" data-toggle="tab">친구</a></li>'
            word += '</ul>'
            //정렬별 댓글
            word += '<div class="tab-content">'
            word += '      <div role="tabpanel" class="tab-pane active" id="best-' + thisitemID + '"></div>'
            word += '   <div role="tabpanel" class="tab-pane" id="time-' + thisitemID + '"></div>'
            word += '    <div role="tabpanel" class="tab-pane" id="frie-' + thisitemID + '"></div>'
            word += '    </div></div>'
            tab_comment.append(word);
            tab_comment.append('<div contenteditable="true" type="text" class="commentReg form-control"><div></div></div>');
            //댓글 태그기능
            tab_comment
                .append($('<div>')  //드롭다운 div
                    .addClass('dropdown').append(
                        $('<button>').addClass('btn btn-default dropdown-toggle reply-tag-btn').attr({
                            'type': 'button',
                            'data-toggle': 'dropdown',
                            'aria-expanded': 'true',
                            'id': thisitemID + '-rep-tag'
                        }).append($('<span>').addClass('pubico pico-person-plus'))
                        , $('<ul>') //태그 리스트 안에 input이 들어간다
                            .on('click', function (e) {
                                e.stopPropagation();
                            }).addClass('dropdown-menu rep_tag-ul')
                            .attr({
                                'role': 'menu',
                                'aria-labelledby': thisitemID + '-rep-tag'
                            }).append(
                                $('<li>').addClass('rep-tag-input-li').append(
                                    $('<input>').addClass('form-control rep-tag-input').attr({'type': 'text'})
                                        .on('input', function (e) {    //태그칸에 글 입력하면 이름 불러옴
                                            if ($(this).val().length > 0) {
                                                var name = $(this).val();
                                                var spinner = $('<div>')
                                                    .attr('data-loader', 'spinner')
                                                    .addClass('load-item tag-load');
                                                var ul = $(this).parents('ul')   //ul에 스피너 추가
                                                if (!ul.find('.tag-load').length) {
                                                    ul.append(
                                                        $('<li>')
                                                            .append(spinner)
                                                    );
                                                }
                                                $.ajax({
                                                    url: '/php/data/nameFind.php',
                                                    type: 'GET',
                                                    dataType: 'json',
                                                    data: {target: 'friend', mid: mid, name: name},
                                                    success: function (res) {
                                                        ul.children(':not(.rep-tag-input-li)').remove();
                                                        for (var i = 0; i < res.length; i++) {
                                                            ul.append(
                                                                $('<li>')
                                                                    .addClass('rep-tag-friend')
                                                                    .append(
                                                                        $('<div>')
                                                                            .append(
                                                                                $('<img>')
                                                                                    .attr('src', res[i]['PIC'])
                                                                                    .addClass('rep-tag-friend-pic')
                                                                            )
                                                                            .addClass('rep-tag-friend-pic-wrap')
                                                                        , $('<span>')
                                                                            .addClass('rep-tag-friend-name')
                                                                            .attr('data-userID', res[i]['ID'])
                                                                            .text(res[i]['USER_NAME'])
                                                                    )
                                                                    .on('click', function () {  //선택되면 댓글창으로 넘기고 아래 친구 리스트 다 지운다음 드롭다운 토글하기.
                                                                        var tagId = $(this).children('.rep-tag-friend-name').attr('data-userID');
                                                                        tab_comment.children('.commentReg').append(
                                                                            $('<span>')
                                                                                .addClass('rep-tag')
                                                                                .text(
                                                                                    $(this).children('.rep-tag-friend-name').text()
                                                                                )
                                                                                .attr({
                                                                                    'onclick': 'location.href="/profile/' + tagId + '"',
                                                                                    'contenteditable': 'false',
                                                                                    'data-userid': tagId
                                                                                })
                                                                                .css('cursor', 'pointer')
                                                                        );
                                                                        $(this).parent().children(':not(.rep-tag-input-li)').remove();  //리스트 전부 삭제
                                                                        ul.find('input').val('');  //입력 내용도 삭제
                                                                    })
                                                            )
                                                        }
                                                    }
                                                })
                                            }
                                        })
                                )
                            )
                    )
                );
            tab_comment
                .append(
                    $('<div>')
                        .addClass('dropdown')
                        .append(
                            $('<button>')
                                .addClass('btn btn-default dropdown-toggle donate-btn')
                                .attr({
                                    'type': 'button',
                                    'data-toggle': 'dropdown',
                                    'aria-expanded': 'true',
                                    'id': thisitemID + '-donate-btn'
                                })
                                .append(
                                    $('<span>')
                                        .addClass('pubico pico-24')
                                )
                                .on('click', function () { //기부버튼 누를때 서버에서 남은 포인트 가져오기
                                    var input = $(this).siblings('ul').find('input');
                                    getpoint(mid, function (point) {
                                        input.attr('placeholder', point);
                                    });
                                })
                            , $('<ul>')  //기부 금액 넣기
                                .on('click', function (e) {
                                    e.stopPropagation();
                                })
                                .addClass('dropdown-menu donate-input-ul')
                                .attr({
                                    'role': 'menu',
                                    'aria-labelledby': thisitemID + '-donate-btn'
                                })
                                .append(
                                    $('<li>')
                                        .addClass('donate-input-li')
                                        .append(
                                            $('<input>')
                                                .addClass('form-control donate-form')
                                                .attr('type', 'text')
                                                .on('keyup', function (e) {
                                                    if (e.keyCode == 13 && $(this).val().length > 0) {
                                                        var thisform = $(this);
                                                        var point = $(this).val();
                                                        var rex = /^\d+$/;
                                                        //숫자인지 체크
                                                        if (rex.test(point) && point != 0) {
                                                            tab_comment.children('.commentReg')
                                                                .append(
                                                                    $('<span>')
                                                                        .addClass('donate-span')
                                                                        .text(point)
                                                                        .attr('contenteditable', 'false')
                                                                );

                                                            thisform.val('')
                                                        } else {
                                                            alert('1이상 숫자만 입력해주세요.');
                                                            return 1;
                                                        }
                                                    }

                                                })
                                        )
                                )
                        )
                )

            $('#best-' + thisitemID).append(spinner);
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "GET",
                data: {ID: thisitemID, action: "comment", sort: "first", userID: mid, token: token},
                dataType: 'json',
                success: function (res) {
                    spinner.detach();
                    function registRep(res, where) {
                        var list = $('#' + where);
                        list.html('');
                        var repnum = Object.keys(res).length - 3;
                        if (res['more'] == 1) {
                            list.append('<div style="height: 20px;text-align: center" class="cursor"><span class="caret repbtn" style="cursor: pointer;"></span></div>')
                        }
                        for (var i = repnum; i > -1; i--) {
                            var write = '';
                            var ID = res[i]['ID'];
                            var name = res[i]['USER_NAME'];
                            var date = res[i]['REPLY_DATE'];
                            var reply = res[i]['REP_BODY'];
                            var knock = res[i]['KNOCK'];

                            write += '<div class=commentReply id="' + where + '-rep-' + ID + '">';
                            write += '<table style="margin-top: 5px;margin-bottom: 5px;"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'] + '" class="profilepic"></div></td>';
                            write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;"><span class="reply-body">' + reply + '</span><span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">코멘트</a> <span class="repreplybad">' + res[i]['SUB_REPLY'] + '</span>';
                            if (mid == res[i]['ID_USER'] || level == 99 || mid==res[i]['CONTENT_WRITER']) {
                                write += ' <a class="repdel">X</a>'
                            }
                            write += '</span></span></td></tr></table></div>';
                            if (res[i]['DEL'] == 1) {
                                write = $(write);
                                write.find('.reply-body').addClass('reply-del');
                            }
                            list.append(write);
                            var ind = parseInt(list.attr('index')) + 1;
                            list.attr('index', ind);
                        }
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
                        var word = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;" class="nobest"></div>';
                        $('#best-' + thisitemID).append(word);
                        registRep(res, 'time-' + thisitemID);
                        $('a[href=#time-' + thisitemID + ']').trigger('click');
                    } else if (res['result'] == 'NO') {
                        var word = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;" class="nobest"></div>';
                        var word2 = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;" class="nobest"></div>';
                        $('#best-' + thisitemID).append(word);
                        $('#time-' + thisitemID).append(word2);
                        $('a[href=#time-' + thisitemID + ']').trigger('click');

                    }
                }, error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
            tail.addClass('opend-comment');
        } else {
            //댓글창 열려있으면 닫기
            var tail = $(this).parents('.tail')
                .removeClass('opend-comment')
                .removeAttr('style');
            tail.find('.tcomment').removeAttr('style');
            tail.find('.tab-comment').fadeOut(function () {
                tail.find('.tab-comment').remove();
            })
        }
    });
    //댓글 네비게이션에 각 탭 누를때의 동작
    $(document).on('click', '.bestrep,.timerep,.frierep', function () {
        var target = $(this).attr('aria-controls');
        var index = $('#' + target).attr('index');
        var tarsplit = target.split('-');
        var sort = tarsplit[0];
        var num = tarsplit[1];
        var spinner = $('<div>')
            .attr('data-loader', 'spinner')
            .addClass('load-item reply-load')
        $('#' + target).append(spinner);
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "GET",
            data: {ID: num, action: "comment", userID: mid, sort: sort, token: token},
            dataType: 'json',
            success: function (res) {
                spinner.detach();
                function registRep(res, where) {
                    var list = $('#' + where);
                    var repnum = Object.keys(res).length - 3;
                    if (res['more'] == 1) {
                        list.append('<div style="height: 20px;text-align: center" class="cursor"><span class="caret repbtn" style="cursor: pointer;"></span></div>')
                    }
                    for (var i = repnum; i > -1; i--) {
                        var write = '';
                        var ID = res[i]['ID'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REP_BODY'];
                        var knock = res[i]['KNOCK'];
                        write += '<div class=commentReply id="' + where + '-rep-' + ID + '">';
                        write += '<table style="margin-top: 5px;margin-bottom: 5px;"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'] + '" class="profilepic"></div></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;"<span class="reply-body">' + reply + '</span><span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">코멘트</a> <span class="repreplybad">' + res[i]['SUB_REPLY'] + '</span>';
                        if (mid == res[i]['ID_USER'] || level == 99|| mid==res[i]['CONTENT_WRITER']) {
                            write += ' <a class="repdel">X</a>'
                        }
                        write += '</span></span></td></tr></table></div>';
                        if (res[i]['DEL'] == 1) {
                            write = $(write);
                            write.find('.reply-body').addClass('reply-del');
                        }
                        list.append(write);
                        var ind = parseInt(list.attr('index')) + 1;
                        list.attr('index', ind);
                    }

                }

                if (parseInt(index) == 0 && res['result'] != 'NO') {
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
        var tarsplit = target.split('-');
        var sort = tarsplit[0];
        var num = tarsplit[1];
        var panel = $('#' + sort + '-' + num);
        var index = panel.attr('index');
        var spinner = $('<div>')
            .attr('data-loader', 'spinner')
            .addClass('load-item reply-load');
        var btn = $('#' + sort + '-' + num + ' .cursor').append(spinner);
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "GET",
            data: {ID: num, action: "more_comment", userID: mid, index: index, sort: sort, token: token},
            dataType: 'json',
            success: function (res) {
                spinner.detach();
                if (res['result'] == 'NO') {
                    $('#' + sort + '-' + num + ' .cursor').remove();
                    return;
                }
                function registRep(res, where) {
                    var list = $('#' + num + ' .tail ' + '#' + where);

                    var repnum = Object.keys(res).length - 2;
                    for (var i = 0; i < repnum; i++) {
                        var write = '';
                        var ID = res[i]['ID'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REP_BODY'];
                        var knock = res[i]['KNOCK'];
                        write += '<div class=commentReply id="' + where + '-rep-' + ID + '">';
                        write += '<table style="margin-top: 5px;margin-bottom:5px"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'].replace('profile', 'crop34') + '" class="profilepic"></div></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;"><span class="reply-body">' + reply + '</span><span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">코멘트</a> <span class="repreplybad">' + res[i]['SUB_REPLY'] + '</span>';
                        if (mid == res[i]['ID_USER'] || level == 99|| mid==res[i]['CONTENT_WRITER']) {
                            write += ' <a class="repdel">X</a>'
                        }
                        write += '</span></span></td></tr></table></div>';
                        if (res[i]['DEL'] == 1) {
                            write = $(write);
                            write.find('.reply-body').addClass('reply-del');
                        }
                        btn.after(write);
                        var ind = parseInt(list.attr('index')) + 1;
                        list.attr('index', ind);
                    }
                    if (res['more'] == 0) {
                        btn.remove();
                    }
                }

                registRep(res, sort + '-' + num);
            }, error: function (request, status, error) {
                alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
            }
        })
    })
    //댓글 달기
    $(document).on("keydown", ".commentReg", function (e) {
        if (e.keyCode == 13 && $(this).text().length > 0 && !e.shiftKey) {
            var thisform = $(this);
            var thisitemID = $(this).parents()[2].id;
            var form = $('#' + thisitemID + ' .tail .commentReg');
            var reply = form.html();
            var taglist = []; //댓글에서 태그의 아이디 추출
            form.children('.rep-tag').each(function () {
                taglist.push($(this).attr('data-userid'))
            })
            var donatelist = [];  //후원 리스트 추출
            form.children('.donate-span').each(function (list) {
                donatelist.push($(this).text())
            })
            thisform.removeClass('commentReg');
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {
                    ID: thisitemID,
                    action: "commentreg",
                    userID: mid,
                    comment: reply,
                    token: token,
                    taglist: taglist,
                    donatelist: donatelist
                },
                dataType: 'json',
                success: function (res) {
                    if (res['status'] == -9) {
                        alert('해당 유저는 ' + res['result']['BAN'] + ' 까지 댓글 작성이 제한된 유저입니다.');
                        return false;
                    }
                    thisform.addClass('commentReg').text('').css('height', '25px');
                    $('#' + thisitemID + ' .comment .badgea').text(res['COMMENT']);
                    //시간순 댓글의 내용을 지우고 인덱스를 0으로 만들고(이러면 새로 로딩됨) 버튼을 누른 상태로 만든다
                    $('#' + thisitemID + ' .tail .tab-comment').remove();
                    $('#' + thisitemID + ' .tail').removeClass('opend-comment');
                    $('#' + thisitemID + ' .comment').trigger('click');
                }, error: function (request, status, error) {
                    thisform.addClass('commentReg');
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
        }
    });
    //댓글삭제 동작
    $(document).on('click', '.repdel,.sub-repdel', function () {
        var type = $(this).hasClass('repdel') ? 0 : 1;
        var thisrep = type == 0 ? $(this).parents()[6].id : $(this).parents()[5].id;
        var thisrepID = type == 0 ? (thisrep.split('-'))[3] : (thisrep.split('-'))[5];
        if (confirm('정말 삭제하시겠습니까?')) {
            var btn = $(this);
            type == 0 ? $(this).removeClass('repdel') : $(this).removeClass('sub-repdel');
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {ID: thisrepID, action: "rep_del", token: token, userID: mid, type: type},
                dataType: 'json',
                success: function (res) {
                    type == 0 ? btn.addClass('repdel') : btn.addClass('sub-repdel');
                    if (res['result'] == 'Y') {
                        $('#' + thisrep + ' .reply-body').fadeOut(function () {
                            $('#' + thisrep + ' .reply-body').text('삭제된 코멘트 입니다.').addClass('reply-del').fadeIn();
                        })

                    }
                    else alert('동작중 문제가 발생했습니다. 다시 시도해 주세요.');
                }
            })
        }
    });
    //대댓글버튼 동작
    $(document).on("click", ".repreply", function () {
        var thisitemID = $(this).parents()[12].id;
        var thispanelrep = ($(this).parents()[6].id);
        var thisrepID = (thispanelrep.split('-'))[3];
        var rep = $('#' + thispanelrep);
        var subrep_list = $('#' + thispanelrep + '-sub');
        if (!subrep_list.hasClass('opened')) {
            rep.after('<div class="subrep opened" id="' + thispanelrep + '-sub"></div>');
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "GET",
                data: {ID: thisitemID, repID: thisrepID, action: "sub_comment", userID: mid, token: token},
                dataType: 'json',
                success: function (res) {
                    var subrep_list = $('#' + thispanelrep + '-sub');
                    if (res['result'] != 'NO') {
                        function registRep(res) {
                            var repnum = Object.keys(res).length - 3;
                            if (res['more'] == 1) {
                                subrep_list.append('<div style="height: 20px;text-align: center" class="cursor"><span class="caret repbtn_sub" style="cursor: pointer;"></span></div>')
                            }
                            for (var i = repnum; i > -1; i--) {
                                var write = '';
                                var ID = res[i]['ID'];
                                var name = res[i]['USER_NAME'];
                                var date = res[i]['REPLY_DATE'];
                                var reply = res[i]['REP_BODY'];
                                write += '<div class=commentReply id="' + thispanelrep + '-subrep-' + ID + '">';
                                write += '<table style="margin-top: 5px;margin-bottom:5px;"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'].replace('profile', 'crop34') + '" class="profilepic"></div></td>';
                                write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;"><span class="reply-body">' + reply + '</span></span>'
                                if (mid == res[i]['ID_USER'] || level == 99 || mid==res[i]['CONTENT_WRITER'] || mid==res[i]['REPLY_WRITER']) {
                                    write += ' <span class="repaction"><a class="sub-repdel">삭제</a></span>'
                                }
                                write += '</td></tr></table></div>';
                                subrep_list.append(write);
                                var ind = parseInt(subrep_list.attr('index')) + 1;
                                subrep_list.attr('index', ind);
                            }

                        }

                        //인덱스 붙이기
                        if (!subrep_list.attr('index')) {
                            subrep_list.attr('index', '0');
                        }
                        registRep(res);
                    }
                    subrep_list.append('<div contenteditable="true" id="' + thispanelrep + '-form" class="commentReg_sub form-control"><div></div></div>').hide().fadeIn();
                    //대댓글 태그기능
                    subrep_list
                        .append(
                            $('<div>')
                                .addClass('dropdown')
                                .append(
                                    $('<button>')
                                        .addClass('btn btn-default dropdown-toggle reply-tag-btn')
                                        .attr({
                                            'type': 'button',
                                            'data-toggle': 'dropdown',
                                            'aria-expanded': 'true',
                                            'id': thispanelrep + '-rep-tag'
                                        })
                                        .append(
                                            $('<span>')
                                                .addClass('pubico pico-person-plus')
                                        )
                                    , $('<ul>')
                                        .on('click', function (e) {
                                            e.stopPropagation();
                                        })
                                        .addClass('dropdown-menu rep_tag-ul')
                                        .attr({
                                            'role': 'menu',
                                            'aria-labelledby': thisitemID + '-rep-tag'
                                        })
                                        .append(
                                            $('<li>')
                                                .addClass('rep-tag-input-li')
                                                .append(
                                                    $('<input>')
                                                        .addClass('form-control rep-tag-input')
                                                        .attr({
                                                            'type': 'text'
                                                        })
                                                        .on('input', function (e) {    //태그칸에 글 입력하면 이름 불러옴
                                                            if ($(this).val().length > 0) {
                                                                var name = $(this).val();
                                                                var spinner = $('<div>')
                                                                    .attr('data-loader', 'spinner')
                                                                    .addClass('load-item tag-load');
                                                                var ul = $(this).parents('ul')   //ul에 스피너 추가
                                                                if (!ul.find('.tag-load').length) {
                                                                    ul.append(
                                                                        $('<li>')
                                                                            .append(spinner)
                                                                    );
                                                                }
                                                                $.ajax({
                                                                    url: '/php/data/nameFind.php',
                                                                    type: 'GET',
                                                                    dataType: 'json',
                                                                    data: {target: 'friend', mid: mid, name: name},
                                                                    success: function (res) {
                                                                        ul.children(':not(.rep-tag-input-li)').remove();
                                                                        for (var i = 0; i < res.length; i++) {
                                                                            ul.append(
                                                                                $('<li>')
                                                                                    .addClass('rep-tag-friend')
                                                                                    .append(
                                                                                        $('<div>')
                                                                                            .append(
                                                                                                $('<img>')
                                                                                                    .attr('src', res[i]['PIC'])
                                                                                                    .addClass('rep-tag-friend-pic')
                                                                                            )
                                                                                            .addClass('rep-tag-friend-pic-wrap')
                                                                                        , $('<span>')
                                                                                            .addClass('rep-tag-friend-name')
                                                                                            .attr('data-userID', res[i]['ID'])
                                                                                            .text(res[i]['USER_NAME'])
                                                                                    )
                                                                                    .on('click', function () {  //찾아서 클릭하면 친구 li 다 지우고 댓글창에 넘긴다음 드롭다운 토글
                                                                                        var tagId = $(this).children('.rep-tag-friend-name').attr('data-userID');
                                                                                        subrep_list.children('.commentReg_sub').append(
                                                                                            $('<span>')
                                                                                                .addClass('rep-tag')
                                                                                                .text(
                                                                                                    $(this).children('.rep-tag-friend-name').text()
                                                                                                )
                                                                                                .attr({
                                                                                                    'onclick': 'location.href="/profile/' + tagId + '"',
                                                                                                    'contenteditable': 'false',
                                                                                                    'data-userid': tagId
                                                                                                })
                                                                                                .css('cursor', 'pointer')
                                                                                        );
                                                                                        $(this).parent().children(':not(.rep-tag-input-li)').remove();
                                                                                        ul.find('input').val('');
                                                                                    })
                                                                            )
                                                                        }
                                                                    }
                                                                })
                                                            }
                                                        })
                                                )
                                        )
                                )
                                .fadeIn()
                        )

                }
            });
        } else {
            subrep_list.remove();
        }
    });
//대댓글 등록 동작
    $(document).on("keydown", ".commentReg_sub", function (e) {
        if (e.keyCode == 13 && $(this).text().length > 0 && !e.shiftKey) {
            var thisform = $(this);
            var form = $(this)[0].id;
            var idset = form.split('-');
            var sub = form.replace('form', 'sub');
            var thisitemID = idset[1];
            var thisrepID = idset[3];
            var reply = $('#' + form).html();
            var taglist = []; //댓글에서 태그의 아이디 추출
            $('#' + form).children('.rep-tag').each(function () {
                taglist.push($(this).attr('data-userid'))
            })
            thisform.removeClass('commentReg_sub');
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {
                    ID: thisitemID,
                    action: "commentreg_sub",
                    repID: thisrepID,
                    userID: mid,
                    comment: reply,
                    token: token,
                    taglist: taglist
                },
                dataType: 'json',
                success: function (res) {
                    if (res['status'] == -9) {
                        alert('해당 유저는 ' + res['result']['BAN'] + ' 까지 댓글 작성이 제한된 유저입니다.');
                        return false;
                    }
                    thisform.addClass('commentReg_sub').text('').css('height', '25px');
                    var subrep_list = $('#' + form.replace('form', 'sub'));
                    var thisreply = form.replace('-form', '');
                    //시간순 댓글의 내용을 지우고 인덱스를 0으로 만들고(이러면 새로 로딩됨) 버튼을 누른 상태로 만든다
                    subrep_list.html('');
                    subrep_list.attr('index', '0');
                    subrep_list.removeClass('opened');
                    $('#' + sub).remove();
                    $('#' + thisreply + ' .repreplybad').text(res['SUB_REPLY']);
                    $('#' + thisreply + ' .repreply').trigger('click');
                }
            })
        }
    });

    //대댓글에서 화살표 동작
    $(document).on('click', '.repbtn_sub', function (e) {
        var caret = $(this).parents()[1].id;
        var idset = caret.split('-');
        var repID = idset[3];
        var index = $('#' + caret).attr('index');
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "GET",
            data: {repID: repID, action: "more_sub_comment", userID: mid, index: index, token: token},
            dataType: 'json',
            success: function (res) {
                if (res['result'] == 'NO') {
                    $('#' + caret + ' .cursor').remove();
                    return;
                }
                function registRep(res, where) {
                    var list = $('#' + caret);
                    var btn = $('#' + caret + ' .cursor');
                    var repnum = Object.keys(res).length - 3;
                    for (var i = 0; i < repnum; i++) {
                        var write = '';
                        var ID = res[i]['ID'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REP_BODY'];
                        write += '<div class=commentReply id="' + where + '-rep-' + ID + '">';
                        write += '<table style="margin-top: 5px;margin-bottom:5px;"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'].replace('profile', 'crop34') + '" class="profilepic"></div></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;">' + reply + '</span>'
                        if (mid == res[i]['ID_USER'] || level == 99|| mid==res[i]['CONTENT_WRITER'] || mid==res[i]['REPLY_WRITER']) {
                            write += ' <span class="repaction"><a class="sub-repdel">삭제</a></span>'
                        }
                        write += '</td></tr></table></div>';
                        btn.after(write);
                        $('#' + where + '-rep-' + ID).hide().fadeIn();
                        var ind = parseInt(list.attr('index')) + 1;
                        list.attr('index', ind);
                    }
                    if (res['more'] == 0) {
                        btn.remove();
                    }
                }

                registRep(res, caret);
            }
        })
    })

    //공유하기 버튼 동작
    $(document).on("click", ".share", function () {
        var thisitemID = $(this).parents()[5].id;
        var tail = $('#' + thisitemID + ' .tail').css('margin-bottom', '10px');
        if (!tail.hasClass('opend-share')) {
            var linkstr = 'https://alpha.publixher.com/content/' + thisitemID;
            if (tail.hasClass('opend-comment')) {
                tail.removeClass('opend-comment');
                $('#' + thisitemID + ' .tail .tab-comment').remove();
                $('#' + thisitemID + ' .tail .tcomment').css('background-color', 'white');
            }
            $(this).parent().css('background-color', '#f4f4f4');
            tail.append($('<div>').addClass('tab-share').hide().fadeIn());
            var tab_share = $('#' + thisitemID + ' .tail .tab-share');
            var text = '이 게시물의 url<br><div class="form-control linkurl">' + linkstr + '</div>';
            tab_share.append(text)
            tail.addClass('opend-share')
        } else {
            $('#' + thisitemID).find('.tab-share').remove();
            tail.removeClass('opend-share');
            tail.find('.tshare').css('background-color', 'white');
        }

    });
    //구매버튼(가격표시)동작
    var itemPool = window['item_pool'] = new Array();
    var previewarr = [];
    $(document).on("click", ".price", function () {
        var thisitemID = $(this).parents()[5].id;
        var priceSpan = $('#' + thisitemID + ' .tail .price')
        //안산상태에서 한번 눌려졌을때 한번 더누르면 구매됨
        if (priceSpan.hasClass('buyConfirm')) {
            var pricebtn = $(this);
            pricebtn.removeClass('price');
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {
                    ID: thisitemID,
                    action: "buy",
                    userID: mid,
                    token: token
                },
                dataType: 'json',
                success: function (res) {
                    pricebtn.addClass('price')
                    if (res['buy'] == 'f') {
                        alert('구매 실패 : ' + res['reason']);
                    } else {
                        priceSpan.html('<span class="pubico pico-down-tri"></span>');
                        priceSpan.removeClass('buyConfirm').addClass('bought');
                    }
                }
                , error: function (request, status, error) {
                    pricebtn.addClass('price')
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
        } else if (priceSpan.hasClass('bought')) {
            //산상태에서는 더보기 버튼의 역할을 함
            var body = $('#' + thisitemID + ' .body');
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "GET",
                data: {ID: thisitemID, action: "more", userID: mid, token: token},
                dataType: 'json',
                success: function (res) {
                    previewarr['' + thisitemID] = $('#' + thisitemID + ' .body').html();
                    body.fadeOut(function () {
                        body.html('<div id="links' + thisitemID + '">' + res['BODY'] + '</div>').fadeIn(function () {
                            //확장된 순간부터 해당 게시물 읽은 시간을 기록한다
                            itemPool.push({
                                'time': new Date(),
                                'scroll_end': $('#' + thisitemID).position().top + $('#' + thisitemID).height(),
                                'ID': thisitemID
                            });
                        }).find('.gif').gifplayer({
                            playOn: 'hover',
                            wait: true
                        });  //gif 재생

                    });
                    priceSpan.fadeOut(function () {
                        priceSpan.html('<a><span class="pubico pico-up-tri"></span></a>').fadeIn();
                    });

                    priceSpan.removeClass('bought').addClass('expanded');
                }
                , error: function (request) {
                    alert(request.responseText);
                }
            })
        } else if (priceSpan.hasClass('expanded')) {
            //확장된상태에서는 접기역할을 함
            var body = $('#' + thisitemID + ' .body');
            body.fadeOut(function () {
                body.html(previewarr['' + thisitemID]).fadeIn();
            });
            priceSpan.fadeOut(function () {
                priceSpan.html('<a><span class="pubico pico-down-tri"></span></a>').fadeIn();
            });
            document.location.href = '#' + thisitemID;
            priceSpan.removeClass('expanded').addClass('bought');
            //접는 순간 해당 게시물 읽은 시간을 구한다
            var now = new Date();
            var itemIndex = findIndex(itemPool, 'ID', thisitemID);
            var gap = (now.getTime() - itemPool[itemIndex]['time'].getTime()) / 1000;
            readDone(thisitemID, mid, gap);
            itemPool.splice(itemIndex, 1);
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
            data: {ID: thisitemID, action: "del", token: token},
            dataType: 'json',
            success: function (res) {
                if (res['result'] == 'Y') {
                    $('#' + thisitemID).fadeOut("normal", function () {
                        $(this).remove();
                    });
                } else {
                    alert('게시물이 삭제되는 도중 오류가 생겼습니다. 관리자에게 문의해 주세요.');
                }
            }, error: function (request) {
                alert(request.responseText);
            }
        })
    });
    //수정버튼 동작
    var expose_mod;
    var folderid_mod;
    var itemID_mod;
    $(document).on('click', '.itemMod', function () {
        var thisitemID = $(this).parents()[5].id;
        var type = '';
        itemID_mod = thisitemID;
        expose_mod = null;
        folderid_mod = null;
        $('#directorySettingSub-mod').text('비분류');
        if ($('#' + thisitemID).hasClass('item')) {
            if ($('#publixh-mod').hasClass('active')) $('#publixh-mod').removeClass('active');
            $('#send-mod').addClass('active');
            type = 'item';
            $('#send-tag-mod').tagEditor('destroy').val('').tagEditor();
        } else {
            if ($('#send-mod').hasClass('active')) $('#send-mod').removeClass('active');
            $('#publixh-mod').addClass('active');
            type = 'forsale';
            $('#publi-tag-mod').tagEditor('destroy').val('').tagEditor();
        }
        $.ajax({
            url: '/php/data/modItem.php',
            type: 'GET',
            dataType: 'json',
            data: {itemID: thisitemID, action: "get_item"},
            success: function (res) {
                expose_mod = res['EXPOSE'];
                var expset = $('#exposeSetting-mod');
                switch (expose_mod) {
                    case 0:
                        expset.text('나만보기');
                        break;
                    case 1:
                        expset.text('친구에게 공개');
                        break;
                    case 2:
                        expset.text('전체공개');
                }
                if (res['FOLDER']) {
                    $('#directorySettingSub-mod').text(res['DIR']);
                    folderid_mod = res['FOLDER'];
                }
                if (type == 'item') {
                    $('#sendBody-mod').html(res['BODY']);

                } else if (type == 'forsale') {
                    $('#saleTitle-mod').val(res['TITLE']);
                    $('#publiBody-mod').html(res['BODY']);

                    if (res['CATEGORY']) {
                        $('#category-mod').text(res['CATEGORY']);
                        category_mod = res['CATEGORY'];
                        $('#sub-category-mod').text(res['SUB_CATEGORY']);
                        sub_category_mod = res['SUB_CATEGORY'];
                    } else {
                        $('#category-mod').text('분류');
                        $('#sub-category-mod').text('하위분류');
                    }
                    $('#contentCost-mod').val(res['PRICE']);
                }
                var tags = res['TAG'].split(' ');
                for (var i = 0; i < tags.length; i++) {
                    if (type == 'item') {
                        $('#send-tag-mod').tagEditor('addTag', tags[i]);
                    } else {
                        $('#publi-tag-mod').tagEditor('addTag', tags[i]);
                    }
                }
                $('#itemModModal').modal({show: true})
            }
        })
    })
    //모달 실행되면 내용물 리사이징
    $('#itemModModal').on('shown.bs.modal', function (e) {
        $('#publiBody-mod').trigger('keyup');
        $('#sendBody-mod').trigger('keyup');
    })
    //수정모드에서 파일 업로드시 동작
    $('#fileuploads-mod,#fileuploadp-mod').fileupload({
        dataType: 'json',
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
            var gif = data.files[0].type == 'image/gif' ? true : false;
            var img = '<div><img src="/img/' + data.result['files']['file_crop'] + '" class="BodyPic"></div>';
            if (gif) img.addClass('gif');
            if (this == $('#fileuploads-mod')[0]) {
                var sendBody = $('#sendBody-mod');
                sendBody.focus();
                pasteHtmlAtCaret(img + '<br>');
            } else if (this == $('#fileuploadp-mod')[0]) {
                var publiBody = $('#publiBody-mod');
                publiBody.focus();
                pasteHtmlAtCaret(img + '<br>');
            }
        }, fail: function (e, data) {
            alert('파일 업로드중 문제가 발생했습니다. 다시 시도해주세요.<img src="/img/sorry.jpeg">')
        }
    });
    //수정시 글쓰기 버튼 클릭할때의 동작
    $('#sendButton-mod').on('click', function () {
        var $btn = $(this).button('loading');
        var ID_target = null;
        if (targetID) {
            if (mid == targetID) {
                ID_target = null;
            } else {
                ID_target = targetID;
            }
        }
        if ($('#sendBody-mod').html().length > 0) {
            var btn = $(this);
            $(this).attr('disabled', 'disabled')
            $.ajax({
                url: "/php/data/modItem.php",
                type: "POST",
                data: {
                    ID: itemID_mod,
                    body: $('#sendBody-mod').html(),
                    body_text: $('#sendBody-mod').text(),
                    ID_writer: mid,
                    folder: folderid_mod,
                    token: token,
                    tag: JSON.stringify($('#send-tag-mod').tagEditor('getTags')[0].tags),
                    expose: expose_mod,
                    action: "mod_item"
                },
                dataType: 'json',
                success: function (res) {
                    var write = '';
                    var ID = res['ID'];
                    var writer = res['ID_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var knock = res['KNOCK'];
                    var comment = res['COMMENT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var targetID = res['ID_TARGET'];
                    var targetname = res['TARGET_NAME'];
                    var folderID = null;
                    var foldername = null;
                    var more = res['MORE'];
                    var expose = res['EXPOSE'];
                    var tag = res['TAG'] ? res['TAG'].split(' ') : null;
                    if (res['FOLDER'] != null) {
                        folderID = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag, pin,res['KNOCKED']);
                    $('#' + itemID_mod).fadeOut(500, function () {
                        $(this).replaceWith(write).fadeIn(500);
                    })
                    $('#sendBody-mod').html("").trigger('keyup');
                    $('#itemModModal').modal('hide');
                    btn.removeAttr('disabled')
                },
                error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    btn.removeAttr('disabled')
                }
            })
        }
        $btn.button('reset');
        $btn.blur();
    })
    //publixh 버튼 내용
    $('#publixhButton-mod').on('click', function () {

        var $btn = $(this).button('loading');
        if ($('#publiBody-mod').html().length > 0 && $('#saleTitle-mod').val().length > 0) {
            var btn = $(this);
            $(this).attr('disabled', 'disabled')
            $.ajax({
                url: "/php/data/modItem.php",
                type: "POST",
                data: {
                    ID: itemID_mod,
                    body: $('#publiBody-mod').html(),
                    body_text: $('#publiBody-mod').text(),
                    for_sale: "Y",
                    price: $('#contentCost-mod').val().length > 0 ? $('#contentCost-mod').val() : 0,
                    category: category_mod,
                    sub_category: sub_category_mod,
                    adult: $('#adult-mod').is(':checked'),
                    ad: $('#ad-mod').is(':checked'),
                    title: $('#saleTitle-mod').val(),
                    folder: folderid_mod,
                    token: token,
                    tag: JSON.stringify($('#publi-tag-mod').tagEditor('getTags')[0].tags),
                    expose: expose_mod,
                    action: "mod_item"
                },
                dataType: 'json',
                success: function (res) {
                    console.log(res)
                    var write = '';
                    var ID = res['ID'];
                    var writer = res['ID_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var title = res['TITLE'];
                    var knock = res['KNOCK'];
                    var price = res['PRICE'];
                    var comment = res['COMMENT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var folderID = null;
                    var foldername = null;
                    var expose = res['EXPOSE']
                    var more = res['MORE'];
                    var tag = res['TAG'] ? res['TAG'].split(' ') : null;
                    if (res['FOLDER'] != null) {
                        folderID = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, true, preview, writer, folderID, foldername, pic, expose, more, tag, pin, res['CATEGORY'], res['SUB_CATEGORY'],res['KNOCKED']);
                    $('#' + itemID_mod).fadeOut(500, function () {
                        $(this).replaceWith(write).fadeIn(500);
                    })
                    $('#saleTitle-mod').val("");
                    $('#contentCost-mod').val("");
                    $('#publiBody-mod').html("").trigger('keyup');
                    $('#itemModModal').modal('hide');
                    btn.removeAttr('disabled')
                },
                error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    btn.removeAttr('disabled')
                }
            })
        } else {
            alert('제목과 본문, 가격을 입력해 주세요')
        }
        $btn.button('reset');
    })
    //공개설정 버튼
    $('#expSublist-mod li').click(function () {
        var exptarget = $(this).text()
        $('#exposeSetting-mod').text(exptarget);
        switch (exptarget) {
            case '나만보기':
                expose_mod = 0;
                break;
            case '친구에게 공개':
                expose_mod = 1;
                break;
            case '전체공개':
                expose_mod = 2;
                break;
        }
    })
    //폴더설정 버튼
    $('#dirSublist-mod li').on('click',function () {
        $('#directorySettingSub-mod').text($(this).text());
        folderid_mod = $(this).attr('folderid');
    })
    //카테고리 리스트 버튼
    var category_mod = null;
    $('#categorySelect-mod li').click(function () {
        $('#category-mod').text($(this).text());
        category_mod = $(this).text();
        function subwrite(sub) {
            $('#subcategorySelect-mod').html('');
            var write = '';
            for (var i = 0; i < sub.length; i++) {
                write += '<li><a>' + sub[i] + '</a></li>'
            }
            $('#subcategorySelect-mod').html(write);
        }

        switch (category_mod) {
            case '매거진':
                var sub = ['IT', '게임', '여행-국내', '여행-해외', '뷰티', '패션', '반려동물'];
                subwrite(sub);
                break;
            case '뉴스':
                var sub = ['일반', '스포츠', '연애', '테크'];
                subwrite(sub);
                break;
            case '소설':
                var sub = ['문학', '에세이', '인문', '자기개발', '교육'];
                subwrite(sub);
                break;
            case '만화':
                var sub = ['로맨스', '판타지', '개그', '미스터리', '호러', 'SF', '무협', '스포츠'];
                subwrite(sub);
                break;
            case '사진':
                var sub = ['일상', '인물', '자연', '여행', '동식물', 'pine_art', '야경', 'GIF'];
                subwrite(sub);
                break;
        }
    })
    //하위 카테고리 리스트 버튼
    var sub_category_mod;
    $(document).on('click', "#subcategorySelect-mod li", function () {
        $('#sub-category-mod').text($(this).text());
        sub_category_mod = $(this).text();
    })
    //가격입력 숫자 검사
    var checkNum = /^[0-9]*$/;
    var costvali_mod = false;
    //가격 입력 검사
    $('#contentCost-mod').on('change', function () {
        var contentCost = $('#contentCost-mod');
        if (!checkNum.test(contentCost.val())) {
            alert('가격은 숫자로 입력해 주세요.');
            $('#contentCost-mod').focus();
            costvali_mod = false;
        } else if (parseInt(contentCost.val()) > 65535) {
            alert('65535픽 이상은 입력되지 않습니다.');
            costvali_mod = false;
        } else {
            costvali_mod = true;
        }
    });
    //유튜브 태그 넣기(upform에서 쓰던것)
    var iframerex = /^<iframe[^>]width=["']?([^>"']+)["']?[^>]height=["']?([^>"']+)["']?[^>]src=["']?([^>"']+)["']?[^>]*><\/iframe>$/i;
    var you_short = /^https:\/\/youtu.be\/[a-zA-Z0-9-_]+$/;
    $('.youtube-iframe').on('keyup', function (e) {
        var tag = $(this).val();
        if (iframerex.test(tag)) {
            tag = $(tag).attr({
                width: 510,
                height: 280
            });
            $(this).val('');
            var body = $(this).parents('div[role="tabpanel"]').find('div[contenteditable="true"]');
            body.append(tag).trigger('keyup');
        } else if (you_short.test(tag)) {

        }
    })
//최상단컨텐츠 버튼 동작
    $(document).on("click", ".itemTop", function (e) {
        var thisitemID = $(this).parents()[5].id;
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {ID: thisitemID, action: "top", mid: mid, token: token},
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
        var idset = thisrepID.split('-');
        var thisrepnum = idset[3];
        var thisitemID = $(this).parents()[11].id;
        $.ajax({
            url: "/php/data/itemAct.php",
            type: "POST",
            data: {ID: thisrepnum, action: "repknock", mid: mid, thisitemID: thisitemID, token: token},
            dataType: 'json',
            success: function (res) {
                if (res['result'] == 'N' && res['reason'] == 'already') {
                    alert('이미 노크하신 댓글입니다.');
                }
                else {
                    $('#' + thisrepID + ' .repknockbad').text(res['KNOCK']);
                }
            }, error: function (request) {
                alert(request.responseText);
            }
        })
    });
    //핀 클릭시 동작
    $(document).on('click', '.pin-a', function () {
        var thisitemID = $(this).parents()[2].id;
        var pin_a = $(this);
        pin_a.removeClass('pin-a')
        if ($(this).hasClass('pinned')) {
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {ID: thisitemID, token: token, action: "delPin", userID: mid},
                dataType: 'json',
                success: function (res) {
                    if (res['result'] == 'Y') {
                        pin_a.addClass('pin-a').removeClass('pubico').removeClass('pico-pin2');
                        pin_a.removeClass('pinned')
                        pin = pin.replace(' ' + thisitemID, '');
                        $('.pin-list a[href="/content/' + thisitemID + '"]').parents('li').remove();
                    } else {
                        alert('작업중 문제가 생겼습니다.')
                    }
                }
            })
        } else {
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {ID: thisitemID, token: token, action: "addPin", userID: mid},
                dataType: 'json',
                success: function (res) {
                    if (res['result'] == 'Y') {
                        pin_a.addClass('pin-a pubico pico-pin2 pinned');

                        pin = pin + ' ' + thisitemID;
                    } else {
                        alert('작업중 문제가 생겼습니다.')
                    }
                }
            })
        }

    });
    //신고 동작
    $(document).on('click', '.itemReport', function () {
        var thisitemID = $(this).parents()[5].id;
        var btn = $(this);
        $(this).removeClass('itemReport');
        if (confirm('해당 게시물을 신고하시겠습니까?')) {
            $.ajax({
                url: "/php/data/itemAct.php",
                type: "POST",
                data: {ID: thisitemID, token: token, action: "report", userID: mid},
                dataType: 'json',
                success: function (res) {
                    if (res['result'] == 'Y') {
                        alert('신고가 완료되었습니다. 해당 게시물이 일정 횟수 이상 신고되면 다른 사용자에게 보여지지 않게 됩니다.');
                    } else if (res['reason'] == 'already') {
                        alert('이미 신고한 게시물입니다.')
                    }
                    btn.addClass('itemReport');
                }
            });
        }
    })
    //게시글의 카테고리 클릭시 게시글 카드 다 지우고 loadOption 다시작성해서 요청
    $(document).on('click', '.item-category,.item-sub_category', function () {
        var category = $(this).hasClass('item-category') ? $(this).text() : $(this).siblings('.item-category').text();
        var sub_category = $(this).hasClass('item-sub_category') ? $(this).text() : null;
        loadOption['nowpage'] = 0;
        loadOption['category'] = category;
        sub_category ? loadOption['sub_category'] = sub_category : delete loadOption['sub_category'];  //loadOption 정의 끝
        //모든카드 삭제 후 다시 로딩할것
        $('.card').each(function () {
            $(this).remove();
        })
        getCards();

    })
    //스크롤할때 열린 아이템보다 스크롤이 아래 있으면 스크롤 추적 끝내고 해당 시간변수 삭제
    $(document).scroll(function () {
        var scrollTop = $(document).scrollTop() + 300;
        $.each(itemPool, function (index, val) {
            if (scrollTop > val.scroll_end) {
                //사람이 다 봤으면 데이터 보내기 시작
                var now = new Date();
                var gap = (now.getTime() - itemPool[findIndex(itemPool, 'ID', val.ID)]['time'].getTime()) / 1000;
                readDone(val.ID, mid, gap);
                itemPool.splice(index, 1);
            }
        })
    })
});

//오브젝트 안에서 키의 값이 특정값인 인덱스 찾기(한개만)
function findIndex(array, attr, val) {
    var len = array.length;
    for (var i = 0; i < len; i++) {
        if (array[i][attr] === val) {
            return i;
        }
    }
}
