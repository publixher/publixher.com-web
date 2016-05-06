/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
$(document).ready(function () {
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
                    alert('이미 노크하신 게시물입니다.');
                }
            }, error: function (request, status, error) {
                alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                knockbtn.addClass('knock');
            }
        })
    });

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
            tail.append('<div class="tab-comment"></div>');
            var tab_comment = $('#' + thisitemID + ' .tail .tab-comment');
            var word = '<div role="tabpanel">'
            //정렬별 선택패널
            word += '<ul class="nav nav-tabs" role="tablist" id="repnav-"' + thisitemID + '>'
            word += '<li role="presentation" class="active"><a class="bestrep" href="#best-' + thisitemID + '" aria-controls="best-' + thisitemID + '" role="tab" data-toggle="tab">베스트 댓글</a></li>'
            word += '<li role="presentation"><a class="timerep" href="#time-' + thisitemID + '" aria-controls="time-' + thisitemID + '" role="tab" data-toggle="tab">시간순 댓글</a></li>'
            word += '<li role="presentation"><a class="frierep" href="#frie-' + thisitemID + '" aria-controls="frie-' + thisitemID + '" role="tab" data-toggle="tab">친구의 댓글</a></li>'
            word += '</ul>'
            //정렬별 댓글
            word += '<div class="tab-content">'
            word += '      <div role="tabpanel" class="tab-pane active" id="best-' + thisitemID + '"></div>'
            word += '   <div role="tabpanel" class="tab-pane" id="time-' + thisitemID + '"></div>'
            word += '    <div role="tabpanel" class="tab-pane" id="frie-' + thisitemID + '"></div>'
            word += '    </div></div>'
            tab_comment.append('<div contenteditable="true" type="text" class="commentReg form-control" style="width: 510px;height: 25px;white-space=normal" onkeyup="resize(this)" oninput="resize(this)"></div>');
            //댓글 태그기능
            tab_comment
                .append(
                    $('<div>')  //드롭다운 div
                        .addClass('dropdown')
                        .append(
                            $('<button>')
                                .addClass('btn btn-default dropdown-toggle reply-tag-btn')
                                .attr({
                                    'type': 'button',
                                    'data-toggle': 'dropdown',
                                    'aria-expanded': 'true',
                                    'id': thisitemID + '-rep-tag'
                                })
                                .append(
                                    $('<span>')
                                        .addClass('pubico pico-person-plus')
                                )
                            , $('<ul>') //태그 리스트 안에 input이 들어간다
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
                                                        if(!ul.find('.tag-load')) {
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
                                                                console.log(res)
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
            tab_comment.append(word);
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
                        for (var i = 0; i < Object.keys(res).length - 2; i++) {
                            var write = '';
                            var ID = res[i]['ID'];
                            var name = res[i]['USER_NAME'];
                            var date = res[i]['REPLY_DATE'];
                            var reply = res[i]['REP_BODY'];
                            var knock = res[i]['KNOCK'];
                            write += '<div class=commentReply id="' + where + '-rep-' + ID + '">';
                            write += '<table style="margin-top: 5px;margin-bottom: 5px;"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'] + '" class="profilepic"></div></td>';
                            write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;"><span class="reply-body">' + reply + '</span><span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">대댓글</a> <span class="repreplybad">' + res[i]['SUB_REPLY'] + '</span>';
                            if (mid == res[i]['ID_USER'] || level == 99) {
                                write += ' <a class="repdel">삭제</a>'
                            }
                            write += '</span></span></td></tr></table></div>';
                            list.append(write);
                            var ind = parseInt(list.attr('index')) + 1;
                            list.attr('index', ind);
                        }
                        if (res['more'] == 1) {
                            list.append('<div style="height: 20px;text-align: center" class="cursor"><span class="caret repbtn" style="cursor: pointer;"></span></div>')
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
                        var word = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;" class="nobest">아직 베스트 댓글이 없어요 >,.<;;</div>';
                        $('#best-' + thisitemID).append(word);
                        registRep(res, 'time-' + thisitemID);
                        $('a[href=#time-' + thisitemID + ']').trigger('click');
                    } else if (res['result'] == 'NO') {
                        var word = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;" class="nobest">아직 베스트 댓글이 없어요 >,.<;;</div>';
                        var word2 = '<div style="text-align: center;margin: 20px 0 20px 0;font-size: 23px;" class="nobest">아직 댓글이 없어요 >,.<;;</div>';
                        $('#best-' + thisitemID).append(word);
                        $('#time-' + thisitemID).append(word2);
                        $('a[href=#time-' + thisitemID + ']').trigger('click');

                    }
                }, error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                }
            })
            tail.addClass('opend-comment');
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
                    for (var i = 0; i < Object.keys(res).length - 2; i++) {
                        var write = '';
                        var ID = res[i]['ID'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REP_BODY'];
                        var knock = res[i]['KNOCK'];
                        write += '<div class=commentReply id="' + where + '-rep-' + ID + '">';
                        write += '<table style="margin-top: 5px;margin-bottom: 5px;"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'] + '" class="profilepic"></div></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;"<span class="reply-body">' + reply + '</span><span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">대댓글</a> <span class="repreplybad">' + res[i]['SUB_REPLY'] + '</span>';
                        if (mid == res[i]['ID_USER'] || level == 99) {
                            write += ' <a class="repdel">삭제</a>'
                        }
                        write += '</span></span></td></tr></table></div>';
                        list.append(write);
                        var ind = parseInt(list.attr('index')) + 1;
                        list.attr('index', ind);
                    }
                    if (res['more'] == 1) {
                        list.append('<div style="height: 20px;text-align: center" class="cursor"><span class="caret repbtn" style="cursor: pointer;"></span></div>')
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

                    var numrep = Object.keys(res).length - 2;
                    for (var i = 0; i < numrep; i++) {
                        var write = '';
                        var ID = res[i]['ID'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REPLY'];
                        var knock = res[i]['KNOCK'];
                        write += '<div class=commentReply id="' + where + '-rep-' + ID + '">';
                        write += '<table style="margin-top: 5px;margin-bottom:5px"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'].replace('profile', 'crop34') + '" class="profilepic"></div></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;"><span class="reply-body">' + reply + '</span><span class="repaction"><a class="repknock">노크</a> <span class="repknockbad">' + knock + '</span> <a class="repreply">대댓글</a> <span class="repreplybad">' + res[i]['SUB_REPLY'] + '</span>';
                        if (mid == res[i]['ID_USER'] || level == 99) {
                            write += ' <a class="repdel">삭제</a>'
                        }
                        write += '</span></span></td></tr></table></div>';
                        btn.before(write);
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
    //코멘트 등록 동작
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
                    taglist: taglist
                },
                dataType: 'json',
                success: function (res) {
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
        } else {
            resize(this)
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
                        alert('삭제되었습니다.');
                        $('#' + thisrep + ' .reply-body').text('해당 댓글은 삭제되었습니다.');
                    }
                    else alert('동작중 문제가 발생했습니다. 다시 시도해 주세요.');
                }
            })
        }
    })
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
                    subrep_list.append('<div contenteditable="true" id="' + thispanelrep + '-form" class="commentReg_sub form-control" style="width: 100%;height: 25px;white-space=normal" onkeyup="resize(this)" oninput="resize(this)"></div>');
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
                                                                if(!ul.find('.tag-load')) {
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
                        )
                    if (res['result'] != 'NO') {
                        function registRep(res) {
                            var repnum = Object.keys(res).length - 2;
                            for (var i = 0; i < repnum; i++) {
                                var write = '';
                                var ID = res[i]['ID'];
                                var name = res[i]['USER_NAME'];
                                var date = res[i]['REPLY_DATE'];
                                var reply = res[i]['REPLY'];
                                write += '<div class=commentReply id="' + thispanelrep + '-subrep-' + ID + '">';
                                write += '<table style="margin-top: 5px;margin-bottom:5px;"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'].replace('profile', 'crop34') + '" class="profilepic"></div></td>';
                                write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;"><span class="reply-body">' + reply + '</span></span>'
                                if (mid == res[i]['ID_USER'] || level == 99) {
                                    write += ' <span class="repaction"><a class="sub-repdel">삭제</a></span>'
                                }
                                write += '</td></tr></table></div>';
                                subrep_list.append(write);
                                var ind = parseInt(subrep_list.attr('index')) + 1;
                                subrep_list.attr('index', ind);
                            }
                            if (res['more'] == 1) {
                                subrep_list.append('<div style="height: 20px;text-align: center" class="cursor"><span class="caret repbtn_sub" style="cursor: pointer;"></span></div>')
                            }
                        }

                        //인덱스 붙이기
                        if (!subrep_list.attr('index')) {
                            subrep_list.attr('index', '0');
                        }
                        registRep(res);
                    }
                }
            });
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
                    console.log(res)
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
                    for (var i = 0; i < Object.keys(res).length - 2; i++) {
                        var write = '';
                        var ID = res[i]['ID'];
                        var name = res[i]['USER_NAME'];
                        var date = res[i]['REPLY_DATE'];
                        var reply = res[i]['REPLY'];
                        write += '<div class=commentReply id="' + where + '-rep-' + ID + '">';
                        write += '<table style="margin-top: 5px;margin-bottom:5px;"><tr><td style="width: 54px;height: 34px;"><div class="rep-profilepic-wrap"><img src="' + res[i]['PIC'].replace('profile', 'crop34') + '" class="profilepic"></div></td>';
                        write += '<td class="rep"><span class="writer"> <a href="/profile/' + res[i]['ID_USER'] + '">' + name + '</a> &nbsp;<span class="timeago">' + date + '</span></span><br><span style="font-size: 12px;">' + reply + '</span></td></tr></table></div>';
                        btn.before(write);
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
            tail.append('<div class="tab-share"></div>')
            var tab_share = $('#' + thisitemID + ' .tail .tab-share');
            var text = '이 게시물의 url<br><div class="form-control linkurl">' + linkstr + '</div>';
            tab_share.append(text)
            tail.addClass('opend-share')
        }

    });
    //구매버튼(가격표시)동작
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
                        priceSpan.html('<a>더보기</a>');
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
            data: {ID: thisitemID, action: "del", token: token},
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

                    if (res['CATEGOTY']) {
                        $('#category-mod').text(res['CATEGORY']);
                        $('#sub-category-mod').text(res['SUB_CATEGORY']);
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
            if (this == $('#fileuploads-mod')[0]) {
                var sendBody_mod = $('#sendBody-mod');
                sendBody_mod.html(sendBody_mod.html() + "<img src='/img/" + data.result['files']['file_crop'] + "' class='BodyPic'><br><br>");
                sendBody_mod.height(sendBody_mod.height() + data.result['files']['file_height'] + 8);
            } else if (this == $('#fileuploadp-mod')[0]) {
                var publiBody_mod = $('#publiBody-mod')
                publiBody_mod.html(publiBody_mod.html() + "<img src='/img/" + data.result['files']['file_crop'] + "' class='BodyPic'><br><br>");
                publiBody_mod.height(publiBody_mod.height() + data.result['files']['file_height'] + 8);

            }
        }, fail: function (e, data) {
            alert('파일 업로드중 문제가 발생했습니다. 다시 시도해주세요.<img src="/img/sorry.jpeg">')
        }
    })
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
                        foldername = res['DIR'];
                    }
                    write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag, pin);
                    $('#' + itemID_mod).replaceWith(write)
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
        if ($('#publiBody-mod').html().length > 0 && $('#saleTitle-mod').val().length > 0 && $('#contentCost-mod').val().length > 0) {
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
                    price: $('#contentCost-mod').val(),
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
                        foldername = res['DIR'];
                    }
                    write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, true, preview, writer, folderID, foldername, pic, expose, more, tag);
                    $('#' + itemID_mod).replaceWith(write)
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
    $('#dirSublist-mod li').click(function () {
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

        switch (category) {
            case '맛집':
                var sub = ['한식', '양식', '중식', '패스트푸드', '배달', '술집', '카페'];
                subwrite(sub);
                break;
            case '주거':
                var sub = ['원룸', '하숙', '고시원', '오피스텔', '기숙사'];
                subwrite(sub);
                break;
            case '학업':
                var sub = ['시험 후기', '강의 후기', '스터디 모집'];
                subwrite(sub);
                break;
            case '장터':
                var sub = ['교재장터', '의류', '잡화', '디지털'];
                subwrite(sub);
                break;
            case '홍보':
                var sub = ['알바 구인', '과외 구인', '교내 홍보', '교외 홍보'];
                subwrite(sub);
                break;
            case '취업':
                var sub = ['인턴', '공채'];
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
        } else if (contentCost.val().parseint > 65535) {
            alert('65535픽 이상은 입력되지 않습니다.');
            costvali_mod = false;
        } else {
            costvali_mod = true;
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
                console.log(res)
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
                        pin_a.addClass('pin-a').addClass('pubico').addClass('pico-pin2');
                        pin_a.addClass('pinned');

                        pin = pin + ' ' + thisitemID;
                    } else {
                        alert('작업중 문제가 생겼습니다.')
                    }
                }
            })
        }

    })
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
    $(document).on("dragstart", "img,a", function () {
        return false;
    });
});

//텍스트에이리어 입력시 자동 크기조정
function resize(obj) {
    obj.style.height = "1px";
    obj.style.height = (23 + obj.scrollHeight) + "px";
}