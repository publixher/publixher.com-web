/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
// 쿠키 가져오기
function getCookie(cName) {
    cName = cName + '=';
    var cookieData = document.cookie;
    var start = cookieData.indexOf(cName);
    var cValue = '';
    if (start != -1) {
        start += cName.length;
        var end = cookieData.indexOf(';', start);
        if (end == -1)end = cookieData.length;
        cValue = cookieData.substring(start, end);
    }
    return encodeURIComponent(cValue);
}
//커서 위치에 이미지 추가하기
function pasteHtmlAtCaret(html, selectPastedContent) {
    var sel, range;
    if (window.getSelection) {
        // IE9 and non-IE
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();

            // Range.createContextualFragment() would be useful here but is
            // only relatively recently standardized and is not supported in
            // some browsers (IE9, for one)
            var el = document.createElement("div");
            el.innerHTML = html;
            var frag = document.createDocumentFragment(), node, lastNode;
            while ((node = el.firstChild)) {
                lastNode = frag.appendChild(node);
            }
            var firstNode = frag.firstChild;
            range.insertNode(frag);

            // Preserve the selection
            if (lastNode) {
                range = range.cloneRange();
                range.setStartAfter(lastNode);
                if (selectPastedContent) {
                    range.setStartBefore(firstNode);
                } else {
                    range.collapse(true);
                }
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }
    } else if ((sel = document.selection) && sel.type != "Control") {
        // IE < 9
        var originalRange = sel.createRange();
        originalRange.collapse(true);
        sel.createRange().pasteHTML(html);
        if (selectPastedContent) {
            range = sel.createRange();
            range.setEndPoint("StartToStart", originalRange);
            range.select();
        }
    }
}

