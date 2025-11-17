<?php
session_start();

// ====== Koneksi Database ======
$host = "localhost";
$user = "root";
$pass = "rpl12345";
$db   = "db_medsos";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// ====== Cek Login ======
if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit();
}

$username = $_SESSION['user']['Username'];

// ====== Ambil Notifikasi ======
$q = "SELECT * FROM notif WHERE ToUser='$username' ORDER BY NotifID DESC LIMIT 20";
$res = mysqli_query($conn, $q);
$notifData = [];
while ($r = mysqli_fetch_assoc($res)) $notifData[] = $r;

// ====== Hitung yang belum dibaca ======
$count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM notif WHERE ToUser='$username' AND IsRead=0"))['c'] ?? 0;

// ====== Mode JSON untuk AJAX (index.php) ======
if (isset($_GET['mode']) && $_GET['mode'] === 'json') {
  header('Content-Type: application/json');
  echo json_encode(["notif" => $notifData, "count" => $count]);
  exit;
}

/* ===============================
   🔹 Konfirmasi / Tolak Teman
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['confirm_friend'])) {
    $from_user = mysqli_real_escape_string($conn, $_POST['from_user']);
    mysqli_query($conn, "UPDATE friend_requests SET Status='accepted' WHERE FromUser='$from_user' AND ToUser='$username'");
    mysqli_query($conn, "INSERT INTO friends (UserA, UserB) VALUES ('$username', '$from_user')");
    mysqli_query($conn, "INSERT INTO notif (FromUser, ToUser, Type, Message) VALUES ('$username','$from_user','friend_accept','menerima permintaan pertemanan kamu')");
  }

  if (isset($_POST['reject_friend'])) {
    $from_user = mysqli_real_escape_string($conn, $_POST['from_user']);
    mysqli_query($conn, "UPDATE friend_requests SET Status='rejected' WHERE FromUser='$from_user' AND ToUser='$username'");
  }
  header("Location: notifikasi.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Notifikasi - Facebook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/2c6b57f94d.js" crossorigin="anonymous"></script>
    <style>
    body {
        background-color: #f0f2f5;
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .navbar {
        background-color: #1877f2;
    }

    .navbar-brand {
        font-weight: bold;
        color: white !important;
    }

    .notif-container {
        max-width: 750px;
        margin: 50px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 25px;
    }

    .notif-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1877f2;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .notif-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: background 0.3s;
        text-decoration: none;
        color: #050505;
    }

    .notif-item:hover {
        background: #e9f3ff;
    }

    .notif-item.unread {
        background: #e7f3ff;
        border-left: 4px solid #1877f2;
    }

    .notif-item img {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #1877f2;
    }

    .notif-text {
        flex: 1;
        line-height: 1.3;
    }

    .notif-time {
        font-size: 13px;
        color: #666;
        margin-top: 3px;
    }

    .notif-type {
        font-size: 20px;
    }

    .badge-notif {
        position: absolute;
        top: 4px;
        right: 2px;
        background: red;
        color: white;
        font-size: 11px;
        border-radius: 50%;
        padding: 2px 5px;
    }

    .friend-action button {
        font-size: 13px;
        padding: 4px 10px;
        margin-right: 4px;
    }
    </style>
</head>

<body>
    <!-- 🔵 Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../index.php">Facebook</a>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item position-relative me-3">
                    <a href="#" class="nav-link text-white position-relative" id="notifIcon">
                        <i class="fa-solid fa-bell fs-5"></i>
                        <?php if ($count > 0): ?>
                        <span class="badge-notif"><?= $count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white fw-semibold bg-primary rounded-pill px-3 py-1">
                        <?= htmlspecialchars($username) ?>
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a href="../logout.php" class="nav-link text-light">Keluar</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- 🔔 Konten Notifikasi -->
    <div class="notif-container">
        <div class="notif-title"><i class="fa-solid fa-bell"></i> Notifikasi</div>

        <?php if (empty($notifData)): ?>
        <div class="text-center text-muted py-4">Tidak ada notifikasi baru</div>
        <?php else: ?>
        <?php foreach ($notifData as $n):
        $from = htmlspecialchars($n['FromUser']);
        $msg = htmlspecialchars($n['Message']);
        $date = date("d M Y H:i", strtotime($n['CreatedAt']));
        $photo = "../uploads/default.png";

        // Ambil foto pengirim
        $photoQ = mysqli_query($conn, "SELECT Photo FROM user WHERE UserName='$from'");
        if ($photoQ && mysqli_num_rows($photoQ) > 0) {
          $p = mysqli_fetch_assoc($photoQ);
          if (!empty($p['Photo']) && file_exists("../uploads/" . $p['Photo'])) {
            $photo = "../uploads/" . $p['Photo'];
          } else {
            $photo = "https://ui-avatars.com/api/?name=" . urlencode($from) . "&background=1877f2&color=fff";
          }
        }

        // Pilih ikon
        $icon = "<i class='fa-solid fa-bell text-secondary'></i>";
        if ($n['Type'] == "like") $icon = "<i class='fa-solid fa-thumbs-up text-primary'></i>";
        if ($n['Type'] == "comment") $icon = "<i class='fa-solid fa-comment text-success'></i>";
        if ($n['Type'] == "friend_request") $icon = "<i class='fa-solid fa-user-plus text-warning'></i>";
        if ($n['Type'] == "friend_accept") $icon = "<i class='fa-solid fa-user-check text-primary'></i>";
      ?>
        <div class="notif-item <?= $n['IsRead'] ? '' : 'unread' ?>">
            <img src="<?= $photo ?>" alt="<?= $from ?>">
            <div class="notif-text">
                <div><strong><?= $from ?></strong> <?= $msg ?></div>
                <div class="notif-time"><?= $date ?></div>

                <?php if ($n['Type'] == "friend_request"): ?>
                <form method="POST" class="friend-action mt-2">
                    <input type="hidden" name="from_user" value="<?= $from ?>">
                    <button type="submit" name="confirm_friend" class="btn btn-primary btn-sm">Konfirmasi</button>
                    <button type="submit" name="reject_friend" class="btn btn-outline-secondary btn-sm">Tolak</button>
                </form>
                <?php endif; ?>
            </div>
            <div class="notif-type"><?= $icon ?></div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
    // 🔸 Tandai semua notif sebagai dibaca saat halaman dibuka
    fetch("mark_read.php").catch(err => console.error(err));
    </script>
</body>

</html>