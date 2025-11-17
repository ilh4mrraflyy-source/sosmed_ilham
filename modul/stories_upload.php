<?php
session_start();
include "../lib/koneksi.php"; // sesuaikan path jika beda

if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit();
}

$username = $_SESSION['user']['Username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_FILES['story_img']) || $_FILES['story_img']['error'] !== 0) {
    echo "<script>alert('Pilih file gambar.');history.back();</script>";
    exit;
  }

  $file = $_FILES['story_img'];
  $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
  $allowed = ['jpg','jpeg','png','gif','webp'];
  if (!in_array(strtolower($ext), $allowed)) {
    echo "<script>alert('Format file tidak didukung.');history.back();</script>";
    exit;
  }

  $file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/','_',basename($file['name']));
  $target_dir = "../uploads/";
  if (!is_dir($target_dir)) mkdir($target_dir,0755,true);
  $target = $target_dir . $file_name;

  if (!move_uploaded_file($file['tmp_name'], $target)) {
    echo "<script>alert('Gagal mengunggah file.');history.back();</script>";
    exit;
  }

  $expires = date("Y-m-d H:i:s", strtotime("+24 hours"));
  $file_name_sql = mysqli_real_escape_string($koneksi, $file_name);
  $q = "INSERT INTO stories (UserName, ImageName, ExpiresAt) VALUES ('".$koneksi->real_escape_string($username)."', '$file_name_sql', '$expires')";
  if (!$koneksi->query($q)) {
    echo "<script>alert('Gagal menyimpan story.');history.back();</script>";
    exit;
  }

  header("Location: ../index.php");
  exit;
}
?>