/**
 * Created by gangdong-gyun on 2016. 3. 30..
 */
$(document).ready(function(){
    //탑컨텐츠 탐색
    $.ajax({
        url: "/php/data/getOne.php",
        type: "GET",
        data: loadOption,
        dataType: 'json',
        success: function (res) {
            if (res['result'] == 'N' && res['reason'] == 'no top') {
                return;
            } else if (res['result'] == 'N' && res['reason'] == 'deleted') {
                return;
            } else {
                if (res['FOR_SALE'] == "N") {
                    var write = '';
                    var seq = res['SEQ'];
                    var writer = res['SEQ_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var knock = res['KNOCK'];
                    var comment = res['COMMENT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var targetseq = res['SEQ_TARGET'];
                    var targetname = res['TARGET_NAME'];
                    var folderseq = null;
                    var foldername = null;
                    var expose=res['EXPOSE'];
                    if (res['FOLDER'] != null) {
                        folderseq = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    write = itemLoad(write, seq, name, date, knock, comment, preview, writer, folderseq, foldername, pic,targetseq,targetname,expose);
                    $('#topcon').append(write);
                } else {
                    var write = '';
                    var seq = res['SEQ'];
                    var writer = res['SEQ_WRITER'];
                    var name = res['USER_NAME'];
                    var date = res['WRITE_DATE'];
                    var title = res['TITLE'];
                    var knock = res['KNOCK'];
                    var price = res['PRICE'];
                    var comment = res['COMMENT'];
                    var bought = res['BOUGHT'];
                    var preview = res['PREVIEW'];
                    var pic = res['PIC'];
                    var folderseq = null;
                    var foldername = null;
                    var expose=res['EXPOSE']
                    if (res['FOLDER'] != null) {
                        folderseq = res['FOLDER'];
                        foldername = res['FOLDER_NAME'];
                    }
                    write = itemForSaleLoad(write, seq, name, date, title, knock, price, comment, bought, preview, writer, folderseq, foldername, pic,expose);
                    $('#topcon').append(write);
                }
            }
        }, error: function (request) {
            alert(request.responseText);
        }
    })
});