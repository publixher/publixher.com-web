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
        $.ajax({
            url: '/php/data/best.php',
            type: "GET",
            dataType: 'json',
            data: {act: action},
            success: function (res) {
                spinner.detach();
                console.log(res)
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
            }
        })
    }

    getBest('now');
    getBest('daily');
    getBest('weekly');

});
