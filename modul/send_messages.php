<?php
session_start();
$conn = mysqli_connect("localhost", "root", "rpl12345", "db_medsos");
if (!$conn) die("Koneksi gagal");

if (!isset($_SESSION['user'])) exit;

$from = $_SESSION['user']['Username'];
$to = mysqli_real_escape_string($conn, $_POST['to']);
$message = mysqli_real_escape_string($conn, $_POST['message']);

if ($message !== '' && $to !== '') {
  mysqli_query($conn, "INSERT INTO tbl_messages (FromUser, ToUser, Message) VALUES ('$from', '$to', '$message')");
}
?>