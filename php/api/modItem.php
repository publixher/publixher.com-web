<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
$action = $_REQUEST['action'] ? $_REQUEST['action'] : $_REQUEST['action'];
$itemID = $_REQUEST['itemID'] ? $_REQUEST['itemID'] : $_REQUEST['itemID'];
if ($action == 'get_item') {
    $gs = "SELECT TITLE,EXPOSE,CATEGORY,SUB_CATEGORY,PRICE,AGE,AD,BODY,FOLDER,TAG FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $pr = $db->prepare($gs);
    $pr->bindValue(':ID', $itemID);
    $pr->execute();
    $data = $pr->fetch(PDO::FETCH_ASSOC);
    if ($data['FOLDER']) {
        $s = "SELECT DIR FROM publixher.TBL_FOLDER WHERE ID=:ID";
        $pr = $db->prepare($s);
        $pr->bindValue(':ID', $data['FOLDER']);
        $pr->execute();
        $data['DIR'] = $pr->fetchColumn();
    }
    echo json_encode(array('status'=>1,'result'=>$data), JSON_UNESCAPED_UNICODE);
} elseif ($action == 'mod_item') {
    require_once '../../lib/passing_time.php';
    require_once '../../lib/blur.php';
    require_once '../../lib/HTMLPurifier.php';
    require_once '../../lib/iFrameCrop.php';
    require_once '../../lib/getImgFromUrl.php';
    require_once '../../lib/imagecrop.php';

    //이미지 소스만 가져오기
    $reg = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
    $br = "/(\<div\>\<br \/\>\<\/div\>){2,}/i";
    $a = "/class=\"gallery\"/i";
    $body = $_REQUEST['body'];
    $body_text = $_REQUEST['body_text'];
    $for_sale=$_REQUEST['for_sale'];
    $body = $purifier->purify($body);
    $body_text = $purifier->purify($body_text);

    $body=iframe_crop($body);
    preg_match_all($reg, $body, $imgs, PREG_OFFSET_CAPTURE);//PREG_OFFSET_CAPTURE로 동영상의 길이를 통해 최대 크기를 치환
    $body = preg_replace($br, "<div><br></div>", $body);//칸띄움 줄이기
    $body = preg_replace($a, "data-gallery", $body);    //class="gallery"를 data-gallery로 치환
    $imgcount = count($imgs[0]);
    $croprex = "/^\\/img\\/crop_origin\\//i";
    //원본이 서버에 없으면 서버에 저장하고 태그의 소스를 바꾸는작업
    for ($i = 0; $i < $imgcount; $i++) {
        str_replace('http://analograph.com','',$imgs[1][$i][0]);
        if (!preg_match($croprex, $imgs[1][$i][0])) {
            $originurl[$i] = $imgs[1][$i][0];
            $savedurl[$i] = getImgFromUrl($imgs[1][$i][0], 'origin', 'crop', 528,null,null,'crop_origin');
            $imgs[1][$i][0] = $savedurl[$i];
            $imgs[0][$i][0] = str_replace($originurl[$i], $savedurl[$i], $imgs[0][$i][0]);
            $body = str_replace($originurl, $savedurl, $body);
        }
    }
    //gif인지 검사
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
    $bodylen = mb_strlen($body, 'utf-8');
    if (!$previewimg and $bodylen <= 400) {
        $more = 1;
    } elseif ($previewimg and !$imgs[1][1] and $bodylen <= 200) {
        if ($for_sale) {
            $more = 1;
        } else {
            $more = 1;
        }
    } else {
        $more = 1;
    }

    $blured;//오타 아님 정의해야해서 하는
    for ($i = 1; $i < min(5,$imgcount); $i++) {
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
        $preview.="'></div><br>".$previewtxt."<br>";
    } else if ($previewimg) {
        $preview ="<div class='pre-body-pic'><img src='{$previewimg}' class='BodyPic";
        if($gif) $preview.=" gif";
        $preview.="'></div><br><br>";
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
    $ID_writer = $_REQUEST['userID'];
    $targetID = $_REQUEST['targetID'];
    $id=$_REQUEST['ID'];

    $q="SELECT ORIGINAL,CHANGED,BODY,FOLDER FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $p=$db->prepare($q);
    $p->bindValue(':ID',$id);
    $p->execute();
    $origin=$p->fetch(PDO::FETCH_ASSOC);
    $originData=$origin['CHANGED']==1?$origin['ORIGINAL']:$origin['BODY'];

    if($origin['FOLDER']!=null) {
        //원래 폴더에서 수 감소
        $fs = "UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM-1 WHERE ID=:ID";
        $fp = $db->prepare($fs);
        $fp->bindValue(':ID', $origin['FOLDER'], PDO::PARAM_STR);
        $fp->execute();
    }

    if (!$_REQUEST['for_sale']) {
        $sql = "UPDATE publixher.TBL_CONTENT
SET BODY    = :BODY, FOLDER = :FOLDER, EXPOSE = :EXPOSE, CHANGED = 1, PREVIEW = :PREVIEW, ORIGINAL = :ORIGINAL,
  TAG       = :TAG, BODY_TEXT = :BODY_TEXT,MORE=:MORE
WHERE ID = :ID";
    } else {
        $sql = "UPDATE publixher.TBL_CONTENT
SET BODY    = :BODY, FOLDER = :FOLDER, EXPOSE = :EXPOSE, CHANGED = 1, PREVIEW = :PREVIEW, ORIGINAL = :ORIGINAL,
  PRICE     = :PRICE, CATEGORY = :CATEGORY, SUB_CATEGORY = :SUB_CATEGORY, TITLE = :TITLE, AGE = :AGE, AD = :AD,
  TAG       = :TAG, BODY_TEXT = :BODY_TEXT,MORE=:MORE,IMG=:IMG
WHERE ID = :ID";
    }
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $id);
    $prepare->bindValue(':BODY', $body, PDO::PARAM_STR);
    $prepare->bindValue(':PREVIEW', $preview, PDO::PARAM_STR);
    $prepare->bindValue(':FOLDER', $_REQUEST['folder'], PDO::PARAM_STR);
    $prepare->bindValue(':EXPOSE', $_REQUEST['expose'], PDO::PARAM_STR);
    $prepare->bindValue(':ORIGINAL', $originData, PDO::PARAM_STR);
    $prepare->bindValue(':BODY_TEXT', $body_text, PDO::PARAM_STR);
    $prepare->bindValue(':TAG', $_REQUEST['tag'] ? implode(' ', json_decode($_REQUEST['tag'])) : null, PDO::PARAM_STR);
    $prepare->bindValue(':MORE',$more);

    if ($_REQUEST['for_sale']) {
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
WHERE (DEL = 'N' AND CONT.ID = :ID AND REPORT < 10)";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $id, PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetch(PDO::FETCH_ASSOC);

    if ($_REQUEST['folder']) {
        //폴더에 내용 수 증가
        $sql3 = "UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM+1 WHERE ID=:ID";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':ID', $_REQUEST['folder'], PDO::PARAM_STR);
        $prepare3->execute();
        //폴더 이름 받아오기
        $sql4 = "SELECT DIR FROM publixher.TBL_FOLDER WHERE ID=:ID";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':ID', $_REQUEST['folder'], PDO::PARAM_STR);
        $prepare4->execute();
        $result = array_merge($result, $prepare4->fetch(PDO::FETCH_ASSOC));
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

    //핀관리
    $sql5="UPDATE publixher.TBL_PIN_LIST SET MODIFIED=1,LAST_UPDATE=NOW() WHERE ID_CONTENT=:ID_CONTENT";
    $prepare5=$db->prepare($sql5);
    $prepare5->bindValue(':ID_CONTENT',$id);
    $prepare5->execute();

    $result = json_encode(array("status"=>1,'result'=>$result), JSON_UNESCAPED_UNICODE);
    echo $result;
}
?>