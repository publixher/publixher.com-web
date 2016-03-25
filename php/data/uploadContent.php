<?php
header("Content-Type:application/json");
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) OR $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    exit('부정한 호출입니다.');
}
if (!empty($_POST)) {
    require_once '../../conf/database_conf.php';
    require_once '../../lib/passing_time.php';
    require_once '../../lib/blur.php';
    require_once '../../lib/HTMLPurifier.php';
//토큰검사
    session_start();
    //토큰검사
    if (!isset($_POST['token'])) {
        exit('부정한 조작이 감지되었습니다.');
    } elseif ($_POST['token'] != $_SESSION['token']) {
        exit('부정한 조작이 감지되었습니다.');
    }
    //브라우저 검사
    if (!isset($_POST['age'])) {
        exit('부정한 조작이 감지되었습니다.');
    } elseif ($_POST['age'] != $_SESSION['age']) {
        exit('부정한 조작이 감지되었습니다.');
    }
    //이미지 소스만 가져오기
    $reg = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
    $br = "/(\<div\>\<br \/\>\<\/div\>){2,}/i";
    $a = "/class=\"gallery\"/i";
    $body = $_POST['body'];
    $body = $purifier->purify($body);
    preg_match_all($reg, $body, $imgs, PREG_OFFSET_CAPTURE);//PREG_OFFSET_CAPTURE로 잡힌태그의 위치를 갖는다
    $body = preg_replace($br, "<div><br></div>", $body);//칸띄움 줄이기
    $body = preg_replace($a, "data-gallery", $body);    //class="gallery"를 data-gallery로 치환
    if (isset($imgs[0][0])) {
        for ($i = 0; $i < count($imgs[0]); $i++) {
            $originSource = str_replace("crop", "origin", $imgs[1][$i][0]);
            $not_covered[$i] = $imgs[0][$i][0];
            $a_covered[$i] = "a href='" . $originSource . "' data-gallery>" . $imgs[0][$i][0] . "</a";
        }
        $body = preg_replace($not_covered, $a_covered, $body);
    }
    $previewimg = $imgs[1][0][0];
    $blured;//오타 아님 정의해야해서 하는
    for ($i = 1; $i < count($imgs[1]); $i++) {
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
    //content테이블에 넣음
    $seq_writer = $_POST['seq_writer'];
    $targetseq=$_POST['targetseq'];
    if (!$_POST['for_sale']) {
        $sql = "INSERT INTO publixher.TBL_CONTENT (SEQ_WRITER,BODY,PREVIEW,FOLDER,EXPOSE,SEQ_TARGET) VALUES (:SEQ_WRITER,:BODY,:PREVIEW,:FOLDER,:EXPOSE,:SEQ_TARGET)";
    } else {
        $sql = "INSERT INTO publixher.TBL_CONTENT (SEQ_WRITER,BODY,FOR_SALE,PRICE,CATEGORY,SUB_CATEGORY,AGE,AD,TITLE,PREVIEW,FOLDER,EXPOSE) VALUES (:SEQ_WRITER,:BODY,'Y',:PRICE,:CATEGORY,:SUB_CATEGORY,:AGE,:AD,:TITLE,:PREVIEW,:FOLDER,:EXPOSE)";
    }
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ_WRITER', $seq_writer, PDO::PARAM_STR);
    $prepare->bindValue(':BODY', $body, PDO::PARAM_STR);
    $prepare->bindValue(':PREVIEW', $preview, PDO::PARAM_STR);
    $prepare->bindValue(':FOLDER', $_POST['folder'], PDO::PARAM_STR);
    $prepare->bindValue(':EXPOSE', $_POST['expose'], PDO::PARAM_STR);
    $prepare->bindValue(':SEQ_TARGET', $targetseq, PDO::PARAM_STR);

    if ($_POST['for_sale']) {
        $prepare->bindValue(':PRICE', $_POST['price'], PDO::PARAM_STR);
        $prepare->bindValue(':CATEGORY', $_POST['category'], PDO::PARAM_STR);
        $prepare->bindValue(':SUB_CATEGORY', $_POST['sub_category'], PDO::PARAM_STR);
        $prepare->bindValue(':TITLE', $_POST['title'], PDO::PARAM_STR);
        if ($_POST['age'] == "true") {
            $prepare->bindValue(':AGE', "Y", PDO::PARAM_STR);
        } else {
            $prepare->bindValue(':AGE', "N", PDO::PARAM_STR);
        }
        if ($_POST['ad'] == "true") {
            $prepare->bindValue(':AD', "Y", PDO::PARAM_STR);
        } else {
            $prepare->bindValue(':AD', "N", PDO::PARAM_STR);
        }
    }
        $prepare->execute();
    $id = $db->lastInsertId();
    //id로 컨텐츠 테이블의 내용도 가져옴
    $sql = "SELECT * FROM publixher.TBL_CONTENT WHERE SEQ=:SEQ";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ', $id, PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetch(PDO::FETCH_ASSOC);
    //글쓴이의 정보도 가져옴
    $sql = "SELECT USER_NAME,PIC FROM publixher.TBL_USER WHERE SEQ=:SEQ";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':SEQ', $seq_writer, PDO::PARAM_STR);
    $prepare->execute();
    $result['WRITE_DATE'] = passing_time($result['WRITE_DATE']);
    $result = array_merge($result, $prepare->fetch(PDO::FETCH_ASSOC));
    //타겟이 있다면 타겟의 정보도 가져옴
    if($targetseq) {
        $t = "SELECT USER_NAME FROM publixher.TBL_USER WHERE SEQ=:SEQ";
        $tp = $db->prepare($t);
        $tp->bindValue(':SEQ', $targetseq);
        $tp->execute();
        $result['TARGET_NAME'] = $tp->fetchColumn();
    }
    //판매목록에 수 증가
    $sql2 = "INSERT INTO publixher.TBL_SELL_LIST(SEQ_USER,SEQ_CONTENT) VALUES(:SEQ_USER,:SEQ_CONTENT);";
    $prepare2 = $db->prepare($sql2);
    $prepare2->bindValue(':SEQ_USER', $seq_writer, PDO::PARAM_STR);
    $prepare2->bindValue(':SEQ_CONTENT', $id, PDO::PARAM_STR);
    $prepare2->execute();
    if ($_POST['folder']) {
        //폴더에 내용 수 증가
        $sql3 = "UPDATE publixher.TBL_FORDER SET CONTENT_NUM=CONTENT_NUM+1 WHERE SEQ=:SEQ";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':SEQ', $_POST['folder'], PDO::PARAM_STR);
        $prepare3->execute();
        //폴더 이름 받아오기
        $sql4 = "SELECT DIR FROM publixher.TBL_FORDER WHERE SEQ=:SEQ";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':SEQ', $_POST['folder'], PDO::PARAM_STR);
        $prepare4->execute();
        $result = array_merge($result, $prepare4->fetch(PDO::FETCH_ASSOC));
    }
    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $result;
} else {
    exit;
}
?>