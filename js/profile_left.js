/**
 * Created by gangdong-gyun on 2016. 2. 26..
 */
$(document).ready(function(){
    //친구요청
    $('#friendRequest').on('click',function () {
        $.ajax({
            url: "/php/data/friend.php",
            type: "POST",
            data: {targetseq:targetid,myseq:myseq,action:"request",token:token,age:age},
            dataType: 'json',
            success: function (res) {
                if(res['result']=='Y'){
                    alert('상대방이 수락하면 친구가 됩니다.');
                    $('#friendRequest').text('이미 친구신청을 했네요');
                    $('#friendRequest').attr('id','alreadyRequest');
                }else if(res['result']=='N'&& res['reason']=='already requested'){
                    alert('이미 친구신청을 했습니다.')
                }
            }
        });
    });
    $('.friendok').on('click', function () {
        var fid=$(this).attr('fid');
        var requestid=$(this).attr('requestid');
        var pa=$(this).parent()[0];
        $.ajax({
            url:"/php/data/friend.php",
            type:"POST",
            data:{targetseq:fid,requestid:requestid,action:"friendok",myseq:myseq},
            dataType:'json',
            success: function (res) {
                if(res['result']=='Y'){
                    pa.remove();
                    $('#frequestnum').text($('#frequestnum').text()-1);
                    if($('#frequestnum').text()==0){
                        $('#frequestli').append("<li><a>친구요청이 없습니다</a></li>")
                    }
                }
            }
        })
    })
    $('.friendno').on('click', function () {
        var requestid=$(this).attr('requestid');
        var pa=$(this).parent()[0];
        $.ajax({
            url:"/php/data/friend.php",
            type:"POST",
            data:{requestid:requestid,action:"friendno"},
            dataType:'json',
            success: function (res) {
                if(res['result']=='Y'){
                    pa.remove();
                    $('#frequestnum').text($('#frequestnum').text()-1);
                    if($('#frequestnum').text()==0){
                        $('#frequestli').append("<li><a>친구요청이 없습니다</a></li>")
                    }
                }
            }
        })
    })
});
