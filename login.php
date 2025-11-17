<?php
session_start();
include "lib/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE UserName='$username' AND Password='$password'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['user'] = [
            'Username' => $data['UserName'],
            'Email'    => $data['Email']
        ];
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | Medsos Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', sans-serif;
    }

    .card {
        border-radius: 10px;
    }

    .btn-primary {
        background-color: #1877f2;
        border: none;
    }

    .btn-primary:hover {
        background-color: #166fe5;
    }
    </style>
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4" style="width: 360px;">
        <h3 class="text-center mb-4 fw-bold text-primary">Fesbuker</h3>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post">
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" class="btn btn-primary w-100">Log In</button>
        </form>
        <hr>
        <div class="text-center">
            Belum punya akun? <a href="register.php">Daftar</a>
        </div>
    </div>
</body>

</html>