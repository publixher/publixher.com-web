<?php
function rand64($size=10){
    $a='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
    $return='';
    for($i=0;$i<$size;$i++){
        $return.=$a[mt_rand(0,62)];
    }
    return $return;
}
function uniqueid($db,$obj){
    switch($obj){
        case 'user':$table='USER';break;
        case 'content':$table='CONTENT';break;
        case 'reply':$table='CONTENT_REPLY';break;
        case 'sub_reply':$table='CONTENT_SUB_REPLY';break;
        case 'folder':$table='FOLDER';break;
    }
    $sql="SELECT ID FROM publixher.TBL_${table} WHERE ID=:ID";
    $p=$db->prepare($sql);
    do{
        $id=rand64();
        $p->bindValue(':ID', $id);
        $p->execute();
        $exist=$p->fetchColumn();
    }while($exist);
    return $id;
}
?>