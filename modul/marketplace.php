<?php
session_start();
$conn = mysqli_connect("localhost", "root", "rpl12345", "db_medsos");
if (!$conn) die("Koneksi gagal");

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['user']['Username'];

// ==== FILTER KATEGORI ====
$cat = $_GET['cat'] ?? '';

if ($cat !== '') {
    $q = mysqli_query($conn,
        "SELECT * FROM marketplace WHERE Category='$cat' ORDER BY ItemID DESC"
    );
} else {
    $q = mysqli_query($conn,
        "SELECT * FROM marketplace ORDER BY ItemID DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Fesbuker Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
    body {
        background: #f0f2f5;
        font-family: Segoe UI;
    }

    .sidebar {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .category-item {
        display: block;
        padding: 8px 5px;
        border-radius: 8px;
        color: #333;
        text-decoration: none;
        font-size: 15px;
        margin-bottom: 8px;
        transition: .2s;
    }

    .category-item:hover {
        background: #e8f0ff;
        color: #1877f2;
    }

    .card-item {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: .2s;
        background: white;
    }

    .card-item:hover {
        transform: scale(1.02);
    }

    .card-item img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .upload-btn-box {
        text-align: right;
        margin-bottom: 15px;
    }

    .upload-btn {
        background: #1877f2;
        border: none;
        color: white;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: .2s;
    }

    .upload-btn:hover {
        background: #0f63d1;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark" style="background:#1877f2;">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">Fesbuker Marketplace</a>
            <div class="d-flex gap-2">
                <a href="../index.php" class="btn btn-light btn-sm">Beranda</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container market-container">
        <div class="row g-4 mt-4">

            <!-- Sidebar Kategori -->
            <div class="col-md-3">
                <div class="sidebar">
                    <h5 class="fw-bold mb-3">Kategori</h5>

                    <a href="marketplace.php" class="category-item">
                        <i class="fa-solid fa-list"></i> Semua
                    </a>

                    <a href="marketplace.php?cat=Elektronik" class="category-item">
                        <i class="fa-solid fa-mobile-screen"></i> Elektronik
                    </a>

                    <a href="marketplace.php?cat=Perabot" class="category-item">
                        <i class="fa-solid fa-couch"></i> Perabot Rumah
                    </a>

                    <a href="marketplace.php?cat=Kendaraan" class="category-item">
                        <i class="fa-solid fa-car"></i> Kendaraan
                    </a>

                    <a href="marketplace.php?cat=Fashion" class="category-item">
                        <i class="fa-solid fa-shirt"></i> Fashion
                    </a>

                    <a href="marketplace.php?cat=Game" class="category-item">
                        <i class="fa-solid fa-gamepad"></i> Game & Hobi
                    </a>

                    <a href="marketplace.php?cat=Buku" class="category-item">
                        <i class="fa-solid fa-book"></i> Buku
                    </a>
                </div>
            </div>

            <!-- Daftar Produk -->
            <div class="col-md-9">

                <!-- Tombol kecil upload -->
                <div class="upload-btn-box">
                    <a href="marketplace_upload.php" class="upload-btn">
                        <i class="fa-solid fa-plus"></i> Jual Barang
                    </a>
                </div>

                <div class="row">
                    <?php while ($item = mysqli_fetch_assoc($q)):
                        $photo = !empty($item['Photo'])
                            ? "../uploads/" . $item['Photo']
                            : "https://source.unsplash.com/600x400/?product";
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card-item">
                            <img src="<?= $photo ?>">
                            <div class="p-3">
                                <h6 class="fw-bold"><?= htmlspecialchars($item['Title']) ?></h6>
                                <div class="text-success fw-bold">
                                    Rp <?= number_format($item['Price'], 0, ',', '.') ?>
                                </div>
                                <small class="text-muted">Penjual: <?= htmlspecialchars($item['Seller']) ?></small>

                                <a href="marketplace_detail.php?id=<?= $item['ItemID'] ?>"
                                    class="btn btn-primary w-100 mt-2 btn-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

            </div>
        </div>
    </div>

</body>

</html>