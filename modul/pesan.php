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

// ===== Ambil daftar teman (dari tabel friends) =====
$friends_q = mysqli_query($conn, "
  SELECT CASE 
           WHEN UserA = '$username' THEN UserB 
           ELSE UserA 
         END AS FriendName
  FROM friends 
  WHERE UserA = '$username' OR UserB = '$username'
");
$friends = [];
while ($f = mysqli_fetch_assoc($friends_q)) {
  $friends[] = $f['FriendName'];
}

// ===== Kirim Pesan =====
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pesan'])) {
  $toUser = mysqli_real_escape_string($conn, $_POST['toUser']);
  $message = mysqli_real_escape_string($conn, $_POST['pesan']);
  $date = date("Y-m-d H:i:s");

  // cek apakah teman
  $cekTeman = mysqli_query($conn, "
    SELECT * FROM friends 
    WHERE (UserA='$username' AND UserB='$toUser') 
       OR (UserA='$toUser' AND UserB='$username')
  ");
  if (mysqli_num_rows($cekTeman) > 0) {
    // kirim langsung
    mysqli_query($conn, "INSERT INTO message (FromUser, ToUser, MessageText, DateSent) 
                         VALUES ('$username', '$toUser', '$message', '$date')");
  } else {
    // masuk ke permintaan pesan
    mysqli_query($conn, "INSERT INTO message_requests (FromUser, ToUser, MessageText, DateSent) 
                         VALUES ('$username', '$toUser', '$message', '$date')");
  }
  exit();
}

// ===== Ambil Pesan untuk Chat tertentu =====
if (isset($_GET['fetch']) && isset($_GET['chat'])) {
  $chat = $_GET['chat'];
  $q = "
    SELECT * FROM message 
    WHERE (FromUser='$username' AND ToUser='$chat')
       OR (FromUser='$chat' AND ToUser='$username')
    ORDER BY DateSent ASC
  ";
  $result = mysqli_query($conn, $q);
  while ($msg = mysqli_fetch_assoc($result)) {
    $cls = ($msg['FromUser'] == $username) ? 'me' : 'them';
    echo "<div class='msg $cls'>" . nl2br(htmlspecialchars($msg['MessageText'])) . "</div>";
  }
  exit();
}

$currentChat = $_GET['chat'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesan - Facebook Messenger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f0f2f5;
        font-family: "Segoe UI", Arial, sans-serif;
        overflow: hidden;
    }

    .navbar {
        background-color: #1877f2;
    }

    .navbar-brand,
    .nav-link {
        color: white !important;
        font-weight: 600;
    }

    .messenger {
        display: flex;
        height: calc(100vh - 56px);
    }

    .friend-list {
        width: 28%;
        background-color: #fff;
        border-right: 1px solid #ddd;
        overflow-y: auto;
    }

    .friend-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        text-decoration: none;
        color: #050505;
        position: relative;
    }

    .friend-item:hover {
        background-color: #f0f2f5;
    }

    .friend-item img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .online-dot {
        position: absolute;
        left: 50px;
        bottom: 12px;
        width: 10px;
        height: 10px;
        background: #31a24c;
        border-radius: 50%;
        border: 2px solid white;
    }

    .search-box {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .search-box input {
        width: 100%;
        border-radius: 20px;
        border: none;
        padding: 8px 15px;
        background: #f0f2f5;
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: #e5ddd5;
        position: relative;
    }

    .chat-header {
        background-color: #fff;
        padding: 15px;
        border-bottom: 1px solid #ddd;
        font-weight: 600;
    }

    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .msg {
        max-width: 70%;
        padding: 10px 15px;
        margin-bottom: 10px;
        border-radius: 18px;
        font-size: 14px;
    }

    .msg.me {
        background-color: #1877f2;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }

    .msg.them {
        background-color: #f0f0f0;
        color: #050505;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
    }

    .chat-input {
        background-color: #fff;
        padding: 10px 15px;
        display: flex;
        border-top: 1px solid #ddd;
    }

    .chat-input input {
        flex: 1;
        border: none;
        border-radius: 20px;
        padding: 10px 15px;
        background-color: #f0f2f5;
    }

    .chat-input button {
        border: none;
        background-color: #1877f2;
        color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        margin-left: 10px;
        font-weight: bold;
    }

    .no-chat {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #888;
        font-size: 18px;
        flex: 1;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../index.php">Facebook</a>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a href="../index.php" class="nav-link">Beranda</a></li>
                <li class="nav-item"><a href="teman.php" class="nav-link">Teman</a></li>
                <li class="nav-item"><a href="pesan.php" class="nav-link active">Pesan</a></li>
                <li class="nav-item"><a href="permintaan_pesan.php" class="nav-link">Permintaan Pesan</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link">Keluar</a></li>
            </ul>
        </div>
    </nav>

    <div class="messenger">
        <!-- Sidebar Pencarian dan Daftar Teman -->
        <div class="friend-list">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Cari teman atau pengguna...">
            </div>
            <div id="friendContainer">
                <?php foreach ($friends as $f): ?>
                <a href="?chat=<?= urlencode($f) ?>" class="friend-item <?= ($currentChat == $f) ? 'bg-light' : '' ?>">
                    <img src="https://ui-avatars.com/api/?name=<?= htmlspecialchars($f) ?>&background=1877f2&color=fff">
                    <div>
                        <strong><?= htmlspecialchars($f) ?></strong><br>
                        <small>Aktif sekarang</small>
                    </div>
                    <span class="online-dot"></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Area Chat -->
        <div class="chat-area">
            <?php if ($currentChat): ?>
            <div class="chat-header"><?= htmlspecialchars($currentChat) ?></div>
            <div class="chat-messages" id="chat-box"></div>
            <form class="chat-input" method="post" id="chat-form">
                <input type="hidden" name="toUser" id="toUser" value="<?= htmlspecialchars($currentChat) ?>">
                <input type="text" name="pesan" id="pesan" placeholder="Tulis pesan..." required>
                <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
            <?php else: ?>
            <div class="no-chat">Pilih teman atau cari pengguna untuk mulai mengobrol</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // 🔍 Live Search
    const searchInput = document.getElementById('searchInput');
    const container = document.getElementById('friendContainer');

    searchInput.addEventListener('keyup', () => {
        const q = searchInput.value.trim();
        fetch(`search_user.php?q=${encodeURIComponent(q)}`)
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
            });
    });

    // 💬 Chat Fetch
    <?php if ($currentChat): ?>
    const chatBox = document.getElementById('chat-box');
    const form = document.getElementById('chat-form');
    const pesanInput = document.getElementById('pesan');
    const toUser = document.getElementById('toUser').value;

    function fetchMessages() {
        fetch(`pesan.php?fetch=1&chat=${toUser}`)
            .then(res => res.text())
            .then(html => {
                chatBox.innerHTML = html;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);
        fetch('pesan.php', {
            method: 'POST',
            body: formData
        });
        pesanInput.value = '';
        setTimeout(fetchMessages, 300);
    });

    setInterval(fetchMessages, 4000);
    fetchMessages();
    <?php endif; ?>
    </script>
</body>

</html>