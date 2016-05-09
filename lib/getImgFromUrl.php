<?php
function getImgFromUrl($url,$originpath,$path,$size,$opath=null,$osize=null,$sel='crop'){
    require_once 'imagecrop.php';
    //이미지가 서버에 없으면 경로에서 이미지 따와서 서버에 저장하는것
    $tmp_file = explode(' ', microtime());
    $date = substr($tmp_file[0], 2, 6);
    $file_hash = $date.$url;
    $file_hash = md5($file_hash);
    $ext=pathinfo($url, PATHINFO_EXTENSION);
    $fileurl = "/img/".$originpath."/${file_hash}.".$ext ;
    $cropurl = "/img/".$path."/${file_hash}.".$ext;
    $filepath=__DIR__.'/..'.$fileurl;
    $croppath=str_replace($originpath,$path,$filepath);
    copy($url,$filepath);
    $img = new imaging;
    $img->set_img($filepath);
    $img->set_quality(100);
    $img->set_size($size, $size);
    $img->save_img($croppath);
    if($opath){
        $ocroppath=str_replace($originpath,$opath,$filepath);
        $img->set_size($osize,$osize);
        $img->save_img($ocroppath);
    }
    if($sel=='origin') return $fileurl;
    else return $cropurl;
}
?>