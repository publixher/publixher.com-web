<?php
session_start();
$_SESSION=array();
if(isset($_COOKIE[session_name()])){
    setcookie(session_name(),'',time()-3600,'/');
}
session_destroy();
foreach($_COOKIE as $key=>$val){
    setCookie($key,'',time()-3600,"/");
}
?>
<meta http-equiv='refresh' content='0;url=/https/login.php'>