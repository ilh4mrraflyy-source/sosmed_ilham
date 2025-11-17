<?php
session_start();
$conn = mysqli_connect("localhost", "root", "rpl12345", "db_medsos");
if (!$conn) die("Koneksi gagal");

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['user']['Username'];
$itemID = intval($_GET['id']);

// Ambil data barang
$item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM marketplace WHERE ItemID=$itemID"));
if (!$item) {
    echo "<script>alert('Barang tidak ditemukan');location='marketplace.php';</script>";
    exit();
}

// Data penjual
$seller = $item['Seller'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE UserName='$seller'"));

$photo = (!empty($item['Photo']) && file_exists("../uploads/".$item['Photo']))
    ? "../uploads/".$item['Photo']
    : "https://source.unsplash.com/600x400/?product";

// Foto profil penjual
$sellerPhoto = (!empty($user['Photo']) && file_exists("../uploads/".$user['Photo']))
    ? "../uploads/".$user['Photo']
    : "https://ui-avatars.com/api/?name=" . urlencode($seller) . "&background=1877f2&color=fff";

// Barang lain dari penjual
$other = mysqli_query($conn, 
    "SELECT * FROM marketplace 
     WHERE Seller='$seller' AND ItemID!=$itemID 
     ORDER BY ItemID DESC LIMIT 6");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Barang - Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background: #f0f2f5;
        font-family: Segoe UI;
    }

    .card-detail {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .item-img {
        width: 100%;
        border-radius: 12px;
        max-height: 380px;
        object-fit: cover;
    }

    .seller-box {
        background: white;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .product-card {
        border-radius: 12px;
        overflow: hidden;
        background: white;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        transition: .2s;
    }

    .product-card:hover {
        transform: scale(1.02);
    }

    .product-card img {
        height: 140px;
        width: 100%;
        object-fit: cover;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark" style="background:#1877f2;">
        <div class="container-fluid">
            <a class="navbar-brand" href="marketplace.php">← Marketplace</a>
            <div class="d-flex">
                <a href="../index.php" class="btn btn-light btn-sm me-2">Beranda</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row g-4">

            <!-- GAMBAR BARANG -->
            <div class="col-md-7">
                <div class="card-detail mb-3">
                    <img src="<?= $photo ?>" class="item-img">
                </div>

                <div class="card-detail">
                    <h4 class="fw-bold"><?= htmlspecialchars($item['Title']) ?></h4>
                    <h3 class="text-success fw-bold">Rp <?= number_format($item['Price'],0,',','.') ?></h3>
                    <hr>
                    <p><?= nl2br(htmlspecialchars($item['Description'])) ?></p>
                    <small class="text-muted">Kategori: <?= htmlspecialchars($item['Category']) ?></small>
                </div>
            </div>

            <!-- PENJUAL -->
            <div class="col-md-5">
                <div class="seller-box mb-3">
                    <h5 class="fw-bold mb-3">Info Penjual</h5>
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?= $sellerPhoto ?>" width="60" height="60" class="rounded-circle me-3">
                        <div>
                            <h6 class="mb-0"><?= htmlspecialchars($seller) ?></h6>
                            <a href="profil.php?user=<?= $seller ?>" class="text-primary small">Lihat Profil</a>
                        </div>
                    </div>

                    <!-- TOMBOL CHAT PENJUAL -->
                    <button class="btn btn-primary w-100 mb-2" onclick="openChatPopup('<?= $seller ?>')">
                        <i class="fa-solid fa-message"></i> Chat Penjual
                    </button>

                    <!-- WA STYLE, OPSIONAL -->
                    <a href="https://wa.me/62<?= rand(81234567890, 89999999999) ?>" class="btn btn-success w-100"
                        target="_blank">
                        <i class="fa-brands fa-whatsapp"></i> Hubungi via WhatsApp
                    </a>
                </div>

                <!-- PRODUK LAIN -->
                <h5 class="fw-semibold mb-2">Barang Lain dari Penjual</h5>
                <div class="row">
                    <?php while ($o = mysqli_fetch_assoc($other)):
                $img = (!empty($o['Photo']) && file_exists("../uploads/".$o['Photo']))
                    ? "../uploads/".$o['Photo']
                    : "https://source.unsplash.com/600x400/?product";
            ?>
                    <div class="col-6 mb-3">
                        <a href="marketplace_detail.php?id=<?= $o['ItemID'] ?>" class="text-decoration-none text-dark">
                            <div class="product-card">
                                <img src="<?= $img ?>">
                                <div class="p-2">
                                    <strong style="font-size:14px;"><?= htmlspecialchars($o['Title']) ?></strong>
                                    <div class="text-success fw-bold" style="font-size:14px;">
                                        Rp <?= number_format($o['Price'],0,',','.') ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
    // POPUP CHAT (pakai sistem chat kamu yang sudah ada)
    let chatPopup = null;

    function openChatPopup(username) {
        if (chatPopup) chatPopup.remove();
        fetch('chat_popup.php?user=' + encodeURIComponent(username))
            .then(res => res.text())
            .then(html => {
                const div = document.createElement('div');
                div.innerHTML = html;
                document.body.appendChild(div);
                chatPopup = div;
            });
    }

    function closeChatPopup() {
        if (chatPopup) chatPopup.remove();
    }
    </script>

</body>

</html>