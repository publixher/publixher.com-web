/**
 * Created by gangdong-gyun on 2016. 2. 26..
 */
$(document).ready(function(){
    $('#id-ban-3,#id-ban-7,#id-ban-30').on('click', function () {
        if(confirm('정말 해당 ID를 제한하겠습니까?')){
            var days=(($(this)[0].id).split('-'))[2];
            $.ajax({
                url:'/php/data/idManage.php',
                type:'POST',
                dataType:'json',
                data:{days:days,target:targetid,token:token,action:"ban"},
                success: function (res) {
                    if(res['result']=='N' && res['reason']=='already'){
                        alert('이미 제한된 ID입니다.');
                        return 0;
                    }
                    alert('이 ID는 '+days+'일간 로그인이 제한됩니다.')
                }
            })
        }
    })
    $('#id-ban-cancel').on('click', function () {
        if(confirm('정말 해당 ID의 제한을 푸시겠습니까?')){
            $.ajax({
                url:'/php/data/idManage.php',
                type:'POST',
                dataType:'json',
                data:{target:targetid,token:token,action:"release"}
            })
        }
    })
});
