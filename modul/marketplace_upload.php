<?php
session_start();
$conn = mysqli_connect("localhost","root","rpl12345","db_medsos");
if (!$conn) die("Koneksi gagal");

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['user']['Username'];

if ($_SERVER['REQUEST_METHOD']=='POST') {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $price = intval($_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);

    $img = "";
    if (!empty($_FILES['photo']['name'])) {
        $img = time()."_".$_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/".$img);
    }

    mysqli_query($conn,
        "INSERT INTO marketplace (Seller, Title, Price, Description, Category, Photo)
        VALUES ('$username','$title',$price,'$desc','$category','$img')"
    );

    echo "<script>alert('Barang berhasil diupload!');location='marketplace.php';</script>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Upload Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background: #f0f2f5;
        font-family: 'Segoe UI';
    }

    .box {
        max-width: 500px;
        margin: 40px auto;
        background: #fff;
        padding: 25px;
        border-radius: 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
    }
    </style>

</head>

<body>

    <div class="box">
        <h4 class="fw-bold mb-3">Jual Barang Baru</h4>

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" class="form-control mb-2" placeholder="Nama Barang" required>

            <input type="number" name="price" class="form-control mb-2" placeholder="Harga (Rp)" required>

            <select name="category" class="form-control mb-2" required>
                <option value="">Pilih Kategori</option>
                <option>Elektronik</option>
                <option>Kendaraan</option>
                <option>Fashion</option>
                <option>Perabot</option>
                <option>Game</option>
                <option>Hobi</option>
            </select>

            <textarea name="desc" rows="3" class="form-control mb-2" placeholder="Deskripsi barang..."
                required></textarea>

            <input type="file" name="photo" class="form-control mb-3" required>

            <button class="btn btn-primary w-100">Upload Barang</button>
        </form>

        <a href="marketplace.php" class="btn btn-secondary w-100 mt-3">Kembali</a>
    </div>

</body>

</html>