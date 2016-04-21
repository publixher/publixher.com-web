/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
$(document).ready(function(){
    //탑컨텐츠 탐색
    $.ajax({
        url: "/php/data/getOne.php",
        type: "GET",
        data: {profile:targetID},
        dataType: 'json',
        success: function (res) {
            console.log(res)
            if (res['result'] == 'N' && res['reason'] == 'no top') {
                return;
            } else if (res['result'] == 'N' && res['reason'] == 'deleted') {
                return;
            } else {
                if (res['FOR_SALE'] == "N") {
                    var write = '';
                    var ID = res['ID'];
                    var writer = res['ID_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var knock = res['KNOCK'];
                    var comment = res['COMMENT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var targetID = res['ID_TARGET'];
                    var targetname = res['TARGET_NAME'];
                    var folderID = null;
                    var foldername = null;
                    var expose=res['EXPOSE'];
                    if (res['FOLDER'] != null) {
                        folderID = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    var more = res['MORE'];
                    var tag = res['TAG'] ? res['TAG'].split(' ') : null;
                    write = itemLoad(write, ID, name, date, knock, comment, preview, writer, folderID, foldername, pic,targetID,targetname,expose,more,tag,pin);
                    $('#topcon').append(write);
                } else {
                    var write = '';
                    var ID = res['ID'];
                    var writer = res['ID_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var title = res['TITLE'];
                    var knock = res['KNOCK'];
                    var price = res['PRICE'];
                    var comment = res['COMMENT'];
                    var bought = res['BOUGHT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var folderID = null;
                    var foldername = null;
                    var expose=res['EXPOSE']
                    if (res['FOLDER'] != null) {
                        folderID = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    var more = res['MORE'];
                    var tag = res['TAG'] ? res['TAG'].split(' ') : null;
                    write = itemForSaleLoad(write, ID, name, date, title, knock, price, comment, bought, preview, writer, folderID, foldername, pic,expose,more,tag,pin);
                    $('#topcon').append(write);
                }
            }
        }, error: function (request) {
            alert(request.responseText);
        }
    })
});