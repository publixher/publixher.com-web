/**
 * Created by gangdong-gyun on 2016. 2. 5..
 */
$(document).ready(function () {

    function callHot(){
        $.ajax({
            url: "/php/data/.php",
            type: "POST",
            data: {seq: thisrepnum, action: "repknock", mid: mid, thisitemID: thisitemID, token: token, age: age},
            dataType: 'json',
            success: function (res) {
                if (res['result'] == 'N' && res['reason'] == 'already') {
                    alert('이미 노크하신 댓글입니다.');
                }
                else {
                    $('#' + thisrepID + ' .repknockbad').text(res['knock']);
                }
            }, error: function (request) {
                alert(request.responseText);
            }
        })
    }
});