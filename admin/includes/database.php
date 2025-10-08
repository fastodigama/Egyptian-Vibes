<?php
// includes/config.php

$host = getenv("MYSQLHOST") ?: "127.0.0.1";
$port = getenv("MYSQLPORT") ?: 3306;
$db   = getenv("MYSQLDATABASE") ?: "egyptian_vibes";
$user = getenv("MYSQLUSER") ?: "root";
$pass = getenv("MYSQLPASSWORD") ?: "root";

$connect = new mysqli($host, $user, $pass, $db, $port);

if ($connect->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>