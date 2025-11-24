<?php
session_start();

// ===== Koneksi Database =====
$host = "localhost";
$user = "root";
$pass = "rpl12345";
$db   = "db_medsos";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ===== Cek Login =====
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['user']['Username'];

// ===== API untuk AJAX: terima/tolak request dan hitung jumlah pending =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'accept' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        // Ambil request
        $req = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM message_requests WHERE ID=$id AND ToUser='" . mysqli_real_escape_string($conn, $username) . "'"));
        if ($req) {
            // Update status
            mysqli_query($conn, "UPDATE message_requests SET Status='accepted' WHERE ID=$id");

            $from = mysqli_real_escape_string($conn, $req['FromUser']);
            $to = mysqli_real_escape_string($conn, $req['ToUser']);
            $text = mysqli_real_escape_string($conn, $req['MessageText']);
            $datesent = mysqli_real_escape_string($conn, $req['DateSent']);

            // Masukkan pesan ke table message (jadi user bisa chat)
            mysqli_query($conn, "INSERT INTO message (FromUser, ToUser, MessageText, DateSent) VALUES ('$from', '$to', '$text', '$datesent')");

            // Tambah ke tabel friends jika belum ada
            $checkFriend = mysqli_query($conn, "
                SELECT * FROM friends 
                WHERE (UserA='$from' AND UserB='$to') OR (UserA='$to' AND UserB='$from')
            ");
            if (mysqli_num_rows($checkFriend) == 0) {
                mysqli_query($conn, "INSERT INTO friends (UserA, UserB, CreatedAt) VALUES ('$from', '$to', NOW())");
            }

            // Optional: insert notif ke pengirim bahwa permintaan pesan diterima (jika kamu punya notif table)
            if (mysqli_query($conn, "SHOW TABLES LIKE 'notif'")) {
                mysqli_query($conn, "INSERT INTO notif (FromUser, ToUser, Type, Message) VALUES ('$to', '$from', 'message_request_accepted', 'menerima permintaan pesan kamu')");
            }

            echo json_encode(['status' => 'ok', 'msg' => 'Permintaan diterima']);
            exit();
        } else {
            echo json_encode(['status' => 'err', 'msg' => 'Request tidak ditemukan']);
            exit();
        }
    }

    if ($action === 'reject' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        // hanya pemilik ToUser yang bisa menolak
        $req = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM message_requests WHERE ID=$id AND ToUser='" . mysqli_real_escape_string($conn, $username) . "'"));
        if ($req) {
            mysqli_query($conn, "UPDATE message_requests SET Status='rejected' WHERE ID=$id");
            // Optional: notify pengirim
            if (mysqli_query($conn, "SHOW TABLES LIKE 'notif'")) {
                mysqli_query($conn, "INSERT INTO notif (FromUser, ToUser, Type, Message) VALUES ('" . mysqli_real_escape_string($conn, $username) . "', '" . mysqli_real_escape_string($conn, $req['FromUser']) . "', 'message_request_rejected', 'menolak permintaan pesan kamu')");
            }
            echo json_encode(['status' => 'ok', 'msg' => 'Permintaan ditolak']);
            exit();
        } else {
            echo json_encode(['status' => 'err', 'msg' => 'Request tidak ditemukan']);
            exit();
        }
    }

    if ($action === 'count_pending') {
        $res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM message_requests WHERE ToUser='" . mysqli_real_escape_string($conn, $username) . "' AND Status='pending'"));
        echo json_encode(['count' => intval($res['cnt'])]);
        exit();
    }

    // fallback
    echo json_encode(['status' => 'err', 'msg' => 'Action tidak dikenali']);
    exit();
}

