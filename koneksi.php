<?php
$host = "db";        // bukan localhost
$user = "user";
$pass = "password";
$db   = "dbinputpelanggan";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
