<?php
//밴 되있으면 글쓰기 제한
function banCheck($ID,$db,$status){
    $date = strtotime(date('Y-m-d H:i:s'));
    $getBan = $db->prepare("SELECT DATE_FORMAT(BAN,'%Y/%m/%d') FROM publixher.TBL_USER WHERE ID=:ID");
    $getBan->execute(array('ID' => $ID));
    $bandate_str = $getBan->fetchColumn();
    $bandate = strtotime($bandate_str);
    if ($bandate > $date) {
        echo json_encode(array('status' => $status, 'result' => array('BAN' => $bandate_str)), JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>