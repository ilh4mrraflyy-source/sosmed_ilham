<?php
session_start();
include "../lib/koneksi.php";

// cek login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$login_user = $_SESSION['user']['Username'];
$profile_user = isset($_GET['user']) ? mysqli_real_escape_string($koneksi, $_GET['user']) : $login_user;

// ambil data profil
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE UserName='$profile_user'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    echo "<script>alert('User tidak ditemukan!');window.location='../index.php';</script>";
    exit();
}

// upload foto profil
if (isset($_POST['upload_foto']) && isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

    $file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES["foto"]["name"]));
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        $file_name_sql = mysqli_real_escape_string($koneksi, $file_name);
        mysqli_query($koneksi, "UPDATE user SET Photo='$file_name_sql' WHERE UserName='" . mysqli_real_escape_string($koneksi, $login_user) . "'");
        echo "<script>alert('Foto profil berhasil diperbarui!');window.location='profil.php?user=$login_user';</script>";
        exit();
    }
}

// upload cover/sampul
if (isset($_POST['upload_cover']) && isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

    $file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES["cover"]["name"]));
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["cover"]["tmp_name"], $target_file)) {
        $file_name_sql = mysqli_real_escape_string($koneksi, $file_name);
        mysqli_query($koneksi, "UPDATE user SET Cover='$file_name_sql' WHERE UserName='" . mysqli_real_escape_string($koneksi, $login_user) . "'");
        echo "<script>alert('Foto sampul berhasil diperbarui!');window.location='profil.php?user=$login_user';</script>";
        exit();
    }
}

/* ======================================================
   =============== FITUR PERTEMANAN ======================
   ====================================================== */

// tambah teman (kirim permintaan)
if (isset($_GET['add_friend'])) {
    $friend = mysqli_real_escape_string($koneksi, $_GET['add_friend']);
    if ($friend !== $login_user) {
        $check = mysqli_query($koneksi, "SELECT * FROM friend_requests WHERE FromUser='$login_user' AND ToUser='$friend' AND Status='pending'");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($koneksi, "INSERT INTO friend_requests (FromUser, ToUser, Status) VALUES ('$login_user','$friend','pending')");
            mysqli_query($koneksi, "INSERT INTO notif (FromUser, ToUser, Type, Message) VALUES ('$login_user','$friend','friend_request','mengirim permintaan pertemanan')");
        }
    }
    echo "<script>alert('Permintaan pertemanan dikirim!');window.location='profil.php?user=$friend';</script>";
    exit();
}

// konfirmasi teman
if (isset($_POST['confirm_friend'])) {
    $from_user = mysqli_real_escape_string($koneksi, $_POST['from_user']);
    mysqli_query($koneksi, "UPDATE friend_requests SET Status='accepted' WHERE FromUser='$from_user' AND ToUser='$login_user'");
    mysqli_query($koneksi, "INSERT INTO friends (UserA, UserB) VALUES ('$login_user', '$from_user')");
    mysqli_query($koneksi, "INSERT INTO notif (FromUser, ToUser, Type, Message) VALUES ('$login_user','$from_user','friend_accept','menerima permintaan pertemanan kamu')");
    echo "<script>alert('Permintaan pertemanan diterima!');window.location='profil.php?user=$from_user';</script>";
    exit();
}

// tolak teman
if (isset($_POST['reject_friend'])) {
    $from_user = mysqli_real_escape_string($koneksi, $_POST['from_user']);
    mysqli_query($koneksi, "UPDATE friend_requests SET Status='rejected' WHERE FromUser='$from_user' AND ToUser='$login_user'");
    echo "<script>alert('Permintaan pertemanan ditolak.');window.location='profil.php?user=$from_user';</script>";
    exit();
}

