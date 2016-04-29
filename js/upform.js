/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
$(document).ready(function () {
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
            var btn=$(this);
            $(this).attr('disabled','disabled');
            console.log($('#sendBody').text())
            $.ajax({
                url: "/php/data/uploadContent.php",
                type: "POST",
                data: {
                    body: $('#sendBody').html(),
                    body_text:$('#sendBody').text(),
                    ID_writer: mid,
                    folder: $folderID,
                    token: token,
                    expose: expose,
                    targetID: ID_target,
                    tags:JSON.stringify($('#send-tag').tagEditor('getTags')[0].tags)
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
                    var pic = res['PIC'].replace('profile', 'crop50');
                    var targetID = res['ID_TARGET'];
                    var targetname = res['TARGET_NAME'];
                    var folderID = null;
                    var foldername = null;
                    var expose = res['EXPOSE']
                    var more = res['MORE']
                    if (res['FOLDER'] != null) {
                        folderID = res['FOLDER'];
                        foldername = res['DIR'];
                    }
                    var tag=res['TAG']?res['TAG'].split(' '):null;
                    write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic,targetID,targetname,expose,more,tag,pin);
                    $('#upform').after(write);
                    $('#sendBody').html("").trigger('input').trigger('keyup');
                    var tags=$('#send-tag').tagEditor('getTags')[0].tags
                    for(var i=0;i<tags.length;i++){
                        $('#send-tag').tagEditor('removeTag',tags[i]);
                    }
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
    $('#publixhButton').on('click', function () {
        var $btn = $(this).button('loading');
        if ($('#publiBody').html().length > 0 && $('#saleTitle').val().length > 0 && $('#contentCost').val().length>0) {
            var btn=$(this);
            $(this).attr('disabled','disabled')
            $.ajax({
                url: "/php/data/uploadContent.php",
                type: "POST",
                data: {
                    body: $('#publiBody').html(),
                    body_text:$('#publiBody').text(),
                    ID_writer: mid,
                    for_sale: "Y",
                    price: $('#contentCost').val(),
                    category: category,
                    sub_category: sub_category,
                    adult: $('#adult').is(':checked'),
                    ad: $('#ad').is(':checked'),
                    title: $('#saleTitle').val(),
                    folder: $folderID,
                    token: token,
                    expose: expose,
                    tags:JSON.stringify($('#publi-tag').tagEditor('getTags')[0].tags)
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
                    var pic = res['PIC'].replace('profile', 'crop50');
                    var folderID = null;
                    var foldername = null;
                    var expose = res['EXPOSE'];
                    var more = res['MORE']
                    if (res['FOLDER'] != null) {
                        folderID = res['FOLDER'];
                        foldername = res['DIR'];
                    }
                    var tag=res['TAG']?res['TAG'].split(' '):null;
                    write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, true, preview, writer, folderID, foldername, pic, expose, more,tag,pin);
                    $('#upform').after(write);
                    $('#saleTitle').val("");
                    $('#contentCost').val("");
                    $('#publiBody').html("").trigger('input').trigger('keyup');
                    var tags=$('#publi-tag').tagEditor('getTags')[0].tags;
                    for(var i=0;i<tags.length;i++){
                        $('#publi-tag').tagEditor('removeTag',tags[i]);
                    }
                    btn.removeAttr('disabled')
                },
                error: function (request, status, error) {
                    alert("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                    btn.removeAttr('disabled')
                }
            })
        } else {
            alert('제목과 본문 , 가격을 입력해 주세요')
        }
        $btn.button('reset');
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
    $('#dirSublist li').click(function () {
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
            var write = '';
            for (var i = 0; i < sub.length; i++) {
                write += '<li><a>' + sub[i] + '</a></li>'
            }
            $('#subcategorySelect').html(write);
        }

        switch (category) {
            case '맛집':
                var sub = ['한식','양식','중식','패스트푸드','배달','술집','카페'];
                subwrite(sub);
                break;
            case '주거':
                var sub = ['원룸','하숙','고시원','오피스텔','기숙사'];
                subwrite(sub);
                break;
            case '학업':
                var sub = ['시험 후기','강의 후기','스터디 모집'];
                subwrite(sub);
                break;
            case '장터':
                var sub = ['교재장터','의류','잡화','디지털'];
                subwrite(sub);
                break;
            case '홍보':
                var sub = ['알바 구인','과외 구인','교내 홍보','교외 홍보'];
                subwrite(sub);
                break;
            case '취업':
                var sub = ['인턴','공채'];
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
        IDuentialUploads: true,
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
            if (progress == 100) {
                upp.remove()
                $('#sendBody').trigger('keyup')
                $('#publiBody').trigger('keyup')
            }
        }, start: function (e) {
            if (this == $('#fileuploads')[0]) {
                var sendBody = $('#sendBody');
                sendBody.html(sendBody.html() + '<div id="up-progress" style="background-color: lightpink;height: 5px;width: 0;"></div>');
            } else if (this == $('#fileuploadp')[0]) {
                var publiBody = $('#publiBody')
                publiBody.html(publiBody.html() + '<div id="up-progress" style="background-color: lightpink;height: 5px;width: 0;"></div>');
            }
        }, done: function (e, data) {
            if (this == $('#fileuploads')[0]) {
                var sendBody = $('#sendBody');
                sendBody.html(sendBody.html() + "<img src='/img/" + data.result['files']['file_crop'] + "' class='BodyPic'><br><br>");
                sendBody.height(sendBody.height() + data.result['files']['file_height'] + 8);
            } else if (this == $('#fileuploadp')[0]) {
                var publiBody = $('#publiBody')
                publiBody.html(publiBody.html() + "<img src='/img/" + data.result['files']['file_crop'] + "' class='BodyPic'><br><br>");
                publiBody.height(publiBody.height() + data.result['files']['file_height'] + 8);
            }
        }, fail: function (e, data) {
            alert('파일 업로드중 문제가 발생했습니다. 다시 시도해주세요.')
        }
    })
//해시태그 플러그인
    $('.tag-input').tagEditor({
        delimiter:', ',
        maxLength:50
    });
});

//텍스트에이리어 입력시 자동 크기조정
function resize(obj) {
    obj.style.height = "1px";
    obj.style.height = (23 + obj.scrollHeight) + "px";
}