$(document).ready(function () {
    //드롭다운안에 클릭했을때 안닫히게 하려면 이렇게
    $('.hasInput .form-control').click(function (e) {
        e.stopPropagation();
    });

    //글쓸때 버튼 클릭할때의 동작
    $('#sendButton').on('click', function () {
        var $btn = $(this).button('loading');
        var ID_target = null;
        if (targetID) {
            if (mid == targetID) {
                ID_target = null;
            } else {
                ID_target = targetID;
            }
        }

        if ($('#sendBody').html().length > 0) {
            var btn = $(this);
            $(this).addClass('disabled')
            $.ajax({
                url: "/php/data/uploadContent.php",
                type: "POST",
                data: {
                    body: $('#sendBody').html(),
                    body_text: $('#sendBody').text(),
                    ID_writer: mid,
                    folder: $folderID,
                    token: token,
                    expose: expose,
                    targetID: ID_target,
                    tags: JSON.stringify($('#send-tag').tagEditor('getTags')[0].tags)
                },
                dataType: 'json',
                success: function (res) {
                    if (res['status'] == -2) {
                        alert('해당 계정은 ' + res['result']['BAN'] + ' 까지 글 작성이 제한되었습니다.');
                        return false;
                    }
                    var write = '';
                    var ID = res['ID'];
                    var writer = res['ID_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var knock = res['KNOCK'];
                    var comment = res['COMMENT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'].replace('profile', 'crop50');
                    var targetID = res['TARGET_ID'];
                    var targetname = res['TARGET_NAME'];
                    var folderID = null;
                    var foldername = null;
                    var expose = res['EXPOSE']
                    var more = res['MORE']
                    if (res['FOLDER'] != null) {
                        folderID = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    var tag = res['TAG'] ? res['TAG'].split(' ') : null;
                    write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic, targetID, targetname, expose, more, tag, pin, res['KNOCKED']);
                    $('#upform').after(write);
                    $('#' + ID).hide().fadeIn()
                        .find('.gif').gifplayer({wait: true});
                    $('#sendBody').html('').removeAttr('style')
                    var tags = $('#send-tag').tagEditor('getTags')[0].tags
                    for (var i = 0; i < tags.length; i++) {
                        $('#send-tag').tagEditor('removeTag', tags[i]);
                    }
                    document.location.href = '#' + ID;
                },
                error: function (request, status, error) {
                    errorReport("send_upload", {
                        body: $('#sendBody').html(),
                        body_text: $('#sendBody').text(),
                        ID_writer: mid,
                        folder: $folderID,
                        token: token,
                        expose: expose,
                        targetID: ID_target,
                        tags: JSON.stringify($('#send-tag').tagEditor('getTags')[0].tags)
                    }, status, error)
                    //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                }, complete: function () {
                    $btn.button('reset');
                    btn.removeClass('disabled')
                }
            })
        }
        $btn.blur();
    })
    //publixh 버튼 내용
    $('#publixhButton').on('click', function () {

        if ($('#publiBody').html().length > 0 && $('#saleTitle').val().length > 0) {
            var $btn = $(this).button('loading');
            var btn = $(this);
            $(this).addClass('disabled')
            $.ajax({
                url: "/php/data/uploadContent.php",
                type: "POST",
                data: {
                    body: $('#publiBody').html(),
                    body_text: $('#publiBody').text(),
                    ID_writer: mid,
                    for_sale: "Y",
                    price: $('#contentCost').val().length > 0 ? $('#contentCost').val() : 0,
                    category: category != null ? category : 'SNS',
                    sub_category: sub_category,
                    adult: $('#adult').is(':checked'),
                    ad: $('#ad').is(':checked'),
                    title: $('#saleTitle').val(),
                    folder: $folderID,
                    token: token,
                    expose: expose,
                    tags: JSON.stringify($('#publi-tag').tagEditor('getTags')[0].tags)
                },
                dataType: 'json',
                success: function (res) {
                    if (res['status'] == -2) {
                        alert('해당 계정은 ' + res['result']['BAN'] + ' 까지 글 작성이 제한되었습니다.');
                        return false;
                    }
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
                    var pic = res['PIC'].replace('profile', 'crop50');
                    var folderID = null;
                    var foldername = null;
                    var expose = res['EXPOSE'];
                    var more = res['MORE']
                    if (res['FOLDER'] != null) {
                        folderID = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    var tag = res['TAG'] ? res['TAG'].split(' ') : null;
                    write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, true, preview, writer, folderID, foldername, pic, expose, more, tag, pin, res['CATEGORY'], res['SUB_CATEGORY'], res['KNOCKED']);
                    $('#upform').after(write);
                    $('#' + ID).hide().fadeIn()
                        .find('.gif').gifplayer({wait: true});
                    $('#saleTitle').val("");
                    $('#contentCost').val("");
                    $('#publiBody').html('').removeAttr('style');

                    var tags = $('#publi-tag').tagEditor('getTags')[0].tags;
                    for (var i = 0; i < tags.length; i++) {
                        $('#publi-tag').tagEditor('removeTag', tags[i]);
                    }
                    document.location.href = '#' + ID;
                },
                error: function (request, status, error) {
                    errorReport("publixh_upload", {
                        body: $('#publiBody').html(),
                        body_text: $('#publiBody').text(),
                        ID_writer: mid,
                        for_sale: "Y",
                        price: $('#contentCost').val().length > 0 ? $('#contentCost').val() : 0,
                        category: category != null ? category : 'SNS',
                        sub_category: sub_category,
                        adult: $('#adult').is(':checked'),
                        ad: $('#ad').is(':checked'),
                        title: $('#saleTitle').val(),
                        folder: $folderID,
                        token: token,
                        expose: expose,
                        tags: JSON.stringify($('#publi-tag').tagEditor('getTags')[0].tags)
                    }, status, error)
                    //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                }, complete: function () {
                    $btn.button('reset');
                    btn.removeClass('disabled')
                }
            })
        } else {
            alert('제목을 입력해 주세요')
        }
    })
    //공개설정 버튼
    var expose = 2;   //기본값 전체공개
    $('#expSublist li').click(function () {
        var exptarget = $(this).text()
        $('#exposeSettingSub').text(exptarget);
        switch (exptarget) {
            case '나만보기':
                expose = 0;
                break;
            case '친구에게 공개':
                expose = 1;
                break;
            case '전체공개':
                expose = 2;
                break;
        }
    })
    //폴더설정 버튼
    var $folderID;
    $('#dirSublist').on('click', 'li', function () {
        $('#directorySettingSub').text($(this).text());
        $folderID = $(this).attr('folderid');
    })

    //카테고리 리스트 버튼
    var category = null;
    $('#categorySelect li').click(function () {
        $('#category').text($(this).text());
        category = $(this).text();
        function subwrite(sub) {
            $('#subcategorySelect').html('');
            $('#sub-category').text('하위 분류');
            sub_category = null;
            var write = '';
            for (var i = 0; i < sub.length; i++) {
                write += '<li><a>' + sub[i] + '</a></li>'
            }
            $('#subcategorySelect').html(write);
        }

        switch (category) {
            case '매거진':
                var sub = ['IT', '게임', '여행-국내', '여행-해외', '뷰티', '패션', '반려동물','맛집'];
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
                var sub = ['일상', '인물', '자연', '여행', '동식물', 'fine_art', '야경', 'GIF'];
                subwrite(sub);
                break;
            default:
                var sub = [''];
                subwrite(sub);
                break;
        }
    })
    //하위 카테고리 리스트 버튼
    var sub_category;
    $(document).on('click', "#subcategorySelect li", function () {
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
        sequentialUploads: true,
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
        }, progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            var upp = $('#up-progress');
            upp.css('width', progress + '%');
            if (progress >= 100) {
                upp.remove();
                $('#sendBody').trigger('keyup')
                $('#publiBody').trigger('keyup')
            }
        }, start: function (e) {
            if (this == $('#fileuploads')[0]) {
                var sendBody = $('#sendBody');
                sendBody.html(sendBody.html() + '<div id="up-progress" style="background-color: lightpink;height: 5px;width: 0;"></div>');
            } else if (this == $('#fileuploadp')[0]) {
                var publiBody = $('#publiBody');
                publiBody.html(publiBody.html() + '<div id="up-progress" style="background-color: lightpink;height: 5px;width: 0;"></div>');
            }
        }, done: function (e, data) {
            var gif = data.files[0].type == 'image/gif' ? true : false;
            var img = '<div><img src="/img/' + data.result['files']['file_crop'] + '" class="BodyPic"></div>';
            if (gif) {
                img = $(img);
                img.children('img').addClass('gif');
                img = img[0].outerHTML;
            }
            pasteHtmlAtCaret(img + '<br>')

        }, fail: function (e, data) {
            alert('파일 업로드중 문제가 발생했습니다. 다시 시도해주세요.')
        }
    });
//해시태그 플러그인
    $('.tag-input').tagEditor({
        delimiter: ', ',
        maxLength: 50
    });
    //fid쿠키의 폴더가 폴더리스트에 있으면 선택되게하는거
    var fid = getCookie('fid');
    if (fid) {
        $('#dirSublist')
            .find("[folderid='" + fid + "']")
            .trigger('click');
    }
    //공개대상 쿠키에서 받아와 선택되게함
    var exp = getCookie('exp');
    if (exp) {
        $('#expSublist')
            .find('li:nth-child(' + (parseInt(exp) + 1) + ')').trigger('click');
    }
    //새 폴더 생성
    $('.new-folder').on('keyup', function (e) {
        if (e.keyCode == 13 && $(this).val().length > 0) {
            var form = $(this);
            var folderName = $(this).val();
            if (/^[ㄱ-ㅎㅏ-ㅣ가-힣a-zA-Z0-9-_]{1,15}$/.test(folderName)) {
                $.ajax({
                    url: "/php/data/profileChange.php",
                    dataType: 'json',
                    type: "POST",
                    data: {folder: folderName, action: "newfolder"},
                    success: function (res) {
                        $('#dirSublist li:last-child').before(
                            $('<li>').attr('folderid', res['ID']).append(
                                $('<a>').attr('href', '#').text(folderName))
                                .fadeIn()
                        )
                        //수정을 위한 모달에도 추가
                        $('#dirSublist-mod li:last-child').before(
                            $('<li>').attr('folderid', res['ID']).append(
                                $('<a>').attr('href', '#').text(folderName))
                                .fadeIn()
                        )
                    }, complete: form.val(''), error: function (xhr, status, error) {
                        errorReport("new_folder", {folder: folderName, action: "newfolder"}, status, error);
                        //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                    }
                })
            } else {
                alert('폴더 이름은 한글,영문,숫자,구분자(-,_) 1~15글자만 허용됩니다')
            }
        }
    });

    $('#send-li,#pub-li').on('show.bs.tab', function (e) {
        var thisbody = $(e.relatedTarget.getAttribute('href')).find('div[contenteditable="true"]');
        var otherbody = $(e.target.getAttribute('href')).find('div[contenteditable="true"]');
        otherbody.html(thisbody.html());
    })
    $('#fileuploads,#fileuploadp').on('click', function () {
        var body;
        if ($(this).is($('#fileuploads'))) {
            body = $('#sendBody');
        } else {
            body = $('#publiBody');
        }
        body.focus();
    })
});