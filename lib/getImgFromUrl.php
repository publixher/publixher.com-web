<?php
function getImgFromUrl($url,$originpath,$path,$size,$opath=null,$osize=null,$sel='crop',$o2path=null,$o2size=null){
    require_once 'imagecrop.php';
    //이미지가 서버에 없으면 경로에서 이미지 따와서 서버에 저장하는것
    $tmp_file = explode(' ', microtime());
    $date = substr($tmp_file[0], 2, 6);
    $file_hash = $date.$url;
    $file_hash = md5($file_hash);
    $ext=explode('?',pathinfo($url, PATHINFO_EXTENSION))[0];
    $fileurl = "/img/".$originpath."/${file_hash}.".$ext ;
    $cropurl = "/img/".$path."/${file_hash}.".$ext;
    $croporiginurl=str_replace('crop','crop_origin',$cropurl);
    $filepath=__DIR__.'/..'.$fileurl;
    $croppath=str_replace($originpath,$path,$filepath);
    $croporiginpath=str_replace($originpath,'crop_origin',$filepath);
    copy($url,$filepath);
    $img = new imaging;
    $img->set_img($filepath);
    $img->set_quality(100);
    $img->set_size($size, $size);
    $img->save_img($croppath);
    $img->set_origin(true);
    $img->save_img($croporiginpath);
    if($opath!==null){
        $ocroppath=str_replace($originpath,$opath,$filepath);
        $img->set_size($osize,$osize);
        $img->save_img($ocroppath);
    }
    if($o2path!==null){
        $o2croppath=str_replace($originpath,$o2path,$filepath);
        $img->set_size($o2size,$o2size);
        $img->save_img($o2croppath);
    }
    if($sel=='origin') return $fileurl;
    elseif($sel=='crop_origin') return $croporiginurl;
    else return $cropurl;
}
?>