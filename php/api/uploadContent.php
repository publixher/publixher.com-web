<?php
header("Content-Type:application/json");
if (!empty($_REQUEST)) {
    require_once '../../conf/database_conf.php';
    require_once '../../lib/passing_time.php';
    require_once '../../lib/blur.php';
    require_once '../../lib/HTMLPurifier.php';
    require_once'../../lib/imagecrop.php';
    require_once'../../lib/random_64.php';
    require_once'../../lib/getImgFromUrl.php';
    require_once'../../lib/banchk.php';
    require_once '../../lib/iFrameCrop.php';
    banCheck($_REQUEST['ID_writer'],$db,-2 );

    //이미지 소스만 가져오기
    $reg = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
    $br = "/(\<div\>\<br \/\>\<\/div\>){2,}/i";
    $a = "/class=\"gallery\"/i";
    $body = $_REQUEST['body'];
    $body_text=$_REQUEST['body_text'];
    $for_sale=$_REQUEST['for_sale'];
    $body = $purifier->purify($body);
    $body_text=$purifier->purify($body_text);
    $body=iframe_crop($body);
    preg_match_all($reg, $body, $imgs, PREG_OFFSET_CAPTURE);//PREG_OFFSET_CAPTURE로 잡힌태그의 위치를 갖는다
    $body = preg_replace($br, "<div><br></div>", $body);//칸띄움 줄이기
    $body = preg_replace($a, "data-gallery", $body);    //class="gallery"를 data-gallery로 치환
    $imgcount=count($imgs[0]);
    //TODO: croprex에서 도메인에따라 바뀌어야함
    $croprex="/^\\/img\\/crop_origin\\//i";
    //원본이 서버에 없으면 서버에 저장하고 태그의 소스를 바꾸는작업
    for($i=0;$i<$imgcount;$i++) {
        str_replace('http://analograph.com','',$imgs[1][$i][0]);
        if (!preg_match($croprex,$imgs[1][$i][0])){
            $originurl[$i]=$imgs[1][$i][0];
            $savedurl[$i] = getImgFromUrl($imgs[1][$i][0], 'origin', 'crop', 528,null,null,'crop_origin');
            $imgs[1][$i][0]=$savedurl[$i];
            $imgs[0][$i][0]=str_replace($originurl[$i],$savedurl[$i],$imgs[0][$i][0]);
            $body=str_replace($originurl,$savedurl,$body);
        }
    }
    $gif=strpos($imgs[0][0][0],'class="gif"');
    //링크로 덮는작업
    if (isset($imgs[0][0])) {
        for ($i = 0; $i < $imgcount; $i++) {
            $path=$imgs[1][$i][0];
            if(strpos($imgs[0][$i][0],'class="gif"'))$path=str_replace('.png','.gif',$path);
            $originSource = str_replace("crop_origin", "origin",$path);
            $not_covered[$i] = $imgs[0][$i][0];
            $a_covered[$i] = "a href='" . $originSource . "' data-gallery>" . $imgs[0][$i][0] . "</a";
        }
        $body = preg_replace($not_covered, $a_covered, $body);
    }
    $previewimg = str_replace('crop_origin','crop',$imgs[1][0][0]);

    //더보기가 있어야할지 검사
    $bodylen=mb_strlen($body,'utf-8');
    if(!$previewimg and $bodylen<=400){
        $more=1;
    }elseif($previewimg and !$imgs[1][1] and $bodylen<=200){
        if($for_sale){
            $more=1;
        }else{
            $more=1;
        }
    }else{
        $more=1;
    }

    $blured;//오타 아님 정의해야해서 하는
    for ($i = 1; $i < min($imgcount,5); $i++) {
        //4는 블러강도. 3은평균 5가 가장 높은것.
        $imgsrc=__DIR__.'/../..'.str_replace('crop_origin','origin',$imgs[1][$i][0]);
        $imgout=str_replace('origin','blur',$imgsrc);
        $img = new imaging;
        $img->set_img($imgsrc);
        $img->set_quality(100);
        $img->set_origin(true);
        $img->set_size(100, 100);
        $img->save_img($imgout);
        $blured[$i - 1] = blur($imgout, 2);
    }
    //이미지 있으면 프리뷰 길이가 150 없으면 400
    if ($previewimg) {
        $previewlength = 150;
    } else {
        $previewlength = 400;
    }
    $beforepic = substr($body, 0, $imgs[0][0][1]); //사진이 있기 전까지의 텍스트 프리뷰에 나오는거
    $beforepic = strip_tags($beforepic);
    $previewtxt = strip_tags($body);
    if ($beforepic) {
        $previewtxt = min(mb_substr($previewtxt, 0, $previewlength, 'UTF-8'), $beforepic);
    } else {
        $previewtxt = mb_substr($previewtxt, 0, $previewlength);
    }
    if (strlen($previewtxt) > 0 && $previewimg) {
        $preview =  "<div class='pre-body-pic'><img src='{$previewimg}' class='BodyPic";
        if($gif) $preview.=" gif";
        $preview.="'></div><p>".$previewtxt."</p>";
    } else if ($previewimg) {
        $preview = "<div class='pre-body-pic'><img src='{$previewimg}' class='BodyPic";
        if($gif) $preview.=" gif";
        $preview.="'></div>";
    } else {
        $preview = $previewtxt;
    }
    if ($imgcount > 5) {
        for ($i = 0; $i < 4; $i++) {
            $preview = $preview . "<div class='thumbPic-wrap'><img src='{$blured[$i]}' class='thumbPic'></div>";
        }
        $ex = $imgcount - 4;
        $preview = $preview . "<p style='font-size=20;font-weight:700;' class='oi'>&nbsp;외&nbsp;" . $ex . "장";
    } else {
        for ($i = 0; $i < count($blured); $i++) {
            $preview = $preview . "<div class='thumbPic-wrap'><img src='{$blured[$i]}' class='thumbPic'></div>";
        }
    }
    //사진 80으로 크롭시켜서 대표이미지로 등록
    if($previewimg and $for_sale){
        $imgsrc=__DIR__.'/../..'.str_replace('crop_origin','origin',$imgs[1][0][0]);
        $imgout=str_replace('origin','crop80',$imgsrc);
        $img = new imaging;
        $img->set_img($imgsrc);
        $img->set_quality(100);
        $img->set_origin(true);
        $img->set_size(162, 162);
        $img->save_img($imgout);
    }
    //content테이블에 넣음
    try {
        $db->beginTransaction();
        $ID_writer = $_REQUEST['ID_writer'];
        $targetID = $_REQUEST['targetID'];
        if (!$for_sale) {
            $sql = "INSERT INTO publixher.TBL_CONTENT (ID,ID_WRITER,BODY,PREVIEW,FOLDER,EXPOSE,ID_TARGET,MORE,TAG,BODY_TEXT) VALUES (:ID,:ID_WRITER,:BODY,:PREVIEW,:FOLDER,:EXPOSE,:ID_TARGET,:MORE,:TAG,:BODY_TEXT)";
        } else {
            $sql = "INSERT INTO publixher.TBL_CONTENT (ID,ID_WRITER,BODY,FOR_SALE,PRICE,CATEGORY,SUB_CATEGORY,AGE,AD,TITLE,PREVIEW,FOLDER,EXPOSE,ID_TARGET,MORE,IMG,TAG,BODY_TEXT) VALUES (:ID,:ID_WRITER,:BODY,'Y',:PRICE,:CATEGORY,:SUB_CATEGORY,:AGE,:AD,:TITLE,:PREVIEW,:FOLDER,:EXPOSE,:ID_TARGET,:MORE,:IMG,:TAG,:BODY_TEXT);";
        }
        $uid = uniqueid($db, 'content');
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID', $uid, PDO::PARAM_STR);
        $prepare->bindValue(':ID_WRITER', $ID_writer, PDO::PARAM_STR);
        $prepare->bindValue(':BODY', $body, PDO::PARAM_STR);
        $prepare->bindValue(':PREVIEW', $preview, PDO::PARAM_STR);
        $prepare->bindValue(':FOLDER', $_REQUEST['folder'], PDO::PARAM_STR);
        $prepare->bindValue(':EXPOSE', $_REQUEST['expose'], PDO::PARAM_STR);
        $prepare->bindValue(':MORE', $more, PDO::PARAM_STR);
        $prepare->bindValue(':BODY_TEXT', $body_text, PDO::PARAM_STR);
        $prepare->bindValue(':TAG', $_REQUEST['tags'] ? implode(' ', json_decode($_REQUEST['tags'])) : null, PDO::PARAM_STR);  //태그가 있으면 넣고 아니면 말고
        if ($targetID == '') {
            $prepare->bindValue(':ID_TARGET', NULL, PDO::PARAM_STR);
        } else {
            $prepare->bindValue(':ID_TARGET', $targetID, PDO::PARAM_STR);
        }

        if ($for_sale) {
            $prepare->bindValue(':PRICE', $_REQUEST['price'], PDO::PARAM_STR);
            $prepare->bindValue(':CATEGORY', $_REQUEST['category'], PDO::PARAM_STR);
            $prepare->bindValue(':SUB_CATEGORY', $_REQUEST['sub_category']?$_REQUEST['sub_category']:null, PDO::PARAM_STR);
            $prepare->bindValue(':TITLE', $_REQUEST['title'], PDO::PARAM_STR);
            $prepare->bindValue(':IMG', $previewimg?str_replace('crop','crop80',$previewimg) : null, PDO::PARAM_STR);
            if ($_REQUEST['adult'] == true) {
                $prepare->bindValue(':AGE', "Y", PDO::PARAM_STR);
            } else {
                $prepare->bindValue(':AGE', "N", PDO::PARAM_STR);
            }
            if ($_REQUEST['ad'] == true) {
                $prepare->bindValue(':AD', "Y", PDO::PARAM_STR);
            } else {
                $prepare->bindValue(':AD', "N", PDO::PARAM_STR);
            }
        }
        $prepare->execute();
        //유료컨텐츠 업로드가 성공하면 팔로우테이블에 LAST_UPDATE도 수정함
        if ($for_sale) {
            $folsql = "UPDATE publixher.TBL_FOLLOW SET LAST_UPDATE=NOW() WHERE ID_MASTER=:ID_MASTER";
            $folprepare = $db->prepare($folsql);
            $folprepare->bindValue(':ID_MASTER', $ID_writer);
            $folprepare->execute();
        }
        //id로 컨텐츠 테이블의 내용도 가져옴
        //id로 컨텐츠 테이블의 내용도 가져옴
        $sql = "SELECT
  CONT.ID,
  CONT.ID_WRITER,
  CONT.TITLE,
  CONT.EXPOSE,
  CONT.KNOCK,
  CONT.WRITE_DATE,
  CONT.MODIFY_DATE,
  CONT.FOR_SALE,
  CONT.CATEGORY,
  CONT.SUB_CATEGORY,
  CONT.PRICE,
  CONT.PREVIEW,
  CONT.COMMENT,
  CONT.SALE,
  CONT.FOLDER,
  CONT.CHANGED,
  CONT.MORE,
  CONT.TAG,
  USER.USER_NAME,
  REPLACE(USER.PIC,'profile','crop50') AS PIC,
  FOLDER.DIR AS FOLDER_NAME,
  USER2.USER_NAME AS TARGET_NAME,
  USER2.ID AS TARGET_ID
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
  LEFT JOIN publixher.TBL_USER AS USER2
  ON USER2.ID=CONT.ID_TARGET
  WHERE CONT.ID=:ID_CONT";
        $prepare = $db->prepare($sql);
        $prepare->bindValue(':ID_CONT', $uid, PDO::PARAM_STR);
        $prepare->execute();
        $result = $prepare->fetch(PDO::FETCH_ASSOC);
        //판매목록에 수 증가
        if ($for_sale) {
            $sql2 = "INSERT INTO publixher.TBL_SELL_LIST(ID_USER,ID_CONTENT) VALUES(:ID_USER,:ID_CONTENT);";
            $prepare2 = $db->prepare($sql2);
            $prepare2->bindValue(':ID_USER', $ID_writer, PDO::PARAM_STR);
            $prepare2->bindValue(':ID_CONTENT', $uid, PDO::PARAM_STR);
            $prepare2->execute();
        }
        //태그 넣기
        if ($_REQUEST['tags']) {
            $tags = json_decode($_REQUEST['tags']);
            $tagsql = "INSERT INTO publixher.TBL_TAGS(TAG,ID_CONTENT) VALUES(:TAG,:ID_CONTENT)";
            $tpr = $db->prepare($tagsql);
            $tpr->bindValue(':ID_CONTENT', $uid);
            for ($i = 0; $i < count($tags); $i++) {
                $tpr->bindValue(':TAG', $tags[$i]);
                $tpr->execute();
            }
        }
        if ($_REQUEST['folder']) {
            //폴더에 내용 수 증가
            $sql3 = "UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM+1 WHERE ID=:ID";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue(':ID', $_REQUEST['folder'], PDO::PARAM_STR);
            $prepare3->execute();
        }
        $bulk=new MongoDB\Driver\BulkWrite;
        $bulk->insert(['id'=>$uid, 'interested_users' => []]);
        $mongomanager->executeBulkWrite('publixher.contents',$bulk);
        $db->commit();
        if(!$result) {
            echo json_encode(array('status' => array('code' => 0)), JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(array('result'=>$result,'status'=>array('code'=>1)), JSON_UNESCAPED_UNICODE);
    }catch(PDOException $e){
        $db->rollBack();
        echo '{"status":-1}';
        exit;
    }
} else {
    exit;
}
?>