<?php
$host = "localhost";
$user = "root"; // Ganti jika di hosting
$pass = "";     // Ganti jika di hosting
$db   = "simplecrm";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