// cek status pertemanan
$isFriend = mysqli_num_rows(mysqli_query($koneksi, "
    SELECT * FROM friends 
    WHERE (UserA='$login_user' AND UserB='$profile_user') OR (UserA='$profile_user' AND UserB='$login_user')
")) > 0;

$isRequested = mysqli_num_rows(mysqli_query($koneksi, "
    SELECT * FROM friend_requests WHERE FromUser='$login_user' AND ToUser='$profile_user' AND Status='pending'
")) > 0;

$isIncomingRequest = mysqli_num_rows(mysqli_query($koneksi, "
    SELECT * FROM friend_requests WHERE FromUser='$profile_user' AND ToUser='$login_user' AND Status='pending'
")) > 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Profil - <?= htmlspecialchars($user['UserName']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f0f2f5;
        font-family: "Segoe UI", Roboto, Arial, sans-serif;
    }

    .navbar {
        background-color: #1877f2;
    }

    .navbar-brand {
        color: #fff !important;
        font-weight: 700;
    }

    /* ✅ COVER IMAGE ANIMATION */
    .cover {
        width: 100%;
        height: 320px;
        background-position: center;
        background-size: cover;
        position: relative;
        border-bottom: 1px solid #ddd;
        transition: transform 0.4s ease, filter 0.4s ease;
    }

    .cover:hover {
        transform: scale(1.02);
        filter: brightness(92%);
        cursor: pointer;
    }

    /* ✅ PROFILE IMAGE ANIMATION */
    .profile-photo {
        position: absolute;
        bottom: -65px;
        left: 50%;
        transform: translateX(-50%);
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid #fff;
        object-fit: cover;
        background: #ccc;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .profile-photo:hover {
        transform: translateX(-50%) scale(1.08);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.3);
        cursor: pointer;
    }

    /* efek klik halus */
    .profile-photo:active,
    .cover:active {
        transform: scale(0.98);
        filter: brightness(85%);
    }

    .profile-section {
        text-align: center;
        margin-top: 90px;
    }

    .profile-nav {
        background: #fff;
        border-radius: 8px;
        margin: 15px auto;
        width: 90%;
        padding: 10px;
        display: flex;
        justify-content: center;
        gap: 30px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .profile-nav a {
        color: #1877f2;
        font-weight: 500;
        text-decoration: none;
    }

    .profile-nav a:hover {
        text-decoration: underline;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../index.php">Fesbuker</a>
            <div class="d-flex">
                <a href="../index.php" class="btn btn-light btn-sm me-2">Beranda</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Keluar</a>
            </div>
        </div>
    </nav>

    <?php
    $cover_url = $user['Cover'] && file_exists('../uploads/' . $user['Cover']) ? '../uploads/' . $user['Cover'] : 'https://source.unsplash.com/1600x900/?landscape';
    $photo_url = $user['Photo'] && file_exists('../uploads/' . $user['Photo']) ? '../uploads/' . $user['Photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['UserName']) . '&background=1877f2&color=fff';
    ?>
    <div class="cover" style="background-image:url('<?= htmlspecialchars($cover_url) ?>')">
        <?php if ($profile_user == $login_user): ?>
        <div class="cover-overlay p-2" style="position:absolute;right:20px;bottom:20px;">
            <form method="POST" enctype="multipart/form-data">
                <label class="btn btn-light btn-sm">
                    📷 Ubah Sampul
                    <input type="file" name="cover" hidden onchange="this.form.submit()">
                    <input type="hidden" name="upload_cover" value="1">
                </label>
            </form>
        </div>
        <?php endif; ?>
        <img src="<?= htmlspecialchars($photo_url) ?>" class="profile-photo" alt="foto profil">
    </div>

    <div class="profile-section">
        <h4><?= htmlspecialchars($user['UserName']) ?></h4>
        <?php if ($profile_user == $login_user): ?>
        <form method="POST" enctype="multipart/form-data" class="mt-2">
            <label class="btn btn-light btn-sm">
                📷 Ganti Foto Profil
                <input type="file" name="foto" hidden onchange="this.form.submit()">
                <input type="hidden" name="upload_foto" value="1">
            </label>
        </form>
        <?php else: ?>
        <?php if ($isFriend): ?>
        <button class="btn btn-success btn-sm mt-2" disabled>✅ Sudah Berteman</button>
        <?php elseif ($isRequested): ?>
        <button class="btn btn-secondary btn-sm mt-2" disabled>⏳ Menunggu Konfirmasi</button>
        <?php elseif ($isIncomingRequest): ?>
        <form method="POST" class="d-inline">
            <input type="hidden" name="from_user" value="<?= htmlspecialchars($profile_user) ?>">
            <button type="submit" name="confirm_friend" class="btn btn-primary btn-sm mt-2">✅ Konfirmasi</button>
            <button type="submit" name="reject_friend" class="btn btn-outline-secondary btn-sm mt-2">Tolak</button>
        </form>
        <?php else: ?>
        <a href="?user=<?= urlencode($profile_user) ?>&add_friend=<?= urlencode($profile_user) ?>"
            class="btn btn-primary btn-sm mt-2">+ Tambah Teman</a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="profile-nav mt-3">
        <a href="#">Postingan</a>
        <a href="#">Tentang</a>
        <a href="#">Teman</a>
        <a href="#">Foto</a>
    </div>

    <div class="container mt-3">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card p-3">
                    <h6 class="fw-bold mb-3">Tentang Saya</h6>
                    <p><strong>Nama:</strong> <?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['UserName']) ?></p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-3">
                    <h6 class="fw-bold mb-3">📝 Postingan</h6>
                    <?php
                    $sql = "
                    SELECT p.PostID, p.Text, p.Date, p.Time, p.UserName, i.ImageName
                    FROM post p
                    LEFT JOIN detailpost dp ON p.PostID = dp.PostID
                    LEFT JOIN image i ON dp.ImageID = i.ImageID
                    WHERE p.UserName = '" . mysqli_real_escape_string($koneksi, $profile_user) . "'
                    ORDER BY p.PostID DESC";
                    $posts = mysqli_query($koneksi, $sql);

                    if ($posts && mysqli_num_rows($posts) > 0) {
                        while ($p = mysqli_fetch_assoc($posts)) {
                            echo "<div class='border-bottom pb-3 mb-3'>";
                            echo "<p>" . nl2br(htmlspecialchars($p['Text'])) . "</p>";
                            echo "<small class='text-muted'>" . htmlspecialchars($p['Date']) . " pukul " . htmlspecialchars($p['Time']) . "</small>";
                            if (!empty($p['ImageName']) && file_exists('../uploads/' . $p['ImageName'])) {
                                echo "<div class='mt-2'><img src='../uploads/" . htmlspecialchars($p['ImageName']) . "' class='img-fluid rounded shadow-sm'></div>";
                            }
                            echo "</div>";
                        }
                    } else {
                        echo "<p class='text-muted'>Belum ada postingan.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>