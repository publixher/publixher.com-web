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
                for (var i = 0; i <res.length; i++) {
                    $('<li>')
                        .appendTo($('#' + action + '-hot-list'))
                        .addClass(action + '-list-item')
                        .append(
                            $('<img>')
                                .attr({
                                    src:res[i]['WRITER_PIC'],
                                    onclick:'location.href="/profile/"'+res[i]['ID_WRITER']+'"'
                                })
                                .addClass('hot-pic')
                            ,$('<a>')
                                .attr('href', '/content/' + res[i]['ID_CONTENT'])
                                .text(res[i]['BODY'])
                                .append(
                                    $('<span>')
                                        .addClass('hot-data')
                                        .append(
                                            $('<a>')
                                                .attr('href', '/profile/' + res[i]['ID_WRITER'])
                                                .text(res[i]['USER_NAME'])
                                        )

                                    , $('<span>')
                                        .addClass('hot-knock')
                                        .text(res[i]['KNOCK'])
                                    , $('<span>')
                                        .addClass('hot-reply')
                                        .text(res[i]['COMMENT'])
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