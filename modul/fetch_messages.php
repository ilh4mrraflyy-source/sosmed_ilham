<?php
session_start();
$conn = mysqli_connect("localhost", "root", "rpl12345", "db_medsos");
if (!$conn) die("Koneksi gagal");

if (!isset($_SESSION['user'])) exit;

$user = $_SESSION['user']['Username'];
$friend = $_GET['user'] ?? '';

if ($friend === '') exit;

$q = "
SELECT * FROM tbl_messages 
WHERE (FromUser='$user' AND ToUser='$friend') OR (FromUser='$friend' AND ToUser='$user')
ORDER BY SentAt ASC
";
$res = mysqli_query($conn, $q);

while ($r = mysqli_fetch_assoc($res)) {
  $isMine = ($r['FromUser'] === $user);
  $align = $isMine ? 'text-end' : 'text-start';
  $color = $isMine ? 'bg-primary text-white' : 'bg-light';
  echo "<div class='$align mb-1'><span class='d-inline-block px-2 py-1 rounded $color'>{$r['Message']}</span></div>";
}
?>