<?php
date_default_timezone_set('Asia/Phnom_Penh');

$host = "localhost";
$user = "root";
$pwd = "";
$database = "raksa_coffee";

$conn = new mysqli($host, $user, $pwd, $database);

if($conn->connect_error){
    die("Connection failed: ". $conn->connect_error);
}
?>