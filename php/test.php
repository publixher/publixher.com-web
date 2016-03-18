<?php
if ( !empty($_POST) ) {
    $user = $_POST['user'];
    $password = $_POST['password'];

    // PDO 연결
    $config['dbconnect'] = array(
        'host'          => 'localhost',
        'dbname'     => 'dbname',
        'username'  => 'username',
        'password'   => 'password'
    );
    try {
        $dbh = new PDO(
            'mysql:host='.$config['dbconnect']['host'].'; dbname='.$config['dbconnect']['dbname'], $config['dbconnect']['username'],
            $config['dbconnect']['password']);
    } catch (PDOException $e) {
        $e->getMessage();
    }
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);

    $sth = $dbh->query("SELECT username, password, id FROM users WHERE username='$user' ");

    foreach ( $sth->fetchAll() as $row ) {
        if (md5($password) ===  $row['password']) {
            // jQuery.post를 사용할 때 아래와 같이
            echo json_encode($row);
            // jQuery.ajax를 사용할 때 아래
            // echo $row['id'];
        } else {
            exit;
        }
    }
} else {
    exit;
}