// ===== Ambil daftar permintaan pesan untuk halaman (pending) =====
$requests_q = mysqli_query($conn, "
    SELECT mr.*, u.Photo 
    FROM message_requests mr
    LEFT JOIN user u ON u.UserName = mr.FromUser
    WHERE mr.ToUser = '" . mysqli_real_escape_string($conn, $username) . "'
    ORDER BY mr.DateSent DESC
");

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Permintaan Pesan - Facebook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: "Segoe UI", Roboto, Arial;
        background: #f0f2f5;
    }

    .navbar {
        background: #1877f2;
    }

    .card {
        border-radius: 10px;
    }

    .request-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .req-photo {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
    }

    .req-actions button {
        margin-right: 8px;
    }

    .small-muted {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .badge-pending {
        background: #ff4d4f;
        color: white;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 999px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand text-white" href="../index.php">Facebook</a>
            <div class="d-flex align-items-center">
                <a class="btn btn-light btn-sm me-2" href="../index.php">Beranda</a>
                <a class="btn btn-danger btn-sm" href="../logout.php">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Permintaan Pesan</h5>
                        <div>
                            <span id="pendingBadge" class="badge-pending" style="display:none;">0</span>
                        </div>
                    </div>
                    <div id="requestsList">
                        <?php if (mysqli_num_rows($requests_q) === 0): ?>
                        <div class="p-3 text-muted">Belum ada permintaan pesan.</div>
                        <?php else: ?>
                        <?php while ($r = mysqli_fetch_assoc($requests_q)): 
                            $photo = (!empty($r['Photo']) && file_exists('../uploads/' . $r['Photo'])) ? '../uploads/' . $r['Photo'] : ('https://ui-avatars.com/api/?name=' . urlencode($r['FromUser']) . '&background=1877f2&color=fff');
                            $snippet = mb_strimwidth($r['MessageText'], 0, 120, '...');
                        ?>
                        <div class="request-item" data-id="<?= intval($r['ID']) ?>">
                            <img src="<?= htmlspecialchars($photo) ?>" class="req-photo" alt="photo">
                            <div style="flex:1;">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <strong><?= htmlspecialchars($r['FromUser']) ?></strong>
                                        <div class="small-muted"><?= htmlspecialchars($snippet) ?></div>
                                    </div>
                                    <div class="small-muted" style="text-align:right;">
                                        <?= date("d M Y H:i", strtotime($r['DateSent'])) ?><br>
                                        <small class="text-capitalize"><?= htmlspecialchars($r['Status']) ?></small>
                                    </div>
                                </div>

                                <div class="mt-2 req-actions">
                                    <?php if ($r['Status'] === 'pending'): ?>
                                    <button class="btn btn-sm btn-success btn-accept"
                                        data-id="<?= intval($r['ID']) ?>">Terima</button>
                                    <button class="btn btn-sm btn-outline-secondary btn-reject"
                                        data-id="<?= intval($r['ID']) ?>">Tolak</button>
                                    <?php else: ?>
                                    <span class="text-muted">Anda sudah <?= htmlspecialchars($r['Status']) ?> permintaan
                                        ini.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card p-3">
                    <h6>Penjelasan</h6>
                    <p class="small-muted mb-0">Permintaan pesan yang diterima akan otomatis masuk ke chat, dan jika
                        belum menjadi teman
                        sistem akan menambahkan relasi teman (friends). Permintaan yang ditolak akan berubah status
                        menjadi <em>rejected</em>.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // helper fetch wrapper
    async function postJSON(data) {
        const form = new FormData();
        for (const k in data) form.append(k, data[k]);
        const res = await fetch('', {
            method: 'POST',
            body: form
        });
        return res.json();
    }

    async function refreshPendingCount() {
        try {
            const res = await postJSON({
                action: 'count_pending'
            });
            const badge = document.getElementById('pendingBadge');
            if (res.count && res.count > 0) {
                badge.style.display = 'inline-block';
                badge.textContent = res.count;
            } else {
                badge.style.display = 'none';
            }
        } catch (e) {
            console.error(e);
        }
    }

    document.addEventListener('click', async function(e) {
        if (e.target.matches('.btn-accept')) {
            const id = e.target.getAttribute('data-id');
            e.target.disabled = true;
            e.target.textContent = 'Memproses...';
            try {
                const res = await postJSON({
                    action: 'accept',
                    id: id
                });
                if (res.status === 'ok') {
                    // ubah tampilan item
                    const item = document.querySelector('.request-item[data-id="' + id + '"]');
                    if (item) {
                        item.querySelector('.req-actions').innerHTML =
                            '<span class="text-success">Diterima • pesan dipindahkan ke chat</span>';
                        item.querySelector('.small-muted')?.remove();
                    }
                } else {
                    alert(res.msg || 'Gagal menerima request');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan, cek console.');
            }
            refreshPendingCount();
        }

        if (e.target.matches('.btn-reject')) {
            if (!confirm('Tolak permintaan pesan ini?')) return;
            const id = e.target.getAttribute('data-id');
            e.target.disabled = true;
            try {
                const res = await postJSON({
                    action: 'reject',
                    id: id
                });
                if (res.status === 'ok') {
                    const item = document.querySelector('.request-item[data-id="' + id + '"]');
                    if (item) {
                        item.querySelector('.req-actions').innerHTML =
                            '<span class="text-muted">Ditolak</span>';
                    }
                } else {
                    alert(res.msg || 'Gagal menolak request');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan, cek console.');
            }
            refreshPendingCount();
        }
    });

    // load badge on start and poll tiap 6 detik
    refreshPendingCount();
    setInterval(refreshPendingCount, 6000);
    </script>
</body>

</html>