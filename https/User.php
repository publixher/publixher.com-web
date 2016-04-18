<?php

/**
 * Created by PhpStorm.
 * User: gangdong-gyun
 * Date: 2016. 2. 13.
 * Time: 오후 9:09
 */
class User
{
    private $EMAIL;
    private $PASSWORD;
    private $USER_NAME;
    private $SEX;
    private $BIRTH;
    private $REGION;
    private $H_SCHOOL;
    private $UNIV;
    private $PIC;
    private $JOIN_DATE;
    private $IS_NICK;
    private $TOP_CONTENT;
    private $IN_USE;
    private $ID;
    private $WRITEAUTH;
    private $EXPAUTH;


    /**
     * User constructor.
     * @param $EMAIL
     * @param $PASSWORD
     * @param $USER_NAME
     * @param $SEX
     * @param $BIRTH
     * @param $REGION
     * @param $H_SCHOOL
     * @param $UNIV
     * @param $PIC
     * @param $JOIN_DATE
     * @param $IS_NICK
     * @param $TOP_CONTENT
     * @param $IN_USE
     * @param $ID
     */

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return mixed
     */
    public function getEMAIL()
    {
        return $this->EMAIL;
    }

    /**
     * @return mixed
     */
    public function getPASSWORD()
    {
        return $this->PASSWORD;
    }

    /**
     * @return mixed
     */
    public function getUSERNAME()
    {
        return $this->USER_NAME;
    }

    /**
     * @return mixed
     */
    public function getSEX()
    {
        return $this->SEX;
    }

    /**
     * @return mixed
     */
    public function getBIRTH()
    {
        return $this->BIRTH;
    }

    /**
     * @return mixed
     */
    public function getREGION()
    {
        return $this->REGION;
    }

    /**
     * @return mixed
     */
    public function getHSCHOOL()
    {
        return $this->H_SCHOOL;
    }

    /**
     * @return mixed
     */
    public function getUNIV()
    {
        return $this->UNIV;
    }

    /**
     * @return mixed
     */
    public function getPIC()
    {
        return $this->PIC;
    }

    /**
     * @return mixed
     */
    public function getJOINDATE()
    {
        return $this->JOIN_DATE;
    }

    /**
     * @return mixed
     */
    public function getISNICK()
    {
        return $this->IS_NICK;
    }

    /**
     * @return mixed
     */
    public function getTOPCONTENT()
    {
        return $this->TOP_CONTENT;
    }

    /**
     * @return mixed
     */
    public function getINUSE()
    {
        return $this->IN_USE;
    }

    /**
     * @return mixed
     */
    public function getWRITEAUTH()
    {
        return $this->WRITEAUTH;
    }


    /**
     * @return mixed
     */
    public function getEXPAUTH()
    {
        return $this->EXPAUTH;
    }


}