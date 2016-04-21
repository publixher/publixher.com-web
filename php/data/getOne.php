<?php
header("Content-Type:application/json");
require_once'../../lib/passing_time.php';
require_once'../../conf/database_conf.php';
$sqltop = "SELECT TOP_CONTENT FROM publixher.TBL_USER WHERE ID=:ID";
$preparetop = $db->prepare($sqltop);
$preparetop->bindValue(':ID', $_GET['profile'], PDO::PARAM_STR);
$preparetop->execute();
$result = $preparetop->fetch(PDO::FETCH_ASSOC);
if ($result) {
    $sqlcon = "SELECT ID,ID_WRITER,TITLE,EXPOSE,KNOCK,WRITE_DATE,MODIFY_DATE,FOR_SALE,CATEGORY,SUB_CATEGORY,PRICE,PREVIEW,COMMENT,SALE,FOLDER,CHANGED,MORE,TAG FROM publixher.TBL_CONTENT WHERE (DEL='N' AND ID=:ID)";
    $preparecon = $db->prepare($sqlcon);
    $preparecon->bindValue(':ID', $result['TOP_CONTENT'], PDO::PARAM_STR);
    $preparecon->execute();
    $topcon = $preparecon->fetch(PDO::FETCH_ASSOC);
    if ($topcon) {
        //탑컨텐츠가 있기도 하고 그게 지워진거도 아닐때
        $sql = "SELECT USER_NAME,REPLACE(PIC,'profile','crop50') AS PIC FROM publixher.TBL_USER WHERE ID=:ID";
        $key = 'USER_NAME';
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID', $topcon['ID_WRITER'], PDO::PARAM_STR);
        $prepare->execute();
        $fetch = $prepare->fetch(PDO::FETCH_ASSOC);
        $val = $fetch['USER_NAME'];
        $topcon[${key}] = $val;
        $topcon['WRITE_DATE'] = passing_time($topcon['WRITE_DATE']);
        $topcon['PIC']=$fetch['PIC'];

        //폴더이름 가져오기
        if ($topcon['FOLDER']) {
            $fsql = "SELECT DIR FROM publixher.TBL_FOLDER WHERE ID=:ID";
            $fprepare = $db->prepare($fsql);
            $fprepare->bindValue(':ID', $topcon['FOLDER'], PDO::PARAM_INT);
            $fprepare->execute();
            $foldername = $fprepare->fetch(PDO::FETCH_ASSOC);
            $topcon['FOLDER_NAME'] = $foldername['DIR'];
        }
        //유료일경우 구매한건지 안구매한건지 확인하는것
        if ($topcon['FOR_SALE'] == 'Y') {
            if ($topcon['ID_WRITER'] == $userID) {
                $topcon['BOUGHT'] = $topcon['WRITE_DATE'];
            } else {
                $sql = "SELECT BUY_DATE FROM publixher.TBL_BUY_LIST WHERE ID_USER=:ID_USER AND ID_CONTENT=:ID_CONTENT";
                $prepare = $db->prepare($sql);
                $prepare->bindValue(':ID_USER', $userID, PDO::PARAM_STR);
                $prepare->bindValue(':ID_CONTENT', $topcon['ID'], PDO::PARAM_STR);
                $prepare->execute();
                $bought = $prepare->fetch(PDO::FETCH_ASSOC);
                $topcon['BOUGHT'] = $bought['BUY_DATE'];
            }
        }
        echo json_encode($topcon,JSON_UNESCAPED_UNICODE);
    }else{
        echo '{"result":"N","reason":"deleted"}';
    }
}else{
    echo '{"result":"N","reason":"no top"}';
}
?>