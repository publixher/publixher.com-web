<?php
$unique = substr(base64_encode(mt_rand()), 0, 15);
echo $unique;
?>