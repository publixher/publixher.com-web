<?php

/**
 * Created by PhpStorm.
 * User: donggyun
 * Date: 2016. 6. 2.
 * Time: 오후 3:30
 */
class setCookie
{
    private $time;
    private $date;

    public function __construct()
    {
        $this->time=time();
        $this->date=date('Y-m-d H:i:s',$this->time);
    }

    public function setCid($id,$db){
        $cid=md5(sha1($this->date).$id);
        setcookie('cid', $cid, time() + 3600 * 24 * 365, '/');
        setcookie('mid',$id,time() + 3600 * 24 * 365, '/');
        $sql="UPDATE publixher.TBL_USER SET LAST_LOGIN=:LAST_LOGIN WHERE ID=:ID";
        $prepare=$db->prepare($sql);
        $prepare->execute(array('LAST_LOGIN'=>$this->date,'ID'=>$id));
        $prepare=null;
    }

    public function getCid($cid,$mid,$db){
        $sql="SELECT ID,LAST_LOGIN FROM publixher.TBL_USER WHERE ID=:ID";
        $prepare=$db->prepare($sql);
        $prepare->execute(array('ID'=>$mid));
        $r=$prepare->fetch(PDO::FETCH_ASSOC);
        $r_cid=md5(sha1($r['LAST_LOGIN']).$mid);
        $prepare=null;
        if($r_cid==$cid){
            return true;
        }else{
            return false;
        }
    }
}