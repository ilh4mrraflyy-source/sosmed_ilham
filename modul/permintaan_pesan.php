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
  header("Location: ../login.php");
  exit();
}

$username = $_SESSION['user']['Username'];

// ===== Terima atau Tolak Pesan =====
if (isset($_POST['action'])) {
  $id = intval($_POST['id']);
  $action = $_POST['action'];

  if ($action === 'accept') {
    mysqli_query($conn, "UPDATE message_requests SET Status='accepted' WHERE ID=$id");
  } elseif ($action === 'reject') {
    mysqli_query($conn, "UPDATE message_requests SET Status='rejected' WHERE ID=$id");
  }
  header("Location: permintaan_pesan.php");
  exit();
}

// ===== Ambil Permintaan Pesan =====
$req = mysqli_query($conn, "
  SELECT * FROM message_requests 
  WHERE ToUser='$username' AND Status='pending' 
  ORDER BY DateSent DESC
");

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Permintaan Pesan - Facebook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f0f2f5;
        font-family: "Segoe UI", Roboto, Arial, sans-serif;
    }

    .navbar {
        background-color: #1877f2;
    }

    .navbar-brand,
    .nav-link {
        color: white !important;
        font-weight: 600;
    }

    .container {
        margin-top: 40px;
        max-width: 700px;
    }

    .request-card {
        background: #fff;
        border-radius: 10px;
        padding: 15px 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
        transition: transform 0.2s ease;
    }

    .request-card:hover {
        transform: translateY(-2px);
    }

    .sender {
        font-weight: 600;
        color: #1877f2;
    }

    .msg-preview {
        color: #444;
        margin-top: 5px;
    }

    .btn-accept {
        background: #1877f2;
        color: white;
    }

    .btn-reject {
        background: #ddd;
        color: #333;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../index.php">Facebook</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="../index.php" class="nav-link">Beranda</a></li>
                <li class="nav-item"><a href="pesan.php" class="nav-link">Pesan</a></li>
                <li class="nav-item"><a href="permintaan_pesan.php" class="nav-link">Permintaan Pesan</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link">Keluar</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h4 class="mb-4 text-center fw-bold text-primary">📩 Permintaan Pesan</h4>

        <?php if (mysqli_num_rows($req) > 0): ?>
        <?php while ($r = mysqli_fetch_assoc($req)): ?>
        <div class="request-card">
            <div class="sender">@<?= htmlspecialchars($r['FromUser']) ?></div>
            <div class="msg-preview"><?= nl2br(htmlspecialchars($r['MessageText'])) ?></div>
            <small class="text-muted">Dikirim pada <?= $r['DateSent'] ?></small>

            <form method="post" class="mt-3 d-flex gap-2">
                <input type="hidden" name="id" value="<?= $r['ID'] ?>">
                <button type="submit" name="action" value="accept" class="btn btn-accept btn-sm">
                    Terima
                </button>
                <button type="submit" name="action" value="reject" class="btn btn-reject btn-sm">
                    Tolak
                </button>
            </form>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="text-center text-muted mt-4">Tidak ada permintaan pesan baru.</div>
        <?php endif; ?>
    </div>
</body>

</html>