<?php
$servername = "localhost";
$username = "NhatHau";
$password = "Nhathau@12345";
$dbname = "webthoitrang_moii";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn){
    die('Kết nối thất bại: ' . mysqli_connect_error());
}
?>