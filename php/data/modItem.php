<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
session_start();
$action = $_GET['action'] ? $_GET['action'] : $_POST['action'];
$itemID = $_GET['itemID'] ? $_GET['itemID'] : $_POST['itemID'];
if ($action == 'get_item') {
    $gs = "SELECT TITLE,EXPOSE,CATEGORY,SUB_CATEGORY,PRICE,AGE,AD,BODY,FOLDER FROM publixher.TBL_CONTENT WHERE ID=:ID";
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
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'mod_item') {
    require_once '../../lib/passing_time.php';
    require_once '../../lib/blur.php';
    require_once '../../lib/HTMLPurifier.php';
    //CSRF검사
    if (!isset($_POST['token']) AND !isset($_GET['token'])) {
        exit('부정한 조작이 감지되었습니다. case1 \n$_POST["token"] :'.$_POST['token'].' \n $_GET["token"] :'.$_GET['token'].'$_SESSION :'.$_SESSION);
    } elseif ($_POST['token'] != $_SESSION['token'] AND $_GET['token'] != $_SESSION['token']) {
        exit('부정한 조작이 감지되었습니다. case2 \n$_POST["token"] :'.$_POST['token'].' \n $_GET["token"] :'.$_GET['token'].'$_SESSION :'.$_SESSION);
    }
//세션탈취 검사
    if (!isset($_POST['age']) AND !isset($_GET['age'])) {
        exit('부정한 조작이 감지되었습니다. case3 \n$_POST["age"] :'.$_POST['age'].' \n $_GET["age"] :'.$_GET['age'].'$_SESSION :'.$_SESSION);
    } elseif ($_POST['age'] != $_SESSION['age'] AND $_GET['age'] != $_SESSION['age']) {
        exit('부정한 조작이 감지되었습니다. case4 \n$_POST["age"] :'.$_POST['age'].' \n $_GET["age"] :'.$_GET['age'].'$_SESSION :'.$_SESSION);
    }

    //여기부턴 uploadContent.php와 같음

    //이미지 소스만 가져오기
    $reg = "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i";
    $br = "/(\<div\>\<br \/\>\<\/div\>){2,}/i";
    $gallery="/<a[^>]*href=[\"']?\/img\/origin\/[\"']?[^>]*><\/a>/i";
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
    $body=preg_replace($gallery,"",$body);
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
    $ID_writer = $_POST['ID_writer'];
    $targetID = $_POST['targetID'];
    $id=$_POST['ID'];

    $q="SELECT ORIGINAL,CHANGED,BODY FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $p=$db->prepare($q);
    $p->bindValue(':ID',$id);
    $p->execute();
    $origin=$p->fetch(PDO::FETCH_ASSOC);
    $originData=$origin['CHANGED']==1?$origin['ORIGINAL']:$origin['BODY'];

    if (!$_POST['for_sale']) {
        $sql = "UPDATE publixher.TBL_CONTENT SET BODY=:BODY , FOLDER=:FOLDER , EXPOSE=:EXPOSE , CHANGED=1 , PREVIEW=:PREVIEW , ORIGINAL=:ORIGINAL WHERE ID=:ID";
    } else {
        $sql = "UPDATE publixher.TBL_CONTENT SET BODY=:BODY , FOLDER=:FOLDER , EXPOSE=:EXPOSE , CHANGED=1 , PREVIEW=:PREVIEW , ORIGINAL=:ORIGINAL , PRICE=:PRICE , CATEGORY=:CATEGORY , SUB_CATEGORY=:SUB_CATEGORY , TITLE=:TITLE , AGE=:AGE , AD=:AD WHERE ID=:ID";
    }
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $id);
    $prepare->bindValue(':BODY', $body, PDO::PARAM_STR);
    $prepare->bindValue(':PREVIEW', $preview, PDO::PARAM_STR);
    $prepare->bindValue(':FOLDER', $_POST['folder'], PDO::PARAM_STR);
    $prepare->bindValue(':EXPOSE', $_POST['expose'], PDO::PARAM_STR);
    $prepare->bindValue(':ORIGINAL', $originData, PDO::PARAM_STR);

    if ($_POST['for_sale']) {
        $prepare->bindValue(':PRICE', $_POST['price'], PDO::PARAM_STR);
        $prepare->bindValue(':CATEGORY', $_POST['category'], PDO::PARAM_STR);
        $prepare->bindValue(':SUB_CATEGORY', $_POST['sub_category'], PDO::PARAM_STR);
        $prepare->bindValue(':TITLE', $_POST['title'], PDO::PARAM_STR);
        if ($_POST['adult'] == "true") {
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
    //id로 컨텐츠 테이블의 내용도 가져옴
    $sql = "SELECT * FROM publixher.TBL_CONTENT WHERE ID=:ID";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $id, PDO::PARAM_STR);
    $prepare->execute();
    $result = $prepare->fetch(PDO::FETCH_ASSOC);
    //글쓴이의 정보도 가져옴
    $sql = "SELECT USER_NAME,PIC FROM publixher.TBL_USER WHERE ID=:ID";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID', $_SESSION['user']->getID(), PDO::PARAM_STR);
    $prepare->execute();
    $result['WRITE_DATE'] = passing_time($result['WRITE_DATE']);
    $result = array_merge($result, $prepare->fetch(PDO::FETCH_ASSOC));
    //타겟이 있다면 타겟의 정보도 가져옴
    if ($targetID) {
        $t = "SELECT USER_NAME FROM publixher.TBL_USER WHERE ID=:ID";
        $tp = $db->prepare($t);
        $tp->bindValue(':ID', $targetID);
        $tp->execute();
        $result['TARGET_NAME'] = $tp->fetchColumn();
    }
    //원래 폴더에서 수 감소
    $fs="UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM-1 WHERE ID=:ID";
    $fp = $db->prepare($fs);
    $fp->bindValue(':ID', $_POST['folder'], PDO::PARAM_STR);
    $fp->execute();
    if ($_POST['folder']) {
        //폴더에 내용 수 증가
        $sql3 = "UPDATE publixher.TBL_FOLDER SET CONTENT_NUM=CONTENT_NUM+1 WHERE ID=:ID";
        $prepare3 = $db->prepare($sql3);
        $prepare3->bindValue(':ID', $_POST['folder'], PDO::PARAM_STR);
        $prepare3->execute();
        //폴더 이름 받아오기
        $sql4 = "SELECT DIR FROM publixher.TBL_FOLDER WHERE ID=:ID";
        $prepare4 = $db->prepare($sql4);
        $prepare4->bindValue(':ID', $_POST['folder'], PDO::PARAM_STR);
        $prepare4->execute();
        $result = array_merge($result, $prepare4->fetch(PDO::FETCH_ASSOC));
    }
    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $result;
}
?>