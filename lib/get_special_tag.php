<?php

function get_special_tag($str, $tagname) {

    $str = substr($str,strpos($str,'<'.$tagname));

    $tmparr = explode('</'.$tagname.'>', $str);

    return $tmparr[0].'</'.$tagname.'>';

}?>