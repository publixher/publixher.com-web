<?php

/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 6. 11.
 * Time: 오후 2:54
 */
class getC
{
    private $mId;
    private $db;

    public function __construct($mid, $db)
    {
        $this->mId = $mid;
        $this->db = $db;
    }

    public function profile(int $page, string $target)
    {
        $parameter = array('USER_ID1' => $this->mId, 'USER_ID2' => $this->mId, 'ID_WRITER' => $target, 'ID_TARGET' => $target, 'NOWPAGE' => $page,'KNOCK_USER_ID'=>$this->mId);
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
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                             AS FOLDER_NAME,
  USER2.USER_NAME                        AS TARGET_NAME,
  USER2.ID                               AS TARGET_ID,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
  LEFT JOIN publixher.TBL_USER AS USER2
    ON USER2.ID = CONT.ID_TARGET
    LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE DEL = 'N' AND (ID_WRITER = :ID_WRITER OR ID_TARGET = :ID_TARGET) AND EXPOSE >= (
  SELECT CASE ID_WRITER
         WHEN :USER_ID1
           THEN 0
         WHEN (
           SELECT ID_FRIEND
           FROM publixher.TBL_FRIENDS
           WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
         )
           THEN 1
         ELSE 2 END AS AUTH
  FROM
publixher.TBL_CONTENT
  WHERE
    ID = CONT.ID
) AND REPORT < 10
ORDER BY WRITE_DATE DESC
LIMIT :NOWPAGE, 10";

        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function folder(int $page, string $folderId)
    {
        $parameter = array('USER_ID1' => $this->mId, 'USER_ID2' => $this->mId, 'NOWPAGE' => $page, 'FOLDER' => $folderId,'KNOCK_USER_ID'=>$this->mId);
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
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                             AS FOLDER_NAME,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  INNER JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
    LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE DEL = 'N' AND FOLDER = :FOLDER AND REPORT < 10 AND EXPOSE >= (
  SELECT CASE ID_WRITER
         WHEN :USER_ID1
           THEN 0
         WHEN (
           SELECT ID_FRIEND
           FROM publixher.TBL_FRIENDS
           WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
         )
           THEN 1
         ELSE 2 END AS AUTH
  FROM
publixher.TBL_CONTENT
  WHERE
    ID = CONT.ID
)
ORDER BY WRITE_DATE DESC
LIMIT :NOWPAGE, 10";

        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function buyList(int $page)
    {
        $parameter = array('NOWPAGE' => $page, 'ID_USER' => $this->mId,'KNOCK_USER_ID'=>$this->mId);
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
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                             AS FOLDER_NAME,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_BUY_LIST AS BUY_LIST
  INNER JOIN publixher.TBL_CONTENT AS CONT
    ON BUY_LIST.ID_CONTENT = CONT.ID
  INNER JOIN publixher.TBL_USER AS USER
    ON CONT.ID_WRITER = USER.ID
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
    LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE BUY_LIST.ID_USER = :ID_USER
      AND CONT.DEL = 'N' AND REPORT < 10
       ORDER BY BUY_LIST.BUY_DATE DESC
            LIMIT :NOWPAGE,10";
        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function one(string $cid)
    {
        $parameter = array('ID' => $cid, 'USER_ID1' => $this->mId, 'USER_ID2' => $this->mId,'KNOCK_USER_ID'=>$this->mId);
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
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                             AS FOLDER_NAME,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
    LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE DEL = 'N' AND CONT.ID = :ID AND REPORT < 10 AND EXPOSE >= (
  SELECT CASE ID_WRITER
         WHEN :USER_ID1
           THEN 0
         WHEN (
           SELECT ID_FRIEND
           FROM publixher.TBL_FRIENDS
           WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
         )
           THEN 1
         ELSE 2 END AS AUTH
  FROM
publixher.TBL_CONTENT
  WHERE
    ID = CONT.ID
)";

        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function tag(int $page, string $tag)
    {
        $parameter = array('USER_ID1' => $this->mId, 'USER_ID2' => $this->mId, 'NOWPAGE' => $page,'KNOCK_USER_ID'=>$this->mId);
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
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                             AS FOLDER_NAME,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
    LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE DEL = 'N' AND MATCH(TAG) AGAINST('${tag}') AND REPORT < 10 AND EXPOSE >= (
  SELECT CASE ID_WRITER
         WHEN :USER_ID1
           THEN 0
         WHEN (
           SELECT ID_FRIEND
           FROM publixher.TBL_FRIENDS
           WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
         )
           THEN 1
         ELSE 2 END AS AUTH
  FROM
publixher.TBL_CONTENT
  WHERE
    ID = CONT.ID
)
ORDER BY WRITE_DATE DESC
LIMIT :NOWPAGE, 10";

        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function body(int $page, string $body)
    {
        $parameter = array('USER_ID1' => $this->mId, 'USER_ID2' => $this->mId, 'NOWPAGE' => $page,'KNOCK_USER_ID'=>$this->mId);
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
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                             AS FOLDER_NAME,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
    LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE DEL = 'N' AND MATCH(BODY_TEXT) AGAINST('*${body}*' IN BOOLEAN MODE) AND REPORT < 10 AND EXPOSE >= (
  SELECT CASE ID_WRITER
         WHEN :USER_ID1
           THEN 0
         WHEN (
           SELECT ID_FRIEND
           FROM publixher.TBL_FRIENDS
           WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
         )
           THEN 1
         ELSE 2 END AS AUTH
  FROM
publixher.TBL_CONTENT
  WHERE
    ID = CONT.ID
)
ORDER BY WRITE_DATE DESC
LIMIT :NOWPAGE, 10";

        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function subscribe(int $page)
    {
        $parameter = array('ID_SLAVE' => $this->mId, 'NOWPAGE' => $page, 'USER_ID1' => $this->mId, 'USER_ID2' => $this->mId,'KNOCK_USER_ID'=>$this->mId);
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
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                             AS FOLDER_NAME,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
  INNER JOIN publixher.TBL_FOLLOW AS FOLLOW
    ON FOLLOW.ID_MASTER = CONT.ID_WRITER
    LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE DEL = 'N' AND ID_TARGET IS NULL AND REPORT < 10 AND FOLLOW.ID_SLAVE = :ID_SLAVE AND EXPOSE >= (
  SELECT CASE ID_WRITER
         WHEN :USER_ID1
           THEN 0
         WHEN (
           SELECT ID_FRIEND
           FROM publixher.TBL_FRIENDS
           WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
         )
           THEN 1
         ELSE 2 END AS AUTH
  FROM
publixher.TBL_CONTENT
  WHERE
    ID = CONT.ID
)
ORDER BY WRITE_DATE DESC
LIMIT :NOWPAGE, 10";
        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function community(int $page)
    {
        $parameter = array('USER_ID1' => $this->mId, 'USER_ID2' => $this->mId, 'ID_USER' => $this->mId, 'NOWPAGE' => $page,'KNOCK_USER_ID'=>$this->mId);
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
  WRITER.USER_NAME,
  REPLACE(WRITER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                               AS FOLDER_NAME,
  TARGET.ID                                AS TARGET_ID,
  TARGET.USER_NAME                         AS TARGET_NAME,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS WRITER
    ON WRITER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
  LEFT JOIN publixher.TBL_USER AS TARGET
    ON CONT.ID_TARGET = TARGET.ID
  INNER JOIN publixher.TBL_FRIENDS AS FRIENDS
    ON FRIENDS.ID_FRIEND = TARGET.ID
    LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE DEL = 'N' AND REPORT < 10 AND FRIENDS.ID_USER = :ID_USER AND TARGET.COMMUNITY = 1 AND EXPOSE >= (
  SELECT CASE ID_WRITER
         WHEN :USER_ID1
           THEN 0
         WHEN (
           SELECT ID_FRIEND
           FROM publixher.TBL_FRIENDS
           WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
         )
           THEN 1
         ELSE 2 END AS AUTH
  FROM
publixher.TBL_CONTENT
  WHERE
    ID = CONT.ID
)
ORDER BY WRITE_DATE DESC
LIMIT :NOWPAGE, 10";
        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function main(int $page)
    {
        $parameter = array('USER_ID1' => $this->mId,
            'USER_ID2' => $this->mId, 'NOWPAGE' => $page,'KNOCK_USER_ID'=>$this->mId/*,
            'ME1' => $this->mId, 'ME2' => $this->mId,
            'ME3' => $this->mId*/);
        //모든 글중 내가 볼 수 있는 글
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
  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
  FOLDER.DIR                             AS FOLDER_NAME,
  IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
  LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
WHERE DEL = 'N' AND ID_TARGET IS NULL AND REPORT < 10 AND EXPOSE >= (
  SELECT CASE ID_WRITER
         WHEN :USER_ID1
           THEN 0
         WHEN (
           SELECT ID_FRIEND
           FROM publixher.TBL_FRIENDS
           WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
         )
           THEN 1
         ELSE 2 END AS AUTH
  FROM
publixher.TBL_CONTENT
  WHERE
    ID = CONT.ID
)
ORDER BY WRITE_DATE DESC
LIMIT :NOWPAGE, 10";

        //내글,친구의 친구공개 및 전체공개 글,구독한 사람의 전체공개글
//        $sql = "SELECT
//  CONT.ID,
//  CONT.ID_WRITER,
//  CONT.TITLE,
//  CONT.EXPOSE,
//  CONT.KNOCK,
//  CONT.WRITE_DATE,
//  CONT.MODIFY_DATE,
//  CONT.FOR_SALE,
//  CONT.CATEGORY,
//  CONT.SUB_CATEGORY,
//  CONT.PRICE,
//  CONT.PREVIEW,
//  CONT.COMMENT,
//  CONT.SALE,
//  CONT.FOLDER,
//  CONT.CHANGED,
//  CONT.MORE,
//  CONT.TAG,
//  USER.USER_NAME,
//  REPLACE(USER.PIC, 'profile', 'crop50') AS PIC,
//  FOLDER.DIR                             AS FOLDER_NAME,
//        IF(KNOCK.ID_CONTENT IS NOT NULL,TRUE,FALSE) AS KNOCKED
//FROM publixher.TBL_CONTENT AS CONT
//  INNER JOIN publixher.TBL_USER AS USER
//    ON USER.ID = CONT.ID_WRITER
//  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
//    ON CONT.FOLDER = FOLDER.ID
//  LEFT JOIN publixher.TBL_FRIENDS AS FRIEND
//    ON FRIEND.ID_FRIEND = CONT.ID_WRITER
//  LEFT JOIN publixher.TBL_FOLLOW AS FOLLOW
//    ON FOLLOW.ID_MASTER=CONT.ID_WRITER
//        LEFT JOIN publixher.TBL_KNOCK_LIST AS KNOCK
//    ON KNOCK.ID_USER=:KNOCK_USER_ID AND KNOCK.ID_CONTENT=CONT.ID
//WHERE
//  DEL = 'N' AND ID_TARGET IS NULL AND REPORT < 10 AND (
//    FRIEND.ID_USER = :ME1 OR CONT.ID_WRITER = :ME2 OR FOLLOW.ID_SLAVE=:ME3
//  ) AND EXPOSE >= (
//    SELECT CASE ID_WRITER
//           WHEN :USER_ID1
//             THEN 0
//           WHEN (
//             SELECT ID_FRIEND
//             FROM publixher.TBL_FRIENDS
//             WHERE ID_USER = :USER_ID2 AND ID_FRIEND = CONT.ID_WRITER AND ALLOWED = 'Y'
//           )
//             THEN 1
//           ELSE 2 END AS AUTH
//    FROM
//      publixher.TBL_CONTENT
//    WHERE
//      ID = CONT.ID
//  )
//ORDER BY WRITE_DATE DESC
//LIMIT :NOWPAGE, 10";
        $prepare = $this->db->prepare($sql);
            $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function set_recommended()
    {
        $mongomanager = new MongoDB\Driver\Manager("mongodb://DongGyun:Pp999223#@localhost:27017/publixher");
        $now = time();
        $average_time = array();
        $interesting_id = array();
        $interested_user = array();
        $users_intersect = array();
        $array_intersected = array();
        $kind_of_me_value = array();
        $recommended_id = array();
        $filter = ['id' => $this->mId];
        $options = [
            'projection' => ['_id' => 0]
        ];
        $query = new MongoDB\Driver\Query($filter, $options);
        $rows = $mongomanager->executeQuery('publixher.user', $query);
        foreach ($rows as $key => $row) {
            //지금 쌓여있는 내가 봤던 컨텐츠 리스트=$viewd
            $viewd = $row->interest;
            $ids = array();
            foreach ($row->interest as $content) {
                $ids[] = $content->id;
            }

            $content_filter = ['id' => ['$in' => $ids]];
            $content_options = [
                'projection' => ['_id' => 0]
            ];
            $content_query = new MongoDB\Driver\Query($content_filter, $content_options);
            $contents = $mongomanager->executeQuery('publixher.contents', $content_query);
            foreach ($contents as $content) {
                $average_time[$content->id] = $content->average_time;
            }
            //$average_time에 내가 봤던 컨텐츠들중 각각의 평균 보여진 시간이 담겨져 있다
            //상대적인 흥미도를 얻기
            usort($viewd, function ($a, $b) use ($now, $average_time) {
                $a_std = $a->time / (($now - $a->when) * $average_time[$a->id]);
                $b_std = $b->time / (($now - $b->when) * $average_time[$b->id]);
                if ($a_std > $b_std) {
                    return -1;
                } elseif ($a_std < $b_std) {
                    return 1;
                } else {
                    return 0;
                }
            });

            //$viewd에 흥미있는 순서대로 정렬됬음
            array_splice($viewd, 5);
            foreach ($viewd as $interesting) {
                $interesting_id[] = $interesting->id;
            }
            //이제 $viewd는 최고로 흥미있는 5개

            //컨텐츠 다섯개에 기록된 유저 중 2회 이상 기록된 유저를 찾음
            $content_filter = ['id' => ['$in' => $interesting_id]];
            $content_options = [
                'projection' => ['_id' => 0]
            ];
            $content_query = new MongoDB\Driver\Query($content_filter, $content_options);
            $user_lists = $mongomanager->executeQuery('publixher.contents', $content_query);
            foreach ($user_lists as $user_list) {
                //$user_list 에는 각 컨텐츠를 맘에 들어 한 사람 리스트가 있다
                $interested_user[] = $user_list->interested_users;
            }
            //이제 $interested_user에는 각 컨텐츠가 5개 순위에 든 사람들의 id가 있음
            for ($i = 0; $i < count($interested_user) - 1; $i++) {
                for ($j = $i + 1; $j < count($interested_user) - $i; $j++) {
                    $users_intersect[] = array_intersect($interested_user[$i], $interested_user[$j]);
                }
            }
            //$users_intersect의 각 원소에는 겹치는 사람들이 있다(다른 원소에 있는 사람이 중복 될 수 있다)
            foreach ($users_intersect as $user_intersect) {
                foreach ($user_intersect as $intersected_user) {
                    $array_intersected[] = $intersected_user;
                }
            }

            $kind_of_me = array_count_values($array_intersected);
            $kind_of_me = array_filter($kind_of_me, function ($var) {
                if ($var >= 2) {
                    return true;
                } else return false;
            });
            foreach ($kind_of_me as $id) {
                $kind_of_me_value[] = array_keys($id);
            }
            //$kind_of_me_value배열의 값에는 나와 비슷한 취향의 사람들의 아이디가 들어감
            $user_filter = ['id' => ['$in' => $kind_of_me_value]];
            $user_option = [
                'projection' => ['_id' => 0, 'interest' => 1]
            ];
            $user_query = new MongoDB\Driver\Query($user_filter, $user_option);
            $interest_lists = $mongomanager->executeQuery('publixher.user', $user_query);
            foreach ($interest_lists as $interest_list) {
                $recommended_id[] = $interest_list->id;
            }
            //해당 컨텐츠의 관심있는 사람 리스트에 자신을 올려놓는다
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(['id' => ['$in' => $interesting_id]], ['$addToSet' => ['interested_users' => $this->mId]], ['upsert' => true, 'multi' => true]);
            //나머지 내 interest중 $interesting_id에 없는 컨텐츠는 컨텐츠의 interested_users에서 자신을 뺀다
            $bulk->update(['id' => ['$nin' => $interesting_id]], ['$pull' => ['interested_users' => $this->mId]], ['multi' => true]);
            $mongomanager->executeBulkWrite('publixher.contents', $bulk);
            //내 interest에서 $interesting_id에 없는건 전부 다 뺀다
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(['id'=>$this->mId],['$pull'=>['interest'=>['id'=>['$nin'=>$interesting_id]]]],['multi'=>true]);
            $bulk->update(['id' => $this->mId], ['$set' => ['recommended_list' => $recommended_id]], ['upsert' => true]);
            $mongomanager->executeBulkWrite('publixher.user', $bulk);

            return true;
        }
    }

}