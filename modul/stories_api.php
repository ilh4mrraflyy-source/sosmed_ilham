<?php
session_start();
header('Content-Type: application/json');
include "../lib/koneksi.php";

if (!isset($_SESSION['user'])) {
  echo json_encode([]);
  exit;
}

$username = $_SESSION['user']['Username'];

// ambil stories aktif (belum expired), kelompokkan per user (ambil stories terbaru per user)
$q = "
SELECT s.StoryID, s.UserName, s.ImageName, s.CreatedAt, s.ExpiresAt,
       u.Photo as UserPhoto
FROM stories s
LEFT JOIN user u ON u.UserName = s.UserName
WHERE s.ExpiresAt > NOW()
ORDER BY s.CreatedAt DESC
";

$res = mysqli_query($koneksi, $q);
$byUser = [];

// kumpulkan per user
while ($r = mysqli_fetch_assoc($res)) {
  $user = $r['UserName'];
  if (!isset($byUser[$user])) $byUser[$user] = ['user'=>$user, 'photo'=>$r['UserPhoto'], 'items'=>[]];
  $byUser[$user]['items'][] = $r;
}

// convert ke array urut
$output = array_values($byUser);
echo json_encode($output);