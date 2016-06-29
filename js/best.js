/**
 * Created by gangdong-gyun on 2016. 4. 27..
 */
$(document).ready(function () {
    //스피너
    var spinner = $('<div>')
        .attr('data-loader', 'spinner')
        .addClass('load-item best-load')
    $('#market').append(spinner)
    //로딩 다 끝내면 베스트 게시물 찾아오기
    function getBest(action) {
        var data={act: action};
        $.ajax({
            url: '/php/data/best.php',
            type: "GET",
            dataType: 'json',
            data: data,
            success: function (res) {
                spinner.detach();
                for (var i = 0; i < res.length; i++) {
                    var img = '';
                    if (res[i]['WRITER_PIC']) {
                        img = $('<img>').attr({
                            src: res[i]['WRITER_PIC'],
                            onclick: 'location.href="/content/' + res[i]['ID_CONTENT'] + '"'
                        }).addClass('hot-pic')
                    } else {
                        img = $('<div>').addClass('hot-no-img').attr('onclick', 'location.href="/content/' + res[i]['ID_CONTENT'] + '"');
                    }
                    $('<li>')
                        .appendTo($('#' + action + '-hot-list'))
                        .addClass(action + '-list-item')
                        .append($('<div>').append(img).addClass('hot-pic-wrap')
                            , $('<span>').append(
                                $('<a>').addClass('hot-body').attr('href', '/content/' + res[i]['ID_CONTENT']).text(res[i]['BODY'])
                                , $('<a>').attr('href', '/profile/' + res[i]['ID_WRITER']).text(res[i]['USER_NAME']).addClass('hot-data')
                                , $('<span>').addClass('hot-knock').append('<span class="pubico pico-knock"></span>').text(res[i]['KNOCK'])
                                , $('<span>').addClass('hot-reply').append('<span class="pubico pico-comment"></span>').text(res[i]['COMMENT'])
                            )
                        )

                }
            },error:function(xhr,status,error){
                errorReport("getBest",data,status,error)
                //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
            }
        })
    }

    getBest('now');
    getBest('daily');
    getBest('weekly');

});
