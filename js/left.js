/**
 * Created by gangdong-gyun on 2016. 3. 29..
 */
$(document).ready(function(){
    //구독 갱신
    $('.accordion-toggle,.nameuser').on('click', function () {
        var mu;
        if($(this).hasClass('accordion-toggle')) {
            mu = ($(this).attr('href')).replace('#collapse', '')
        }else{
            mu=($(this).attr('href')).replace('/profile/', '')
        }
        $.ajax({url:"php/data/subscribe.php", type: "GET", data: {mu:mu,action:"check"}, dataType: 'json'})
        $('.newcontent[data-substarget='+mu+']').remove();
    })
    //건의 및 오류 신고
    $('#report-button').on('click', function () {
        var report_body=$('#report').val();
        var btn=$(this).attr('disabled','disabled')
        $.ajax({url:"php/data/report.php",type:"POST",data:{report:report_body,userID:mid},dataType:'json',success: function () {
            alert('건의해주셔서 감사합니다. 더욱 상세한 건의를 원하시면 cs@throwout.com으로 메일 보내주시기 바랍니다.');
            btn.removeAttr('disabled');
        }})
    })
});