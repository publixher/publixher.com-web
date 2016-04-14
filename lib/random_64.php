<?php
function rand64($size=10){
    $a='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_';
    $return='';
    for($i=0;$i<$size;$i++){
        $return.=$a[mt_rand(0,63)];
    }
    return $return;
}
?>