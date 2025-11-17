<?php
include "../lib/koneksi.php";
session_start();

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil data user berdasarkan UserName
if (isset($_GET['UserName'])) {
    $UserName = $_GET['UserName'];
    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE UserName='$UserName'");
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        echo "<script>alert('User tidak ditemukan!');window.location='index.php?page=user';</script>";
        exit();
    }
} else {
    header("Location: index.php?page=user");
    exit();
}

// Proses update data
if (isset($_POST['update'])) {
    $FirstName = $_POST['FirstName'];
    $LastName  = $_POST['LastName'];
    $Email     = $_POST['Email'];
    $Password  = $_POST['Password'];

    $update = mysqli_query($koneksi, "UPDATE user SET 
        FirstName='$FirstName',
        LastName='$LastName',
        Email='$Email',
        Password='$Password'
        WHERE UserName='$UserName'");

    if ($update) {
        echo "<script>alert('Data user berhasil diperbarui!');window.location='index.php?page=user';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit User - <?= htmlspecialchars($data['UserName']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f2f4f8;
        font-family: 'Segoe UI', sans-serif;
    }

    .container {
        max-width: 600px;
        margin-top: 60px;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background: #1877f2;
        color: white;
        font-weight: bold;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .btn-primary {
        background: #1877f2;
        border: none;
        border-radius: 8px;
    }

    .btn-primary:hover {
        background: #145db8;
    }

    .btn-secondary {
        border-radius: 8px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                ✏️ Edit User - <?= htmlspecialchars($data['UserName']); ?>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="FirstName" value="<?= htmlspecialchars($data['FirstName']); ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="LastName" value="<?= htmlspecialchars($data['LastName']); ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="Email" value="<?= htmlspecialchars($data['Email']); ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="text" name="Password" value="<?= htmlspecialchars($data['Password']); ?>"
                            class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php?page=user" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>