<?php
function passing_time($datetime)
{
    $time_lag = time() - strtotime($datetime);
    if ($time_lag < 60) {
        $posting_time = "방금";
    } elseif ($time_lag >= 60 and $time_lag < 3600) {
        $posting_time = floor($time_lag / 60) . "분 전";
    } elseif ($time_lag >= 3600 and $time_lag < 86400) {
        $posting_time = floor($time_lag / 3600) . "시간 전";
    } else {
        $posting_time = date("m월d일", strtotime($datetime));
    }
    return $posting_time;
}

?>