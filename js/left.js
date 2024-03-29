/**
 * Created by gangdong-gyun on 2016. 3. 29..
 */
$(document).ready(function(){
    //구독 갱신
    $('.accordion-toggle,.nameuser').on('click', function () {
        var mu;
        if($(this).hasClass('accordion-toggle')) {
            mu = ($(this).attr('href')).replace('#subscribe-collapse', '')
        }else{
            mu=($(this).attr('href')).replace('/profile/', '')
        }
        $.ajax({url:"php/data/subscribe.php", type: "GET", data: {mu:mu,action:"check"}, dataType: 'json',error:function(xhr,status,error){
            errorReport("subscribe_check",{mu:mu,action:"check"},status,error);
            //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
        }})
        $('.newcontent[data-substarget='+mu+']').remove();
    })
    //건의 및 오류 신고
    $('#report-button').on('click', function () {
        var report_body=$('#report').val();
        var btn=$(this);
        $(this).addClass('disabled');
        $.ajax({url:"php/data/report.php",type:"POST",data:{report:report_body,userID:mid},dataType:'json',success: function () {
            //alert('건의해주셔서 감사합니다. 더욱 상세한 건의를 원하시면 cs@throwout.com으로 메일 보내주시기 바랍니다.');
            $('#report').val('');
            btn.removeClass('disabled');
        }})
    })
    $(document).on('click','#subscribe-btn,#community-btn',function () {
        loadOption={nowpage:0,userID:mid};
        $(this).attr('id')=='subscribe-btn'?loadOption['subscribe']=true:loadOption['community']=true;
        $('.card').each(function(){
            $(this).remove();
        })
        getCards();
    })
});