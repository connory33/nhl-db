<?php 
$servername = "connoryoung.com";
$username = "connor";
$password = "PatrickRoy33";
$dbname = "NHL API";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (mysqli_connect_error()) {
    die("Connection failed: " . mysqli_connect_error());
}

header('Content-Type: text/html; charset=utf-8');

mysqli_set_charset($conn, "utf8");

// Railway connection settings

// Database connection parameters
// $host = "yamanote.proxy.rlwy.net"; // Railway database host
// $port = "3306"; // Railway database port
// $username = "root"; // Railway database username
// $password = "MizuWdXKgWyBUpNrUcQGShdCSGtgdhOT"; // Railway database password
// $dbname = "railway"; // Railway database name

// // Create connection
// $conn = mysqli_connect($host, $username, $password, $dbname, $port);

// // Check connection
// if (mysqli_connect_error()) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// echo "Connected to the database successfully!";
// ?>
