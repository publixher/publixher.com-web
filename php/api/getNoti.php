<?php
header("Content-Type:application/json");
require_once '../../conf/database_conf.php';
require_once '../../conf/User.php';
require_once '../../lib/passing_time.php';
$userID = $_GET['userID'];
$action = $_GET['action'];

if ($action == 'confonload') {
    $notinumsql = "SELECT COUNT(*) AS COUNT FROM publixher.TBL_CONTENT_NOTI WHERE (ID_TARGET=:ID_TARGET AND CHECKED='N' AND NOT ID_ACTOR=:ID_ACTOR)";
    $notinumpre = $db->prepare($notinumsql);
    $notinumpre->bindValue(':ID_TARGET', $userID);
    $notinumpre->bindValue(':ID_ACTOR', $userID);
    $notinumpre->execute();
    $number = $notinumpre->fetchColumn();
    echo json_encode($number, JSON_UNESCAPED_UNICODE);
} elseif ($action == 'confnotireq') {
    $nowpage = $_GET['nowpage'] * 20;
    /* 1: 내 컨텐츠가 구매될때:(컨텐츠 ID, 구매자 ID) , (컨텐츠 ID,컨텐츠 SALE,구매자 ID,구매자 이름)
2: 친구 신청 : (신청자 ID) , (신청자 ID, 신청자 이름)
3: 내 게시글에 댓글 :(내 게시글 ID , 댓글 ID , 댓글 단사람 ID) , (내 게시글 ID, 내 게시글 타이틀 , 댓글 ID, 댓글 요약, 댓글 단사람 ID,
                                                         댓글 단사람 이름)
4: 내 게시글에 노크 : (내 게시글 ID , 노크 한사람 ID) , (노크 한 사람 ID, 내 게시글 ID, 내 게시글 요약본, 노크 한사람 이름, 게시글 노크 수)
5: 내가 게시글에 태그될때 :미구현
6:내 댓글에 노크 : (게시글 ID, 내 댓글 ID,노크 한사람 ID,댓글 ID) , (노크 한 사람 ID, 노크 한 사람 이름, 게시글 ID, 게시글 타이틀, 내 댓글 ID,
                                                    내 댓글 요약)
7: 내 댓글에 대댓글 : (게시글 ID, 내 댓글 ID, 대댓글 단사람 ID), (대댓글 단 사람 ID, 대댓글 단 사람 이름, 게시글 ID,게시글 타이틀
                                                        ,내 댓글 ID, 내 댓글 요약)
8: 내가 댓글에서 태그될때 : (게시글 ID, 댓글 ID, 태그한 사람 ID), (게시글 ID, 게시글 타이틀, 댓글 요약, 댓글 ID, 태그한 사람 ID, 태그한 사람 이름)
9: 내가 대댓글에서 태그될때 : (게시글 ID, 댓글 ID, 태그한 사람 ID,대댓글 ID), (게시글 ID, 게시글 타이틀, 댓글 요약, 댓글 ID, 태그한 사람 ID, 태그한 사람 이름,대댓글 ID,대댓글 요약)
     */
    $notireqsql = "SELECT 
  NOTI.ID_CONTENT,
  NOTI.ID_TARGET,
  NOTI.NOTI_DATE,
  NOTI.ACT,
  NOTI.ID_ACTOR,
  NOTI.ID_REPLY,
  NOTI.ID_SUB_REPLY,
  IF(CONT.TITLE IS NOT NULL,LEFT(CONT.TITLE,8),LEFT(CONT.BODY_TEXT,8)) AS TITLE,
  USER.USER_NAME,
  LEFT(REPLY.REPLY_TEXT,8) AS REPLY,
  LEFT(SUB_REPLY.REPLY_TEXT,8) AS SUB_REPLY,
  IF(NOTI.ACT=1,CONT.SALE,NULL) AS SALE,
  IF(NOTI.ACT=4,CONT.KNOCK,NULL) AS KNOCK,
  REPLACE(USER.PIC,'profile','crop50') AS PIC
FROM publixher.TBL_CONTENT_NOTI AS NOTI
  LEFT JOIN publixher.TBL_CONTENT AS CONT
  ON CONT.ID=NOTI.ID_CONTENT
  LEFT JOIN publixher.TBL_USER AS USER
  ON USER.ID=NOTI.ID_ACTOR
  LEFT JOIN publixher.TBL_CONTENT_REPLY AS REPLY
  ON REPLY.ID=NOTI.ID_REPLY
  LEFT JOIN publixher.TBL_CONTENT_SUB_REPLY AS SUB_REPLY
  ON SUB_REPLY.ID=NOTI.ID_SUB_REPLY
WHERE NOTI.ID_TARGET = :ID_TARGET AND NOT NOTI.ID_ACTOR = :ID_ACTOR
ORDER BY NOTI.SEQ DESC
LIMIT :NOWPAGE, 20";
    $notireqpre = $db->prepare($notireqsql);
    $notireqpre->bindValue(':ID_TARGET', $userID);
    $notireqpre->bindValue(':ID_ACTOR', $userID);
    $notireqpre->bindValue(':NOWPAGE', $nowpage);
    $notireqpre->execute();
    $notis = $notireqpre->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($notis, JSON_UNESCAPED_UNICODE);
    //응답한다음 알림을 전부 읽은걸로 처리한다
    $sql = "UPDATE publixher.TBL_CONTENT_NOTI SET CHECKED='Y' WHERE ID_TARGET=:ID_TARGET";
    $prepare = $db->prepare($sql);
    $prepare->bindValue(':ID_TARGET', $userID);
    $prepare->execute();
}
?>