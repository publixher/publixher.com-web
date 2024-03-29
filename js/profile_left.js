/**
 * Created by gangdong-gyun on 2016. 2. 26..
 */
$(document).ready(function () {
    //로그인 제한 걸기
    $('#id-ban-3,#id-ban-7,#id-ban-30').on('click', function () {
        if (confirm('정말 해당 ID를 제한하겠습니까?')) {
            var days = (($(this)[0].id).split('-'))[2];
            $.ajax({
                url: '/php/data/idManage.php',
                type: 'POST',
                dataType: 'json',
                data: {days: days, target: targetid, token: token, action: "ban"},
                success: function (res) {
                    if (res['result'] == 'N' && res['reason'] == 'already') {
                        alert('이미 제한된 ID입니다.');
                        return 0;
                    }
                    alert('이 ID는 ' + days + '일간 로그인이 제한됩니다.')
                }, error: function (xhr, status, error) {
                    errorReport("ban", {days: days, target: targetid, token: token, action: "ban"}, status, error);
                    //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                }
            })
        }
    })
    //로그인 제한 풀기
    $('#id-ban-cancel').on('click', function () {
        if (confirm('정말 해당 ID의 제한을 푸시겠습니까?')) {
            $.ajax({
                url: '/php/data/idManage.php',
                type: 'POST',
                dataType: 'json',
                data: {target: targetid, token: token, action: "release"}, error: function (xhr, status, error) {
                    errorReport("release", {target: targetid, token: token, action: "release"}, status, error);
                    //alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                }
            })
        }
    });
    //폴더 삭제
    $(document).on("click", ".deletefolder", function (e) {
        var folderName = $(this).siblings('a').text();
        var li = $(this).parent();
        if (confirm('정말 ' + folderName + ' 폴더를 삭제 하시겠습니까? (폴더 안의 내용물들은 전부 비분류 처리됩니다).')) {
            var thisfolder = $(this).attr('data-folderid');
            $.ajax({
                url: "/php/data/profileChange.php",
                type: "POST",
                data: {action: "deletefolder", userID: targetID, folderid: thisfolder},
                dataType: 'json',
                success: function (res) {
                    if (res["message"] == "폴더 삭제가 완료되었습니다") {
                        li.fadeOut(function () {
                            $(li).remove();
                        })
                    }else{
                        alert(res["message"])
                    }
                }, error: function (xhr, status, error) {
                    errorReport("deletefolder", {
                        action: "deletefolder",
                        userID: mid,
                        folderid: thisfolder
                    }, status, error);
                    // alert('오류가 탑지되어 자동으로 서버에 오류내역이 저장되었습니다.\n이용에 불편을 드려 죄송합니다.\n새로고침 후 다시 이용해 주세요.')
                }
            })
        }
    });
});
