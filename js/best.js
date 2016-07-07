/**
 * Created by gangdong-gyun on 2016. 4. 27..
 */
$(document).ready(function () {
    $('#best-category-sel').click(function (e) {
        e.stopPropagation();
    });
    //스피너
    var spinner = $('<div>')
        .attr('data-loader', 'spinner')
        .addClass('load-item best-load')
    $('#market').append(spinner)
    //로딩 다 끝내면 베스트 게시물 찾아오기
    function getBest(action) {
        var data = {act: action};
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
            }, error: function (xhr, status, error) {
                errorReport("getBest", data, status, error)
                //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
            }
        })
    }

    getBest('now');
    getBest('daily');
    getBest('weekly');

    function getBestInCategory(category, sub_category) {
        $.ajax({
            url: '/php/data/best.php',
            type: "GET",
            data: {act: 'category', category: category, sub_category: sub_category},
            success: function (res) {
                replacer(res,'now')
                replacer(res,'daily')
                replacer(res,'weekly')

            }
        })
    }

    function replacer(res,section){
        var section_html='';
        for (var i = 0; i < res[section].length; i++) {
            var img = '';
            if (res[section][i]['IMG']) {
                img = $('<img>').attr({
                    src: res[section][i]['IMG'],
                    onclick: 'location.href="/content/' + res[section][i]['ID'] + '"'
                }).addClass('hot-pic')
            } else {
                img = $('<div>').addClass('hot-no-img').attr('onclick', 'location.href="/content/' + res[section][i]['ID'] + '"');
            }

            var li = $('<div>').append(
                $('<li>')
                    .addClass(section+'-list-item')
                    .append(
                        $('<div>').append(img).addClass('hot-pic-wrap'),
                        $('<a>').addClass('hot-body').attr('href', '/content/' + res[section][i]['ID']).text(res[section][i]['TITLE'])
                    )
            )
            section_html += li.html();
        }
        $('#'+section+'-hot-list').html('').append(section_html);
    }
    $('#best-category-sel input').change(function (e) {
        var category = [];
        var sub_category = [];
        if ($(this).hasClass('best-category-checkbox')) {
            if($(this).prop('checked')==false){
                $('.best-sub_category-checkbox[data-best-category='+$(this).val()+']').prop('checked',false)
            }else{
                $('.best-sub_category-checkbox[data-best-category='+$(this).val()+']').prop('checked',true)
            }
        }else{
            $('.best-category-checkbox[value=' + $(this).attr('data-best-category') + ']').prop('checked', true)
        }
        $('.best-category-checkbox:checked').each(function () {
            category.push($(this).val())
        });
        $('.best-sub_category-checkbox:checked').each(function () {
            sub_category.push($(this).val())
        });
        getBestInCategory(category, sub_category)
    })
});
