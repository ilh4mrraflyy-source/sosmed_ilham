<?php
$server   = "localhost";
$username = "root";
$password = "rpl12345"; // sesuai konfigurasi kamu
$database = "db_medsos";

$koneksi = mysqli_connect($server, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>