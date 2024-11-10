<?php
// Connecting to DATABASE
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'php_crud';

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    die("connection failed". mysqli_connect_error());
}
else {
    // echo "connected";
}

?>