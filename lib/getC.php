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
        $parameter = array('USER_ID1'=>$this->mId,'USER_ID2'=>$this->mId,'ID_WRITER'=>$target,'ID_TARGET'=>$target,'NOWPAGE'=>$page);
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
  USER2.ID                               AS TARGET_ID
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
  LEFT JOIN publixher.TBL_USER AS USER2
    ON USER2.ID = CONT.ID_TARGET
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
        $parameter=array('USER_ID1'=>$this->mId,'USER_ID2'=>$this->mId,'NOWPAGE'=>$page,'FOLDER'=>$folderId);
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
  FOLDER.DIR                             AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  INNER JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
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
        $parameter = array('NOWPAGE'=>$page,'ID_USER'=>$this->mId);
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
  FOLDER.DIR                             AS FOLDER_NAME
FROM publixher.TBL_BUY_LIST AS BUY_LIST
  INNER JOIN publixher.TBL_CONTENT AS CONT
    ON BUY_LIST.ID_CONTENT = CONT.ID
  INNER JOIN publixher.TBL_USER AS USER
    ON CONT.ID_WRITER = USER.ID
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
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
        $parameter = array('ID' => $cid,'USER_ID1'=>$this->mId,'USER_ID2'=>$this->mId);
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
  FOLDER.DIR                             AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
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

    public function tag(int $page,string $tag)
    {
        $parameter = array('USER_ID1'=>$this->mId,'USER_ID2'=>$this->mId,'NOWPAGE'=>$page);
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
  FOLDER.DIR                             AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
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

    public function body(int $page,string $body){
        $parameter = array('USER_ID1'=>$this->mId,'USER_ID2'=>$this->mId,'NOWPAGE'=>$page);
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
  FOLDER.DIR                             AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
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

    public function subscribe(int $page){
        $parameter=array('ID_SLAVE'=>$this->mId,'NOWPAGE'=>$page,'USER_ID1'=>$this->mId,'USER_ID2'=>$this->mId);
        $sql= "SELECT
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
  FOLDER.DIR                             AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
  INNER JOIN publixher.TBL_FOLLOW AS FOLLOW
    ON FOLLOW.ID_MASTER = CONT.ID_WRITER
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
        $prepare=$this->db->prepare($sql);
        $prepare->execute($parameter);
        $result=$prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function community(int $page){
        $parameter=array('USER_ID1'=>$this->mId,'USER_ID2'=>$this->mId,'ID_USER'=>$this->mId,'NOWPAGE'=>$page);
        $sql= "SELECT
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
  TARGET.USER_NAME                         AS TARGET_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS WRITER
    ON WRITER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
  LEFT JOIN publixher.TBL_USER AS TARGET
    ON CONT.ID_TARGET = TARGET.ID
  INNER JOIN publixher.TBL_FRIENDS AS FRIENDS
    ON FRIENDS.ID_FRIEND = TARGET.ID
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
        $prepare=$this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function main(int $page){
        $parameter=array('USER_ID1'=>$this->mId,'USER_ID2'=>$this->mId,'NOWPAGE'=>$page);
        $sql= "SELECT
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
  FOLDER.DIR                             AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
    ON USER.ID = CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
    ON CONT.FOLDER = FOLDER.ID
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
        try {
            $prepare = $this->db->prepare($sql);
        }catch(PDOException $e){
            $a=$e->getMessage();
        }
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}