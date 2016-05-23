<?php
header("Content-Type:text/json");
if(! isset($_SERVER['HTTP_X_REQUESTED_WITH']) OR $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
    exit('부정한 호출입니다.');
}
if ($_FILES) {
    $file = $_FILES['fileuploads'] ? $_FILES['fileuploads'] : $_FILES['fileuploadp'];
    //파일이 이미지가 맞는지 검증
    if (getimagesize($file['tmp_name'][0])) {
        require_once'../../conf/database_conf.php';
        require_once'../../lib/imagecrop.php';
//php.ini의 설정의 file_uploads=On을 확인,
//post_max_size와 upload_max_file_size확인
        $uploadDir = __DIR__ . '/../../img/';
        $originDir = $uploadDir . 'origin/';
        $tmp_name = $file['tmp_name'];
        $name = $file['name'][0];
        $ext = substr(strrchr($name, "."), 1);    //확장자앞 .을 제거하기 위하여 substr()함수를 이용
        $ext = strtolower($ext);
        $EXT = strtoupper($ext);
        //확장자가 jpg,gif,png가 아닐경우 뻗는다
        if (!($EXT == 'JPG' or $EXT == 'JPEG' or $EXT == 'PNG' or $EXT == 'GIF')) {
            echo '<script>alert("jpg,jpeg,gif,png파일만 허용됩니다.")</script>';
            exit;
        }
//시간값으로 해시를 만든다
        $tmp_file = explode(' ', microtime());
        $date = substr($tmp_file[0], 2, 6);
        $file_hash = $date . $name;
        $file_hash = md5($file_hash);
        $filepath = "$file_hash" . "." . $ext;
        $move = move_uploaded_file($file['tmp_name'][0], $originDir . $filepath);

        //요청이 들어온 페이지에 따라 동작이 달라진다
        $referer = $_SERVER['HTTP_REFERER'];
        //크롭시키는 함수
        $src = $originDir . $filepath;
        $img = new imaging;
        $img->set_img($src);
        $img->set_quality(100);
        if (strpos($referer, 'profileConfig')) {
            //요청이 들어온 페이지가 프로필 수정페이지면 160으로 크롭하고 유저 테이블에 저장도 하세기!
            $img->set_order(1);
            $img->set_size(160, 160);
            $img->save_img($uploadDir . "profile/" . $filepath);
            //34짜리와 50짜리도 크롭 하세기!
            $img->set_size(34,34);
            $img->save_img($uploadDir."crop34/".$filepath);
            $img->set_size(50,50);
            $img->save_img($uploadDir."crop50/".$filepath);
            $img->set_size(24,24);
            $img->save_img($uploadDir."crop24/".$filepath);
            include_once '../../conf/User.php';
            session_start();
            $userinfo = $_SESSION['user'];
            $userID = $userinfo->getID();
            $sqlup = "UPDATE publixher.TBL_USER SET PIC=:PIC WHERE ID=:ID;";
            $prepareup = $db->prepare($sqlup);
            $prepareup->bindValue(':PIC', '/img/profile/' . $filepath, PDO::PARAM_STR);
            $prepareup->bindValue(':ID', $userID, PDO::PARAM_STR);
            $prepareup->execute();
            $getuser="SELECT * from publixher.TBL_USER WHERE ID=:ID";
            $w=$db->prepare($getuser);
            $w->bindValue(':ID',$userID);
            $w->execute();
            $user=$w->fetchObject(User);
            $_SESSION['user'] = $user;
        }else {
            $img->set_size(510, 510);
            $img->save_img($uploadDir . "crop/" . $filepath);
            $img->set_origin(true);
            $img->set_size(510);
            $img->save_img($uploadDir . "crop_origin/" . $filepath);
            //gif면 jpg를 리턴함
            $filepath=str_replace('.gif','.png',$filepath);
        }
        $out_height = $img->get_out_height();
        $out_width = $img->get_out_width();
        $img->clear_cache();
        //db에 입력시키는 과정
        $sql = "INSERT INTO publixher.TBL_CONTENT_IMG_LIST(FILE_NAME,FILE_HASH) VALUES(:FILE_NAME,:FILE_HASH)";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':FILE_NAME', $name, PDO::PARAM_STR);
        $prepare->bindValue(':FILE_HASH', $filepath, PDO::PARAM_STR);

        $prepare->execute();

        if (strpos($referer, 'profileConfig')) {
            $result = array('files' => array('file_name' => $name, 'file_profile' => "profile/" . $filepath, 'file_origin' => 'origin/' . $filepath, 'file_height' => $out_height, 'file_width' => $out_width));
        } else {
            $result = array('files' => array('file_name' => $name, 'file_crop' => "crop_origin/" . $filepath, 'file_origin' => 'origin/' . $filepath, 'file_height' => $out_height, 'file_width' => $out_width));
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } else echo "<script>alert('이미지파일을 업로드 해 주세요.')</script>";
} else exit;
?>