<?php
function iframe_crop($body){
    $iframeRex="/<iframe[^>]width=[\"']?([^>\"']+)[\"']?[^>]height=[\"']?([^>\"']+)[\"']?[^>]src=[\"']?([^>\"']+)[\"']?[^>]*><\/iframe>/i";
    preg_match_all($iframeRex,$body,$iframes,PREG_OFFSET_CAPTURE);
    $iframecount=count($iframes[0]);
    for($i=0;$i<$iframecount;$i++){
        $width = (int)$iframes[1][$i][0];
        $height=(int)$iframes[2][$i][0];
        $wOffset=$iframes[1][$i][1];
        $hOffset=$iframes[2][$i][1];
        $wLen=strlen($iframes[1][$i][0]);
        $hLen=strlen($iframes[2][$i][0]);
        $outWidth=510;
        $outHeight=($outWidth/$width)*$height;
        $OffsetInc=strlen((string)$outWidth)-$wLen+strlen((string)$outHeight)-$hLen;
        $body=substr_replace($body,(string)$outWidth,$wOffset,$wLen);
        $body=substr_replace($body,(string)$outHeight,$hOffset,$hLen);
        for($j=$i+1;$j<$iframecount;$j++){
            $iframes[1][$j][1]+=$OffsetInc;
            $iframes[2][$j][1]+=$OffsetInc;
        }
    }
    return $body;
}
?>