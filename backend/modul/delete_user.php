<?php
include "../lib/koneksi.php";
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil username yang akan dihapus
if (isset($_GET['UserName'])) {
    $UserName = $_GET['UserName'];

    // Hapus data dari database
    $hapus = mysqli_query($koneksi, "DELETE FROM user WHERE UserName='$UserName'");

    if ($hapus) {
        echo "<script>alert('User berhasil dihapus!');window.location='index.php?page=user';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user.');window.location='index.php?page=user';</script>";
    }
} else {
    header("Location: index.php?page=user");
}
?>