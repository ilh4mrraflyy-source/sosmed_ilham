<?php include "lib/koneksi.php"; ?>

<?php
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "INSERT INTO user (Username, Email, Password) VALUES ('$username', '$email', '$password')");
    if ($query) {
        header("Location: login.php");
    } else {
        $error = "Gagal mendaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar | Medsos Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow p-4" style="width: 360px;">
        <h3 class="text-center mb-4 fw-bold">Facebook</h3>

        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="post">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="register" class="btn btn-success w-100">Daftar</button>
        </form>

        <hr>
        <div class="text-center">
            Sudah punya akun? <a href="login.php" class="text-decoration-none">Masuk</a>
        </div>
    </div>

</body>

</html>