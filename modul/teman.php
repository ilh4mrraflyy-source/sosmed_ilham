<?php
session_start();

// ===== Koneksi Database =====
$host = "localhost";
$user = "root";
$pass = "rpl12345";
$db   = "db_medsos";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

// ===== Cek Login =====
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['user']['Username'];

// ===== Tambah atau Batalkan Pertemanan =====
if (isset($_GET['add'])) {
  $friend = $_GET['add'];

  // Cek apakah sudah difollow
  $check = mysqli_query($conn, "SELECT * FROM follow WHERE UserName='$username' AND FollowName='$friend'");
  if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO follow (UserName, FollowName) VALUES ('$username', '$friend')");
  }

  header("Location: teman.php");
  exit();
}

if (isset($_GET['cancel'])) {
  $friend = $_GET['cancel'];
  mysqli_query($conn, "DELETE FROM follow WHERE UserName='$username' AND FollowName='$friend'");
  header("Location: teman.php");
  exit();
}

// ===== Ambil Daftar User Lain =====
$q = "SELECT * FROM user WHERE UserName != '$username'";
$users = mysqli_query($conn, $q);

// Ambil daftar teman yang sudah difollow
$friends_q = mysqli_query($conn, "SELECT FollowName FROM follow WHERE UserName='$username'");
$friends = [];
while ($f = mysqli_fetch_assoc($friends_q)) {
  $friends[] = $f['FollowName'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Teman - Facebook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f0f2f5;
        font-family: "Segoe UI", Arial, sans-serif;
    }

    .navbar {
        background-color: #1877f2;
    }

    .navbar-brand,
    .nav-link {
        color: white !important;
        font-weight: 600;
    }

    .container-main {
        margin-top: 30px;
    }

    .friend-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .friend-card img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .btn-add {
        background-color: #1877f2;
        border: none;
        color: white;
        font-weight: 600;
        border-radius: 6px;
        padding: 5px 15px;
    }

    .btn-add:hover {
        background-color: #166fe5;
    }

    .btn-cancel {
        background-color: #e4e6eb;
        color: #050505;
        border: none;
        font-weight: 600;
        border-radius: 6px;
        padding: 5px 15px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.php">Facebook</a>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a href="../index.php" class="nav-link">Beranda</a></li>
                <li class="nav-item"><a href="teman.php" class="nav-link">Teman</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">Keluar</a></li>
            </ul>
        </div>
    </nav>

    <div class="container container-main">
        <h4 class="mb-4 fw-semibold">Temukan Teman</h4>

        <?php while ($row = mysqli_fetch_assoc($users)): ?>
        <div class="friend-card d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="https://ui-avatars.com/api/?name=<?= htmlspecialchars($row['UserName']) ?>&background=1877f2&color=fff"
                    alt="profil">
                <div>
                    <h6 class="mb-1"><?= htmlspecialchars($row['UserName']) ?></h6>
                    <small><?= htmlspecialchars($row['Email'] ?? '') ?></small>
                </div>
            </div>
            <div>
                <?php if (in_array($row['UserName'], $friends)): ?>
                <a href="?cancel=<?= urlencode($row['UserName']) ?>" class="btn btn-cancel">Batalkan</a>
                <?php else: ?>
                <a href="?add=<?= urlencode($row['UserName']) ?>" class="btn btn-add">Tambahkan Teman</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</body>

</html>