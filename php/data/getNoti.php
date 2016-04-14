<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/passing_time.php';
session_start();
if(!isset($_SESSION['user'])) include_once "../../lib/loginchk.php";
$userinfo = $_SESSION['user'];
$userID = $userinfo->getID();
$action = $_GET['action'];

if ($action == 'noticenter') {
    //계획 : ACT의 값에 따라 그룹화한다 1: 내 컨텐츠 구매 , 2:친구신청 , 3:내 게시글에 댓글 , 4:내 게시글에 노크 , 5: 내가 게시글에서 태그될때 , 6 : 내 댓글에 노크 , 7 : 내 댓글에 대댓글 , 8 : 운영자의 알림 , 9: 내가 댓글에서 태그될
//그룹관리
    $nowpage = $_GET['nowpage'] * 30;
    $notisql = "SELECT * FROM publixher.TBL_CONTENT_NOTI WHERE ID_TARGET=:ID_TARGET AND NOT ID_ACTOR=:ID_ACTOR ORDER BY NOTI_DATE DESC LIMIT " . $nowpage . ",30";

    $notiprepare = $db->prepare($notisql);
    $notiprepare->bindValue(':ID_TARGET', $userID);
    $notiprepare->bindValue(':ID_ACTOR', $userID);
    $notiprepare->execute();
    $notis = $notiprepare->fetchAll(PDO::FETCH_ASSOC);
//알림들을 ACT에 따라 분류
    $act = array();
    for ($i = 1; $i < 10; $i++) {
        $act[$i] = array();
    }
    foreach ($notis as $val) {
        array_push($act[$val['ACT']], $val);
    }
//그룹화
    function notigrouping($actnum, $order, $db)
    {  //$actnum은 $act[0]처럼 동작 그룹,order는 그룹의 기준,db는 db
        $array = array();
        foreach ($actnum as $val) {
            if (!$array[$val[$order]]) {
                $array[$val[$order]] = array();
                $array[$val[$order]][0] = $val;
                $array[$val[$order]]['date'] = $val['NOTI_DATE'];
                $array[$val[$order]]['count'] = 1;
            } else {
                array_push($array[$val[$order]], $val);
                //해당 알림그룹에 대한 날짜를 설정하는데 제일 최신순으로 설정한
                $array[$val[$order]]['date'] = $array[$val[$order]]['date'] < $val['NOTI_DATE'] ? $val['NOTI_DATE'] : $array[$val[$order]]['date'];
                $array[$val[$order]]['count'] = count($array[$val[$order]]) - 2;
            }
        }
        if ($order == 'ID_CONTENT') {
            $sql = "SELECT TITLE FROM publixher.TBL_CONTENT WHERE ID=:ID";
            $prepare = $db->prepare($sql);
            $return = array();
            foreach ($array as $contentID => $value) {
                $prepare->bindValue(':ID', $contentID);
                $prepare->execute();
                $title = $prepare->fetchColumn();
                if ($title) {
                    $return[$title] = $value;
                } else {
                    $return['SNS-' . $contentID] = $value;
                }
            }
        } elseif ($order == 'ID_REPLY') {
            $sql = "SELECT REPLY FROM publixher.TBL_CONTENT_REPLY WHERE ID=:ID";
            $prepare = $db->prepare($sql);
            $return = array();
            foreach ($array as $replyID => $value) {
                $prepare->bindValue(':ID', $replyID);
                $prepare->execute();
//            $reply = $prepare->fetchColumn();
                $reply = mb_substr($prepare->fetchColumn(), 0, 8, 'UTF-8');    //한글 깨지는경우가 있어서 mb_substr를 쓴다
                $return[$reply] = $value;
            }
        }
        if (!$return) $return = null;
        return $return;
    }
//여기부터
//$act[1]은 ID_CONTENT로 나눠서 다시 그룹을 만듦
    $act[1] = notigrouping($act[1], 'ID_CONTENT', $db);
//$act[2]는 그룹을 나누지 않음
    if (!$act[2]) {
        $act[2] = null;
    } else {
        $friendsql = "SELECT USER_NAME FROM publixher.TBL_USER WHERE ID=:ID";
        $friendpre = $db->prepare($friendsql);
        for ($i = 0; $i < count($act[2]); $i++) {
            $friendpre->bindValue(':ID', $act[2][$i]['ID_ACTOR']);
            $friendpre->execute();
            $act[2][$i]['USER_NAME'] = $friendpre->fetchColumn();
        }
    }
//$act[3]은 ID_CONTENT로 그룹을 나눔
    $act[3] = notigrouping($act[3], 'ID_CONTENT', $db);
//$act[4]는 ID_CONTENT로 그룹을 나눔
    $act[4] = notigrouping($act[4], 'ID_CONTENT', $db);
//$act[5]는 ID_CONTENT로 그룹을 나눔
    $act[5] = notigrouping($act[5], 'ID_CONTENT', $db);
//$act[6]는 ID_REPLY 기준 그룹으로 나눔
    $act[6] = notigrouping($act[6], 'ID_REPLY', $db);
//$act[7]은 ID_REPLY 그룹으로 나눔
    $act[7] = notigrouping($act[7], 'ID_REPLY', $db);
//$act[8]은 그룹을 나누지 않음
    if (!$act[8]) $act[8] = null;
//$act[9]는ID_REPLY로 그룹을 나눔
    $act[9] = notigrouping($act[9], 'ID_REPLY', $db);

    //그룹화된 알림 개수 세기
    $count = 0;
    foreach ($act as $val) {
        $count += count($val);
    }
    $act['count'] = $count;
    echo json_encode($act, JSON_UNESCAPED_UNICODE);
    //사용자한테 답은 갔고 여기서부터 서버단에서 작업하는것
    $db->query("UPDATE publixher.TBL_CONTENT_NOTI SET CHECKED='Y' WHERE ID_TARGET=" . $userID);
} elseif ($action == 'confonload') {
    $notinumsql = "SELECT COUNT(*) FROM publixher.TBL_CONTENT_NOTI WHERE (ID_TARGET=:ID_TARGET AND CHECKED='N' AND NOT ID_ACTOR=:ID_ACTOR)";
    $notinumpre = $db->prepare($notinumsql);
    $notinumpre->bindValue(':ID_TARGET', $userID);
    $notinumpre->bindValue(':ID_ACTOR', $userID);
    $notinumpre->execute();
    $number = $notinumpre->fetchColumn();
    echo json_encode($number, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'confnotireq') {
    $nowpage = $_GET['nowpage']*20;
    $notireqsql = "SELECT * FROM publixher.TBL_CONTENT_NOTI WHERE (ID_TARGET=:ID_TARGET AND NOT ID_ACTOR=:ID_ACTOR) ORDER BY ID DESC LIMIT " . $nowpage . ",20";
    $notireqpre = $db->prepare($notireqsql);
    $notireqpre->bindValue(':ID_TARGET', $userID);
    $notireqpre->bindValue(':ID_ACTOR', $userID);
    $notireqpre->execute();
    $notis = $notireqpre->fetchAll(PDO::FETCH_ASSOC);
    function getTrigger($targetnoti, $order, $db)
    {
        if ($order == 'ID_CONTENT') {
            $sql = "SELECT TITLE FROM publixher.TBL_CONTENT WHERE ID=:ID";
            $prepare = $db->prepare($sql);
            $prepare->bindValue(':ID', $targetnoti['ID_CONTENT']);
            $prepare->execute();
            $targetnoti['TITLE'] = $prepare->fetchColumn();
        } elseif ($order == 'ID_REPLY') {
            $sql = "SELECT REPLY FROM publixher.TBL_CONTENT_REPLY WHERE ID=:ID";
            $prepare = $db->prepare($sql);
            $prepare->bindValue(':ID', $targetnoti['ID_REPLY']);
            $prepare->execute();
            $targetnoti['REPLY'] = mb_substr($prepare->fetchColumn(), 0, 8, 'UTF-8');    //한글 깨지는경우가 있어서 mb_substr를 쓴다
        }elseif($order=='ID_ACTOR'){
            $sql="SELECT USER_NAME FROM publixher.TBL_USER WHERE ID=:ID";
            $prepare=$db->prepare($sql);
            $prepare->bindValue(":ID",$targetnoti['ID_ACTOR']);
            $prepare->execute();
            $targetnoti['USER_NAME']=$prepare->fetchColumn();
        }
        return $targetnoti;
    }

    for ($i = 0; $i < count($notis); $i++) {
        if ($notis[$i]['ACT'] == '1' or $notis[$i]['ACT'] == '3' or $notis[$i]['ACT'] == '4') { //컨텐츠가 주체일때
            $notis[$i]=getTrigger($notis[$i],'ID_CONTENT', $db);
        } elseif ($notis[$i]['ACT'] == '6' or $notis[$i]['ACT']=='7') {   //댓글이 주체일때
            $notis[$i]=getTrigger($notis[$i],'ID_REPLY',$db);
        }elseif($notis[$i]['ACT']=='2'){
            $notis[$i]=getTrigger($notis[$i],'ID_ACTOR',$db);
        }
    }
    echo json_encode($notis, JSON_UNESCAPED_UNICODE);
    //응답한다음 알림을 전부 읽은걸로 처리한다
    $sql="UPDATE publixher.TBL_CONTENT_NOTI SET CHECKED='Y' WHERE ID_TARGET=:ID_TARGET";
    $prepare=$db->prepare($sql);
    $prepare->bindValue(':ID_TARGET',$userID);
    $prepare->execute();
}
?>