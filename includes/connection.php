<?php

$servername = "LOCALHOST";
$username = "root";
$password = "";
$db = "pingpongbuffetdb";


//create connection

$conn = mysqli_connect($servername, $username, $password, $db);

if (!$conn) {
    die('connection failed' . mysqli_connect_error());
}
