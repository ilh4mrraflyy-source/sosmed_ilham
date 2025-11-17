<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "rpl12345";
$db   = "db_medsos";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Koneksi gagal");

$toUser = $_SESSION['user']['Username'];
$fromUser = $_POST['from_user'] ?? '';

if (isset($_POST['confirm'])) {
  // Update status
  mysqli_query($conn, "UPDATE friend_requests SET Status='accepted' WHERE FromUser='$fromUser' AND ToUser='$toUser'");
  // Tambahkan ke tabel teman
  mysqli_query($conn, "INSERT INTO friends (UserA, UserB) VALUES ('$fromUser', '$toUser')");
  // Tambah notifikasi ke pengirim
  mysqli_query($conn, "INSERT INTO notif (FromUser, ToUser, Type, Message) VALUES ('$toUser', '$fromUser', 'friend_accept', 'menerima permintaan pertemanan kamu')");
  echo "<script>alert('Permintaan diterima!'); history.back();</script>";
}

if (isset($_POST['reject'])) {
  mysqli_query($conn, "UPDATE friend_requests SET Status='rejected' WHERE FromUser='$fromUser' AND ToUser='$toUser'");
  echo "<script>alert('Permintaan ditolak!'); history.back();</script>";
}
?>