<?php
session_start();
include "../lib/koneksi.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
  echo json_encode(['ok'=>false]);
  exit;
}

$username = $_SESSION['user']['Username'];
$storyID = intval($_POST['storyID'] ?? 0);

if ($storyID <= 0) {
  echo json_encode(['ok'=>false]);
  exit;
}

// cek sudah ada view?
$chk = mysqli_query($koneksi, "SELECT * FROM story_views WHERE StoryID=$storyID AND Viewer='".$koneksi->real_escape_string($username)."'");
if (mysqli_num_rows($chk) == 0) {
  mysqli_query($koneksi, "INSERT INTO story_views (StoryID, Viewer) VALUES ($storyID, '".$koneksi->real_escape_string($username)."')");
}

echo json_encode(['ok'=>true]);