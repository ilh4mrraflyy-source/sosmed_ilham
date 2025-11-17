<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "rpl12345";
$db   = "db_medsos";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) exit;

if (isset($_SESSION['user'])) {
  $username = $_SESSION['user']['Username'];
  mysqli_query($conn, "UPDATE notif SET IsRead=1 WHERE ToUser='$username'");
}
?>