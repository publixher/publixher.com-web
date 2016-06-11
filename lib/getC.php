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

    public function getExpose($target)
    {
        if ($target == $this->mId) {
            return 0;
        } else {
            $sql = "SELECT COUNT(*) AS FRIEND FROM publixher.TBL_FRIENDS WHERE ID_USER=:ID_USER AND ID_FRIEND=:ID_FRIEND AND ALLOWED='Y'";
            $prepare = $this->db->prepare($sql);
            $prepare->execute(array('ID_USER' => $this->mId, 'ID_FRIEND' => $target));
            $result = $prepare->fetchColumn();
            if ($result != 0) {
                return 1;
            } else {
                return 2;
            }
        }
    }

    public function profile(int $page, string $target, string $category = null, string $sub_category = null)
    {
        $parameter = array();
        $expose = $this->getExpose($target);
        $sql = "SELECT CONT.ID,CONT.ID_WRITER,CONT.TITLE,CONT.EXPOSE,CONT.KNOCK,CONT.WRITE_DATE,CONT.MODIFY_DATE,
            CONT.FOR_SALE,CONT.CATEGORY,CONT.SUB_CATEGORY,CONT.PRICE,CONT.PREVIEW,CONT.COMMENT,CONT.SALE,CONT.FOLDER,
            CONT.CHANGED,CONT.MORE,CONT.TAG,USER.USER_NAME,REPLACE(USER.PIC,'profile','crop50') AS PIC, FOLDER.DIR AS FOLDER_NAME,
            USER2.USER_NAME AS TARGET_NAME,USER2.ID AS TARGET_ID
            FROM publixher.TBL_CONTENT AS CONT 
            INNER JOIN publixher.TBL_USER AS USER
            ON USER.ID=CONT.ID_WRITER
            LEFT JOIN publixher.TBL_FOLDER AS FOLDER
            ON CONT.FOLDER=FOLDER.ID
            LEFT JOIN publixher.TBL_USER AS USER2
            ON USER2.ID=CONT.ID_TARGET
            WHERE DEL='N' AND (ID_WRITER=:ID_WRITER OR ID_TARGET = :ID_TARGET) AND EXPOSE>=:EXPOSE AND REPORT < 10";
        if ($category !== null) {
            $sql .= " AND CATEGORY=:CATEOGORY";
            $parameter['CATEGORY'] = $category;
            if ($sub_category !== null) {
                $sql .= " AND SUB_CATEGORY=:SUB_CATEGORY";
                $parameter['SUB_CATEGORY'] = $sub_category;
            }
        }
        $sql .= " ORDER BY WRITE_DATE DESC
            LIMIT :NOWPAGE,10";

        $parameter['NOWPAGE'] = $page;
        $parameter['ID_WRITER'] = $target;
        $parameter['ID_TARGET'] = $target;
        $parameter['EXPOSE'] = $expose;
        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function folder(int $page, string $folderId)
    {
        $sql = "SELECT ID_USER FROM publixher.TBL_FOLDER WHERE ID=:ID";
        $prepare = $this->db->prepare($sql);
        $prepare->execute(array('ID' => $folderId));
        $expose = $this->getExpose($prepare->fetchColumn());

        $sql = "SELECT CONT.ID,CONT.ID_WRITER,CONT.TITLE,CONT.EXPOSE,CONT.KNOCK,CONT.WRITE_DATE,CONT.MODIFY_DATE,CONT.FOR_SALE,
        CONT.CATEGORY,CONT.SUB_CATEGORY,CONT.PRICE,CONT.PREVIEW,CONT.COMMENT,CONT.SALE,CONT.FOLDER,CONT.CHANGED,CONT.MORE,CONT.TAG,
        USER.USER_NAME,REPLACE(USER.PIC,'profile','crop50') AS PIC,FOLDER.DIR AS FOLDER_NAME 
        FROM publixher.TBL_CONTENT AS CONT
        INNER JOIN publixher.TBL_USER AS USER 
        ON USER.ID=CONT.ID_WRITER 
        LEFT JOIN publixher.TBL_FOLDER AS FOLDER 
        ON CONT.FOLDER=FOLDER.ID
        WHERE DEL='N' AND FOLDER=:FOLDER AND REPORT<10 AND EXPOSE>=:EXPOSE ORDER BY WRITE_DATE DESC LIMIT :NOWPAGE,10";

        $prepare = $this->db->prepare($sql);
        $prepare->execute(array('FOLDER' => $folderId, 'EXPOSE' => $expose, 'NOWPAGE', $page));
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function buyList(int $page, string $category = null, string $sub_category = null)
    {
        $parameter = array();
        $sql = "SELECT CONT.ID,CONT.ID_WRITER,CONT.TITLE,CONT.EXPOSE,CONT.KNOCK,CONT.WRITE_DATE,CONT.MODIFY_DATE,CONT.FOR_SALE,CONT.CATEGORY,
        CONT.SUB_CATEGORY,CONT.PRICE,CONT.PREVIEW,CONT.COMMENT,CONT.SALE,CONT.FOLDER,CONT.CHANGED,CONT.MORE,CONT.TAG,
  USER.USER_NAME,REPLACE(USER.PIC,'profile','crop50') AS PIC,FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_BUY_LIST AS BUY_LIST
  INNER JOIN publixher.TBL_CONTENT AS CONT
  ON BUY_LIST.ID_CONTENT=CONT.ID
  INNER JOIN publixher.TBL_USER AS USER
  ON CONT.ID_WRITER=USER.ID
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE BUY_LIST.ID_USER = :ID_USER
  AND CONT.DEL='N' AND REPORT<10";
        if ($category !== null) {
            $sql .= " AND CATEGORY=:CATEOGORY";
            $parameter['CATEGORY'] = $category;
            if ($sub_category !== null) {
                $sql .= " AND SUB_CATEGORY=:SUB_CATEGORY";
                $parameter['SUB_CATEGORY'] = $sub_category;
            }
        }
        $sql .= " ORDER BY WRITE_DATE DESC
            LIMIT :NOWPAGE,10";
        $parameter['NOWPAGE'] = $page;
        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function one(string $cid)
    {
        $parameter = array('ID' => $cid);
        $sql = "SELECT CONT.ID, CONT.ID_WRITER, CONT.TITLE, CONT.EXPOSE, CONT.KNOCK, CONT.WRITE_DATE, CONT.MODIFY_DATE,
  CONT.FOR_SALE,CONT.CATEGORY,CONT.SUB_CATEGORY,CONT.PRICE,CONT.PREVIEW,CONT.COMMENT,CONT.SALE,CONT.FOLDER,CONT.CHANGED,
  CONT.MORE,CONT.TAG,USER.USER_NAME,REPLACE(USER.PIC,'profile','crop50') AS PIC,FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE DEL = 'N' AND CONT.ID = :ID AND REPORT < 10";

        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result = $prepare->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function tag(string $tag,string $category=null,string $sub_category=null)
    {
        $parameter=array();
        $sql= "SELECT CONT.ID, CONT.ID_WRITER, CONT.TITLE, CONT.EXPOSE, CONT.KNOCK, CONT.WRITE_DATE, CONT.MODIFY_DATE,
  CONT.FOR_SALE,CONT.CATEGORY,CONT.SUB_CATEGORY,CONT.PRICE,CONT.PREVIEW,CONT.COMMENT,CONT.SALE,CONT.FOLDER,
  CONT.CHANGED,CONT.MORE,CONT.TAG,USER.USER_NAME,REPLACE(USER.PIC,'profile','crop50') AS PIC,FOLDER.DIR AS FOLDER_NAME
FROM publixher.TBL_CONTENT AS CONT
  INNER JOIN publixher.TBL_USER AS USER
  ON USER.ID=CONT.ID_WRITER
  LEFT JOIN publixher.TBL_FOLDER AS FOLDER
  ON CONT.FOLDER=FOLDER.ID
WHERE DEL = 'N' AND MATCH(TAG) AGAINST('${tag}') AND REPORT < 10 AND EXPOSE>=(
  SELECT CASE
    WHEN :MID=CONT.ID_WRITER THEN 0
    WHEN (
      SELECT ID_FRIEND FROM publixher.TBL_FRIENDS WHERE ID_USER=:MID2 AND ID_FRIEND=CONT.ID_WRITER
    ) IS NOT NULL
)";
        if ($category !== null) {
            $sql .= " AND CATEGORY=:CATEOGORY";
            $parameter['CATEGORY'] = $category;
            if ($sub_category !== null) {
                $sql .= " AND SUB_CATEGORY=:SUB_CATEGORY";
                $parameter['SUB_CATEGORY'] = $sub_category;
            }
        }
        $sql .= " ORDER BY WRITE_DATE DESC
            LIMIT :NOWPAGE,10";

        $prepare = $this->db->prepare($sql);
        $prepare->execute($parameter);
        $result=$prepare->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}