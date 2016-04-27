/**
 * Created by gangdong-gyun on 2016. 4. 27..
 */
$(document).ready(function(){
    //로딩 다 끝내면 베스트 게시물 찾아오기
    function getBest(action) {
        $.ajax({
            url: 'php/data/best.php',
            type: "GET",
            dataType: 'json',
            data: {act:action},
            success: function (res) {
                console.log(res)
            }
        })
    }
    getBest('now');
    getBest('daily');
    getBest('weekly');

});