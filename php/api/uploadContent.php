<?php
header("Content-Type:application/json");
if (!empty($_POST)) {
    require_once '../../conf/database_conf.php';
    require_once '../../lib/passing_time.php';
    require_once '../../lib/blur.php';
    require_once '../../lib/HTMLPurifier.php';
    require_once'../../lib/imagecrop.php';
    require_once'../../lib/random_64.php';
    require_once'../../lib/getImgFromUrl.php';

    //이미지 소스만 가져오기
    $reg = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
    $br = "/(\<div\>\<br \/\>\<\/div\>){2,}/i";
    $a = "/class=\"gallery\"/i";
    $body = $_POST['body'];
    $body_text=$_POST['body_text'];
    $for_sale=$_POST['for_sale'];
    $body = $purifier->purify($body);
    $body_text=$purifier->purify($body_text);
    preg_match_all($reg, $body, $imgs, PREG_OFFSET_CAPTURE);//PREG_OFFSET_CAPTURE로 잡힌태그의 위치를 갖는다
    $body = preg_replace($br, "<div><br></div>", $body);//칸띄움 줄이기
    $body = preg_replace($a, "data-gallery", $body);    //class="gallery"를 data-gallery로 치환
    $imgcount=count($imgs[0]);
    $croprex="/^\\/img\\/crop\\//i";
    //원본이 서버에 없으면 서버에 저장하고 태그의 소스를 바꾸는작업
    for($i=0;$i<$imgcount;$i++) {
        if (!preg_match($croprex,$imgs[1][$i][0])){
            $originurl[$i]=$imgs[1][$i][0];
            $savedurl[$i]=getImgFromUrl($imgs[1][$i][0],'origin','crop',510);
            $imgs[1][$i][0]=$savedurl[$i];
            $imgs[0][$i][0]=str_replace($originurl[$i],$savedurl[$i],$imgs[0][$i][0]);
            $body=str_replace($originurl,$savedurl,$body);
        }
    }
    //링크로 덮는작업
    if (isset($imgs[0][0])) {
        for ($i = 0; $i < $imgcount; $i++) {
            $originSource = str_replace("crop", "origin", $imgs[1][$i][0]);
            $not_covered[$i] = $imgs[0][$i][0];
            $a_covered[$i] = "a href='" . $originSource . "' data-gallery>" . $imgs[0][$i][0] . "</a";
        }
        $body = preg_replace($not_covered, $a_covered, $body);
    }
    $previewimg = $imgs[1][0][0];
    //더보기가 있어야할지 검사
    $bodylen=mb_strlen($body,'utf-8');
    if(!$previewimg and $bodylen<=400){
        $more=0;
    }elseif($previewimg and !$imgs[1][1] and $bodylen<=200){
        if($for_sale){
            $more=1;
        }else{
            $more=0;
        }
    }else{
        $more=1;
    }

    $blured;//오타 아님 정의해야해서 하는
    for ($i = 1; $i < $imgcount; $i++) {
        //4는 블러강도. 3은평균 5가 가장 높은것.
        $blured[$i - 1] = blur($imgs[1][$i][0], 2, $ext);
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
        $preview = $previewtxt . "<br><img src='{$previewimg}' class='BodyPic'><br><br>";
    } else if ($previewimg) {
        $preview = "<img src='{$previewimg}' class='BodyPic'><br><br>";
    } else {
        $preview = $previewtxt;
    }
    if (count($blured) > 5) {
        for ($i = 0; $i < 4; $i++) {
            $preview = $preview . "<img src='{$blured[$i]}' class='thumbPic'>";
        }
        $ex = count($blured) - 4;
        $preview = $preview . "<p style='font-size=20;font-weight:700;'>&nbsp;외&nbsp;" . $ex . "장";
    } else {
        for ($i = 0; $i < count($blured); $i++) {
            $preview = $preview . "<img src='{$blured[$i]}' class='thumbPic'>";
        }
    }
    //사진 80으로 크롭시켜서 대표이미지로 등록
    if($previewimg and $for_sale){
        $imgsrc=__DIR__.'/../..'.str_replace('crop','origin',$imgs[1][0][0]);
        $imgout=str_replace('origin','crop80',$imgsrc);
        $img = new imaging;
        $img->set_img($imgsrc);
        $img->set_quality(100);
        $img->set_size(80, 80);
        $img->save_img($imgout);
    }
    //content테이블에 넣음
    try {
        $db->beginTransaction();
        $ID_writer = $_POST['ID_writer'];
        $targetID = $_POST['targetID'];
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
        $prepare->bindValue(':FOLDER', $_POST['folder'], PDO::PARAM_STR);
        $prepare->bindValue(':EXPOSE', $_POST['expose'], PDO::PARAM_STR);
        $prepare->bindValue(':MORE', $more, PDO::PARAM_STR);
        $prepare->bindValue(':BODY_TEXT', $body_text, PDO::PARAM_STR);
        $prepare->bindValue(':TAG', $_POST['tags'] ? implode(' ', json_decode($_POST['tags'])) : null, PDO::PARAM_STR);  //태그가 있으면 넣고 아니면 말고
        if ($targetID == '') {
            $prepare->bindValue(':ID_TARGET', NULL, PDO::PARAM_STR);
        } else {
            $prepare->bindValue(':ID_TARGET', $targetID, PDO::PARAM_STR);
        }

        if ($for_sale) {
            $prepare->bindValue(':PRICE', $_POST['price'], PDO::PARAM_STR);
            $prepare->bindValue(':CATEGORY', $_POST['category'], PDO::PARAM_STR);
            $prepare->bindValue(':SUB_CATEGORY', $_POST['sub_category'], PDO::PARAM_STR);
            $prepare->bindValue(':TITLE', $_POST['title'], PDO::PARAM_STR);
            $prepare->bindValue(':IMG', $imgout ? str_replace('crop', 'crop80', $imgs[1][0][0]) : '/img/alt_img.jpg', PDO::PARAM_STR);
            if ($_POST['adult'] == true) {
                $prepare->bindValue(':AGE', "Y", PDO::PARAM_STR);
            } else {
                $prepare->bindValue(':AGE', "N", PDO::PARAM_STR);
            }
            if ($_POST['ad'] == true) {
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
  FOLDER.DIR AS DIR,
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
        if ($_POST['tags']) {
            $tags = json_decode($_POST['tags']);
            $tagsql = "INSERT INTO publixher.TBL_TAGS(TAG,ID_CONTENT) VALUES(:TAG,:ID_CONTENT)";
            $tpr = $db->prepare($tagsql);
            $tpr->bindValue(':ID_CONTENT', $uid);
            for ($i = 0; $i < count($tags); $i++) {
                $tpr->bindValue(':TAG', $tags[$i]);
                $tpr->execute();
            }
        }
        if ($_POST['folder']) {
            //폴더에 내용 수 증가
            $sql3 = "UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM+1 WHERE ID=:ID";
            $prepare3 = $db->prepare($sql3);
            $prepare3->bindValue(':ID', $_POST['folder'], PDO::PARAM_STR);
            $prepare3->execute();
        }
        $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        $db->commit();
        echo $result;
    }catch(PDOException $e){
        $db->rollBack();
        echo "<script>alert('동작중 문제가 생겼습니다. message : $e->getMessage()')";
        exit;
    }
} else {
    exit;
}
?>