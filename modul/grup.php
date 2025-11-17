<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "rpl12345";
$db   = "db_medsos";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit();
}

$username = $_SESSION['user']['Username'];

// ====== Buat Grup ======
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_name'])) {
  $name = mysqli_real_escape_string($conn, $_POST['group_name']);
  $desc = mysqli_real_escape_string($conn, $_POST['group_desc']);
  $photo = '';

  if (!empty($_FILES['group_photo']['name'])) {
    $file_name = time() . "_" . basename($_FILES["group_photo"]["name"]);
    $target = "../uploads/" . $file_name;
    move_uploaded_file($_FILES["group_photo"]["tmp_name"], $target);
    $photo = $file_name;
  }

  mysqli_query($conn, "
    INSERT INTO tbl_groups (GroupName, Description, GroupPhoto, CreatedBy)
    VALUES ('$name', '$desc', '$photo', '$username')
  ");
  $group_id = mysqli_insert_id($conn);
  mysqli_query($conn, "INSERT INTO tbl_group_members (GroupID, Username) VALUES ($group_id, '$username')");
  header("Location: grup.php");
  exit();
}

// ====== Ambil Grup ======
$groups = mysqli_query($conn, "
  SELECT g.*, 
  (SELECT COUNT(*) FROM tbl_group_members m WHERE m.GroupID = g.GroupID) AS memberCount,
  (SELECT COUNT(*) FROM tbl_group_members m WHERE m.GroupID = g.GroupID AND m.Username='$username') AS isJoined
  FROM tbl_groups g ORDER BY g.CreatedAt DESC
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Grup - Facebook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">
    <style>
    body {
        background-color: #f0f2f5;
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .navbar {
        background-color: #1877f2;
    }

    .navbar-brand {
        color: white !important;
        font-weight: 700;
    }

    .container-main {
        margin-top: 30px;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        transition: 0.2s ease;
    }

    .card:hover {
        transform: translateY(-3px);
    }

    .group-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .btn-create {
        background-color: #1877f2;
        color: white;
        border-radius: 8px;
    }

    .btn-create:hover {
        background-color: #166fe5;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../index.php">Facebook</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="../index.php" class="nav-link text-white">Beranda</a></li>
                <li class="nav-item"><a href="teman.php" class="nav-link text-white">Teman</a></li>
                <li class="nav-item"><a href="pesan.php" class="nav-link text-white">Pesan</a></li>
                <li class="nav-item"><a href="grup.php" class="nav-link text-white fw-bold">Grup</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link text-white">Keluar</a></li>
            </ul>
        </div>
    </nav>

    <div class="container container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Semua Grup</h4>
            <button class="btn btn-create" data-bs-toggle="modal" data-bs-target="#buatGrupModal">
                <i class="fa-solid fa-plus"></i> Buat Grup
            </button>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($groups) > 0): ?>
            <?php while ($g = mysqli_fetch_assoc($groups)): 
          $photo = !empty($g['GroupPhoto']) && file_exists('../uploads/'.$g['GroupPhoto'])
            ? '../uploads/'.$g['GroupPhoto']
            : 'https://source.unsplash.com/400x200/?community,group';
        ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="<?= htmlspecialchars($photo) ?>" class="group-img" alt="Grup">
                    <div class="card-body">
                        <h5 class="card-title mb-1"><?= htmlspecialchars($g['GroupName']) ?></h5>
                        <small class="text-muted"><?= $g['memberCount'] ?> anggota</small>
                        <p class="mt-2 text-secondary"><?= htmlspecialchars(substr($g['Description'],0,80)) ?>...</p>
                        <?php if ($g['isJoined'] > 0): ?>
                        <a href="grup_detail.php?id=<?= $g['GroupID'] ?>" class="btn btn-primary btn-sm w-100">Lihat
                            Grup</a>
                        <?php else: ?>
                        <a href="grup_detail.php?id=<?= $g['GroupID'] ?>&join=1"
                            class="btn btn-success btn-sm w-100">Gabung Grup</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p class="text-muted">Belum ada grup.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Buat Grup -->
    <div class="modal fade" id="buatGrupModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Buat Grup Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Grup</label>
                        <input type="text" name="group_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="group_desc" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Grup</label>
                        <input type="file" name="group_photo" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Buat</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>