/**
 * Created by donggyun on 2016. 6. 27..
 */
function errorReport(action,data,status,error){
    $.ajax({
        url:'/php/api/errorReport.php',
        dataType:'json',
        method:'POST',
        data:{"action":action,"sending_data":data,"status":status,"error":error}
    })
}