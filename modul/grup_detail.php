<?php
session_start();
$conn = mysqli_connect("localhost", "root", "rpl12345", "db_medsos");
if (!$conn) die("❌ Koneksi gagal ke database: " . mysqli_connect_error());

if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit();
}

$username = $_SESSION['user']['Username'];
$groupID = intval($_GET['id'] ?? 0);

// ===== CEK DATA GRUP =====
$res = mysqli_query($conn, "SELECT * FROM tbl_groups WHERE GroupID = $groupID");
if (!$res) {
  die("❌ Query error (tbl_groups): " . mysqli_error($conn));
}
$group = mysqli_fetch_assoc($res);

if (!$group) {
  echo "<script>alert('Grup tidak ditemukan!');location='grup.php';</script>";
  exit();
}

// ===== JOIN GRUP =====
if (isset($_GET['join'])) {
  $check = mysqli_query($conn, "SELECT * FROM tbl_group_members WHERE GroupID=$groupID AND Username='$username'");
  if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO tbl_group_members (GroupID, Username) VALUES ($groupID, '$username')");
  }
  header("Location: grup_detail.php?id=$groupID");
  exit();
}

// ===== KELUAR GRUP =====
if (isset($_GET['leave'])) {
  mysqli_query($conn, "DELETE FROM tbl_group_members WHERE GroupID=$groupID AND Username='$username'");
  header("Location: grup.php");
  exit();
}

// ===== CEK KEANGGOTAAN =====
$isMember = mysqli_num_rows(mysqli_query($conn, "
  SELECT * FROM tbl_group_members 
  WHERE GroupID=$groupID AND Username='$username'
")) > 0;

// ===== POSTING DI GRUP =====
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_text'])) {
  $text = mysqli_real_escape_string($conn, $_POST['post_text']);
  $img = '';
  if (!empty($_FILES['post_img']['name'])) {
    $file_name = time() . "_" . basename($_FILES["post_img"]["name"]);
    $target = "../uploads/" . $file_name;
    if (move_uploaded_file($_FILES["post_img"]["tmp_name"], $target)) {
      $img = $file_name;
    }
  }

  $insert = mysqli_query($conn, "
    INSERT INTO tbl_group_posts (GroupID, Username, Content, ImageName, CreatedAt)
    VALUES ($groupID, '$username', '$text', '$img', NOW())
  ");

  if (!$insert) {
    die("❌ Gagal menambah posting: " . mysqli_error($conn));
  }

  header("Location: grup_detail.php?id=$groupID");
  exit();
}

// ===== AMBIL POSTINGAN GRUP =====
$postRes = mysqli_query($conn, "
  SELECT * FROM tbl_group_posts 
  WHERE GroupID=$groupID 
  ORDER BY CreatedAt DESC
");
if (!$postRes) {
  die("❌ Query error (tbl_group_posts): " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($group['GroupName']) ?> - Grup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f0f2f5;
        font-family: "Segoe UI", sans-serif;
    }

    .navbar {
        background: #1877f2;
    }

    .card {
        border-radius: 10px;
    }

    .post-img {
        max-height: 300px;
        object-fit: cover;
        border-radius: 8px;
    }

    .post-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    </style>
</head>

<body>
    <!-- 🔹 NAVBAR -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid px-4">
            <a href="grup.php" class="navbar-brand">← Kembali</a>
            <span class="text-white fw-semibold"><?= htmlspecialchars($group['GroupName']) ?></span>
            <a href="../logout.php" class="text-white">Keluar</a>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <!-- 🔹 INFO GRUP -->
        <div class="card mb-4">
            <img src="<?= !empty($group['GroupPhoto']) && file_exists('../uploads/'.$group['GroupPhoto']) 
              ? '../uploads/'.$group['GroupPhoto'] 
              : 'https://source.unsplash.com/800x300/?community,group' ?>" class="card-img-top"
                style="height:250px;object-fit:cover;">
            <div class="card-body">
                <h4 class="fw-bold"><?= htmlspecialchars($group['GroupName']) ?></h4>
                <p class="text-muted"><?= htmlspecialchars($group['Description']) ?></p>

                <?php if ($isMember): ?>
                <a href="?id=<?= $groupID ?>&leave=1" class="btn btn-outline-danger btn-sm">Keluar Grup</a>
                <?php else: ?>
                <a href="?id=<?= $groupID ?>&join=1" class="btn btn-success btn-sm">Gabung Grup</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- 🔹 FORM POSTING -->
        <?php if ($isMember): ?>
        <div class="card p-3 mb-3">
            <form method="POST" enctype="multipart/form-data">
                <textarea name="post_text" class="form-control mb-2" placeholder="Tulis sesuatu di grup ini..."
                    required></textarea>
                <div class="d-flex justify-content-between align-items-center">
                    <input type="file" name="post_img" class="form-control w-50">
                    <button type="submit" class="btn btn-primary">Posting</button>
                </div>
            </form>
        </div>

        <!-- 🔹 LIST POSTING -->
        <?php while ($p = mysqli_fetch_assoc($postRes)): ?>
        <div class="post-card p-3 mb-3">
            <h6 class="fw-semibold"><?= htmlspecialchars($p['Username']) ?></h6>
            <p><?= nl2br(htmlspecialchars($p['Content'])) ?></p>
            <?php if (!empty($p['ImageName'])): ?>
            <img src="../uploads/<?= htmlspecialchars($p['ImageName']) ?>" class="img-fluid post-img mb-2">
            <?php endif; ?>
            <small class="text-muted"><?= htmlspecialchars($p['CreatedAt']) ?></small>
        </div>
        <?php endwhile; ?>

        <?php else: ?>
        <div class="alert alert-info">
            Gabung ke grup ini untuk melihat dan membuat postingan.
        </div>
        <?php endif; ?>
    </div>
</body>

</html>