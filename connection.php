<?php
DEFINE('DB_USER', 'studentdb');
DEFINE('DB_PASSWORD', 'Hellohello123');
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'blog');

$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
    or die('Could not connect to MySQL: ' .
        mysqli_connect_error());
?>