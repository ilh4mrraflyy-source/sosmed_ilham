<?php
session_start();
include "../lib/koneksi.php"; // gunakan koneksi dari luar folder backend

// ===== CEK LOGIN =====
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['user']['Username'];

// ===== LOGOUT =====
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// ===== LOAD HALAMAN =====
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= htmlspecialchars($username) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f5f6f8;
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .sidebar {
        width: 250px;
        height: 100vh;
        background: #1877f2;
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        padding-top: 20px;
    }

    .sidebar h4 {
        text-align: center;
        font-weight: 700;
        margin-bottom: 30px;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        padding: 12px 25px;
        display: block;
        font-weight: 500;
        transition: 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: #145db8;
        border-left: 4px solid #fff;
    }

    .content {
        margin-left: 250px;
        padding: 30px;
    }

    .topbar {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .logout-btn {
        background: #dc3545;
        border: none;
        color: white;
        padding: 6px 15px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
    }

    .logout-btn:hover {
        background: #b02a37;
        color: white;
    }

    .card-custom {
        border: none;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-edit {
        background: #ffc107;
        color: black;
        border: none;
    }

    .btn-edit:hover {
        background: #e0a800;
        color: black;
    }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a href="?page=home" class="<?= $page == 'home' ? 'active' : '' ?>">🏠 Dashboard</a>
        <a href="?page=user" class="<?= $page == 'user' ? 'active' : '' ?>">👤 Kelola User</a>
        <a href="?page=post" class="<?= $page == 'post' ? 'active' : '' ?>">📝 Postingan</a>
        <a href="?page=pesan" class="<?= $page == 'pesan' ? 'active' : '' ?>">💬 Pesan</a>
        <a href="?page=notif" class="<?= $page == 'notif' ? 'active' : '' ?>">🔔 Notifikasi</a>
        <a href="?logout=true">🚪 Keluar</a>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <div class="topbar">
            <h5>Selamat Datang, <strong><?= htmlspecialchars($username) ?></strong></h5>
            <a href="?logout=true" class="logout-btn">Keluar</a>
        </div>

        <?php
        // ====== HALAMAN DINAMIS ======
        switch ($page) {
            case 'home':
                echo '
                <div class="card card-custom p-4">
                    <h5>📊 Statistik Singkat</h5>
                    <hr>
                    <p>Selamat datang di Dashboard Admin. Gunakan menu di kiri untuk mengelola aplikasi medsos Anda.</p>
                </div>';
                break;

            case 'user':
                // Hapus User
                if (isset($_GET['hapus'])) {
                    $hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
                    mysqli_query($koneksi, "DELETE FROM user WHERE UserName='$hapus'");
                    echo "<script>alert('User berhasil dihapus!');window.location='?page=user';</script>";
                }

                // Edit User (proses update)
                if (isset($_POST['update_user'])) {
                    $uname = mysqli_real_escape_string($koneksi, $_POST['UserName']);
                    $fname = mysqli_real_escape_string($koneksi, $_POST['FirstName']);
                    $lname = mysqli_real_escape_string($koneksi, $_POST['LastName']);
                    $email = mysqli_real_escape_string($koneksi, $_POST['Email']);
                    mysqli_query($koneksi, "UPDATE user SET FirstName='$fname', LastName='$lname', Email='$email' WHERE UserName='$uname'");
                    echo "<script>alert('Data user berhasil diperbarui!');window.location='?page=user';</script>";
                }

                echo '<div class="card card-custom p-4">
                        <h5>👤 Daftar Pengguna</h5>
                        <hr>
                        <table class="table table-bordered table-striped align-middle text-center">
                          <thead class="table-light">
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                          </thead>
                          <tbody>';

                $users = mysqli_query($koneksi, "SELECT * FROM user ORDER BY UserName ASC");
                if (mysqli_num_rows($users) > 0) {
                    while ($u = mysqli_fetch_assoc($users)) {
                        $username = htmlspecialchars($u['UserName']);
              
                        $email = htmlspecialchars($u['Email']);
                        echo "<tr>
                                <td>{$username}</td>
    
                                <td>{$email}</td>
                                <td>
    <a href='edit_user.php?UserName={$u['UserName']}' class='btn btn-sm btn-warning me-1'>Edit</a>
    <a href='delete_user.php?UserName={$u['UserName']}' class='btn btn-sm btn-danger' onclick='return confirm(\'Yakin ingin menghapus user ini?\');'>Hapus</a>

</td>

                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-muted'>Belum ada pengguna</td></tr>";
                }
                echo '</tbody></table></div>';
                if (isset($_GET['edit'])) {
                    $edit = mysqli_real_escape_string($koneksi, $_GET['edit']);
                    $data = mysqli_query($koneksi, "SELECT * FROM user WHERE UserName='$edit'");
                    if (mysqli_num_rows($data) > 0) {
                        $u = mysqli_fetch_assoc($data);
                        echo '
                        <div class="card card-custom p-4 mt-4">
                            <h5>✏️ Edit Pengguna</h5><hr>
                            <form method="POST">
                                <input type="hidden" name="UserName" value="' . htmlspecialchars($u['UserName']) . '">
                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Nama Depan</label>
                                        <input type="text" name="FirstName" class="form-control" value="' . htmlspecialchars($u['FirstName']) . '" required>
                                    </div>
                                    <div class="col">
                                        <label>Nama Belakang</label>
                                        <input type="text" name="LastName" class="form-control" value="' . htmlspecialchars($u['LastName']) . '" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" name="Email" class="form-control" value="' . htmlspecialchars($u['Email']) . '" required>
                                </div>
                                <button type="submit" name="update_user" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="?page=user" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>';
                    }
                }
                break;

                // Form Edit Use
            case 'post':
                $posts = mysqli_query($koneksi, "SELECT * FROM post ORDER BY PostID DESC");
                echo '<div class="card card-custom p-4">
                        <h5>📝 Semua Postingan</h5><hr>';
                if (mysqli_num_rows($posts) > 0) {
                    while ($p = mysqli_fetch_assoc($posts)) {
                        echo "<div class='border rounded p-3 mb-3'>
                                <strong>{$p['UserName']}</strong> - {$p['Date']}<br>
                                <p>{$p['Text']}</p>
                              </div>";
                    }
                } else {
                    echo "<p class='text-muted'>Belum ada postingan.</p>";
                }
                echo '</div>';
                break;

            case 'pesan':
                echo '<div class="card card-custom p-4"><h5>💬 Pesan Masuk</h5><hr><p>Tidak ada pesan baru.</p></div>';
                break;

            case 'notif':
                echo '<div class="card card-custom p-4"><h5>🔔 Notifikasi</h5><hr><p>Tidak ada notifikasi baru.</p></div>';
                break;

            default:
                echo '<div class="card card-custom p-4"><h5>Halaman tidak ditemukan.</h5></div>';
        }
        ?>
    </div>
</body>

</html>