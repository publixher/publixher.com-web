<?php
$m = new MongoClient();
$db = $m->selectDB('publixher');
$collections = $db->listCollections();
foreach ($collections as $collection) {
    echo "$collection : ";
    echo $collection->count() . '\n';
}
?>