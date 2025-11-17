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
  header("Location: login.php");
  exit();
}

$username = $_SESSION['user']['Username'];

// ===== Ambil Data User =====
$userData = mysqli_query($conn, "SELECT * FROM user WHERE UserName='$username'");
$user = mysqli_fetch_assoc($userData);
$photo_url = (!empty($user['Photo']) && file_exists('uploads/' . $user['Photo']))
  ? 'uploads/' . $user['Photo']
  : 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=1877f2&color=fff';

// ===== Ambil daftar teman =====
$friends = mysqli_query($conn, "
SELECT u.UserName, u.Photo 
FROM user u
WHERE u.UserName != '$username'
ORDER BY RAND() LIMIT 5
");

// ===== POST =====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Posting
  if (isset($_POST['text'])) {
    $text = mysqli_real_escape_string($conn, $_POST['text']);
    $date = date("Y-m-d");
    $time = date("H:i:s");

    if (!empty($_FILES['image']['name'])) {
      $imgName = time() . "_" . basename($_FILES["image"]["name"]);
      $target = "uploads/" . $imgName;
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
        mysqli_query($conn, "INSERT INTO image (ImageName, Date) VALUES ('$imgName', '$date')");
        $imageID = mysqli_insert_id($conn);

        mysqli_query($conn, "INSERT INTO post (Date, Time, Text, UserName) VALUES ('$date', '$time', '$text', '$username')");
        $postID = mysqli_insert_id($conn);

        mysqli_query($conn, "INSERT INTO detailpost (PostID, ImageID, Comment) VALUES ($postID, $imageID, '')");
      } else {
        // gagal upload: tetap insert text tanpa image
        mysqli_query($conn, "INSERT INTO post (Date, Time, Text, UserName) VALUES ('$date', '$time', '$text', '$username')");
      }
    } else {
      mysqli_query($conn, "INSERT INTO post (Date, Time, Text, UserName) VALUES ('$date', '$time', '$text', '$username')");
    }
  }

  // Like
  if (isset($_POST['like_post'])) {
    $postID = intval($_POST['like_post']);
    $check = mysqli_query($conn, "SELECT * FROM likes WHERE PostID=$postID AND UserName='$username'");
    if (mysqli_num_rows($check) == 0) {
      mysqli_query($conn, "INSERT INTO likes (PostID, UserName) VALUES ($postID, '$username')");

      // === NOTIF LIKE ===
      $owner = mysqli_fetch_assoc(mysqli_query($conn, "SELECT UserName FROM post WHERE PostID=$postID"))['UserName'];
      if ($owner != $username) {
        $msg = "menyukai postingan kamu.";
        mysqli_query($conn, "INSERT INTO notif (FromUser, ToUser, Type, Message) VALUES ('$username', '$owner', 'like', '$msg')");
      }

    } else {
      mysqli_query($conn, "DELETE FROM likes WHERE PostID=$postID AND UserName='$username'");
    }
  }

  // Komentar
  if (isset($_POST['comment_post'])) {
    $postID = intval($_POST['comment_post']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment_text']);
    $date = date("Y-m-d");
    $time = date("H:i:s");
    if (!empty($comment)) {
      mysqli_query($conn, "INSERT INTO comment (PostID, UserName, Text, Date, Time) VALUES ($postID, '$username', '$comment', '$date', '$time')");

      // === NOTIF KOMENTAR ===
      $owner = mysqli_fetch_assoc(mysqli_query($conn, "SELECT UserName FROM post WHERE PostID=$postID"))['UserName'];
      if ($owner != $username) {
        $msg = "mengomentari postingan kamu.";
        mysqli_query($conn, "INSERT INTO notif (FromUser, ToUser, Type, Message) VALUES ('$username', '$owner', 'comment', '$msg')");
      }
    }
  }
}

// ===== Ambil Post =====
$q = "
SELECT p.PostID, p.Text, p.Date, p.Time, p.UserName, i.ImageName
FROM post p
LEFT JOIN detailpost dp ON p.PostID = dp.PostID
LEFT JOIN image i ON dp.ImageID = i.ImageID
ORDER BY p.PostID DESC
";
$result = mysqli_query($conn, $q);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Facebook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">

    <style>
    :root {
        --bg: #f0f2f5;
        --text: #050505;
        --card: #fff;
        --border: #ddd;
        --hover: #e7f3ff;
        --navbar: #1877f2;
    }

    body.dark {
        --bg: #18191a;
        --text: #e4e6eb;
        --card: #242526;
        --border: #3a3b3c;
        --hover: #3a3b3c;
        --navbar: #242526;
    }

    * {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    body {
        background-color: var(--bg);
        color: var(--text);
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        transition: all 0.3s ease;
    }

    .navbar {
        background-color: var(--navbar);
        transition: background 0.3s ease;
    }

    .container-main {
        margin-top: 30px;
    }

    .sidebar {
        background-color: var(--card);
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        color: var(--text);
        text-decoration: none;
        transition: background 0.2s ease, transform 0.15s ease, color 0.3s ease;
        font-weight: 500;
    }

    .menu-item:hover {
        background-color: var(--hover);
        transform: translateX(3px);
        color: var(--text);
    }

    .dark-toggle {
        background: var(--hover);
        border: none;
        color: var(--text);
        border-radius: 8px;
        padding: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }

    .dark-toggle:hover {
        background: #1877f2;
        color: white;
    }

    .friend-list {
        margin-top: 15px;
        border-top: 1px solid var(--border);
        padding-top: 10px;
    }

    .friend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
        text-decoration: none;
        color: var(--text);
        border-radius: 6px;
        transition: background 0.3s;
    }

    .friend-item img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }

    .friend-item:hover {
        background: var(--hover);
    }

    .create-post,
    .post-card {
        background-color: var(--card);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .post-actions {
        display: flex;
        justify-content: space-around;
        border-top: 1px solid var(--border);
        padding-top: 8px;
        margin-top: 10px;
    }

    .post-actions button {
        background: none;
        border: none;
        color: #65676b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .post-actions button.active {
        color: #1877f2;
    }

    .post-actions button:hover {
        background-color: var(--hover);
    }

    .rightbar {
        background-color: var(--card);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .rightbar img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        transition: transform 0.3s;
    }

    .rightbar img:hover {
        transform: scale(1.07);
    }

    .notif-toast {
        position: fixed;
        bottom: -60px;
        right: 20px;
        background: #1877f2;
        color: #fff;
        padding: 12px 18px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        font-weight: 500;
        opacity: 0;
        transition: all 0.4s ease;
        z-index: 9999;
    }

    .notif-toast.show {
        bottom: 20px;
        opacity: 1;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php">Fesbuker</a>

            <!-- 🔍 SEARCH BAR -->
            <form class="d-flex mx-auto position-relative" role="search" onsubmit="return false;">
                <input id="searchInput" class="form-control me-2 rounded-pill" type="search"
                    placeholder="Cari di Facebook..." aria-label="Search" style="width:350px;">
                <div id="searchResults" class="position-absolute bg-white rounded shadow-sm"
                    style="top:45px;width:100%;max-height:300px;overflow-y:auto;display:none;z-index:999;"></div>
            </form>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown me-3">
                    <a href="#" id="notifDropdown" class="nav-link text-white position-relative"
                        data-bs-toggle="dropdown">
                        <i class="fa-solid fa-bell fs-5"></i>
                        <span id="notifCount"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="display:none;">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2"
                        style="width:300px; max-height:400px; overflow-y:auto;" id="notifList">
                        <li class="text-center text-muted small">Tidak ada notifikasi</li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#"
                        class="nav-link text-white fw-semibold bg-primary rounded-pill px-3 py-1"><?= htmlspecialchars($username) ?></a>
                </li>
                <li class="nav-item ms-2">
                    <a href="logout.php" class="nav-link text-light">Keluar</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- ================= STORIES BAR (paste after </nav> and before container-main) ================= -->
    <style>
    /* Styles stories */
    .stories-bar {
        background: transparent;
        padding: 12px 20px;
        display: flex;
        gap: 12px;
        overflow-x: auto;
        align-items: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    }

    .story-item {
        width: 68px;
        text-align: center;
        cursor: pointer;
        font-size: 12px;
    }

    .story-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: inline-block;
        background-size: cover;
        background-position: center;
        padding: 3px;
        box-sizing: border-box;
        position: relative;
    }

    .story-ring {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        padding: 3px;
        background: linear-gradient(45deg, #ff5a5f, #ff9a5a, #ffd15a, #5ac18e);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .story-inner {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: white;
        display: block;
        overflow: hidden;
    }

    .story-inner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 50%;
    }

    .story-label {
        margin-top: 6px;
        display: block;
        text-align: center;
        color: var(--text);
        font-weight: 600;
        white-space: nowrap;
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #storyUploader {
        display: none;
    }

    /* viewer modal */
    .story-modal {
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.85);
        display: none;
        z-index: 99999;
        align-items: center;
        justify-content: center;
    }

    .story-modal .box {
        width: 420px;
        max-width: 95%;
        background: transparent;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }

    .story-modal .box img {
        width: 100%;
        height: auto;
        display: block;
        max-height: 80vh;
        object-fit: contain;
    }

    .story-modal .meta {
        position: absolute;
        left: 12px;
        top: 12px;
        color: white;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .story-modal .meta img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.15);
        object-fit: cover;
    }

    .story-modal .closeBtn {
        position: absolute;
        right: 12px;
        top: 12px;
        color: white;
        font-size: 22px;
        cursor: pointer;
    }

    .story-modal .progress {
        position: absolute;
        left: 0;
        top: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.5);
        width: 0%;
        transition: width 0.1s linear;
    }
    </style>

    <div class="stories-bar" id="storiesBar">
        <div class="story-item" title="Buat Story">
            <label for="storyUploader" style="cursor:pointer;">
                <div class="story-avatar"
                    style="background:#fff; display:flex;align-items:center;justify-content:center;">
                    <div
                        style="width:56px;height:56px;border-radius:50%;background:linear-gradient(45deg,#fff,#eee);display:flex;align-items:center;justify-content:center;border:2px dashed rgba(0,0,0,0.06);">
                        <i class="fa-solid fa-plus" style="color:#1877f2;font-size:18px;"></i>
                    </div>
                </div>
                <div class="story-label">Buat Cerita</div>
            </label>
            <form id="frmStoryUpload" method="post" action="modul/stories_upload.php" enctype="multipart/form-data"
                style="display:none;">
                <input type="file" id="storyUploader" name="story_img" accept="image/*"
                    onchange="document.getElementById('frmStoryUpload').submit()">
            </form>
        </div>
        <!-- Story items akan diisi oleh JS -->
    </div>

    <!-- VIEWER -->
    <div class="story-modal" id="storyModal">
        <div class="box">
            <div class="progress" id="storyProgress"></div>
            <div class="meta">
                <img id="storyUserPhoto" src="" alt="">
                <div>
                    <div id="storyUserName" style="font-weight:700"></div>
                    <div id="storyTime" style="font-size:12px;opacity:0.9"></div>
                </div>
            </div>
            <div class="closeBtn" onclick="closeStory()">✕</div>
            <img id="storyImage" src="" alt="">
        </div>
    </div>

    <script>
    let storiesData = []; // list users with items
    let currentUserIdx = 0;
    let currentStoryIdx = 0;
    let progressInterval = null;

    function loadStories() {
        fetch('modul/stories_api.php').then(r => r.json()).then(data => {
            storiesData = data || [];
            renderStoriesBar();
        }).catch(e => console.error(e));
    }

    function renderStoriesBar() {
        const bar = document.getElementById('storiesBar');
        // remove existing story-item except first (uploader)
        Array.from(bar.querySelectorAll('.story-item')).forEach((el, i) => {
            if (i > 0) el.remove();
        });
        storiesData.forEach((u, idx) => {
            const username = u.user || 'Unknown';
            const userPhoto = u.photo || '';
            // path: index.php is in root so uploads are under 'uploads/'
            const photo = userPhoto && userPhoto !== null ? (userPhoto.startsWith('http') ? userPhoto : (
                'uploads/' + userPhoto)) : ('https://ui-avatars.com/api/?name=' + encodeURIComponent(
                username) + '&background=1877f2&color=fff');
            const html = document.createElement('div');
            html.className = 'story-item';
            html.innerHTML = `
      <div class="story-avatar" data-idx="${idx}" onclick="openStory(${idx},0)">
        <div class="story-ring">
          <div class="story-inner"><img src="${photo}" alt=""></div>
        </div>
      </div>
      <div class="story-label">${escapeHtml(username)}</div>
    `;
            bar.appendChild(html);
        });
    }

    function openStory(userIdx, storyIdx) {
        currentUserIdx = userIdx;
        currentStoryIdx = storyIdx;
        showStory();
    }

    function showStory() {
        const modal = document.getElementById('storyModal');
        if (!storiesData[currentUserIdx]) return closeStory();
        const user = storiesData[currentUserIdx];
        const item = (user.items && user.items[currentStoryIdx]) ? user.items[currentStoryIdx] : null;
        if (!item) {
            closeStory();
            return;
        }

        const imgEl = document.getElementById('storyImage');
        const nameEl = document.getElementById('storyUserName');
        const photoEl = document.getElementById('storyUserPhoto');
        const timeEl = document.getElementById('storyTime');

        const userPhoto = user.photo || '';
        photoEl.src = userPhoto ? (userPhoto.startsWith('http') ? userPhoto : 'uploads/' + userPhoto) :
            'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.user || '') + '&background=1877f2&color=fff';

        nameEl.textContent = user.user || 'Unknown';
        timeEl.textContent = item.CreatedAt ? new Date(item.CreatedAt).toLocaleString() : '';
        imgEl.src = item.ImageName ? ('uploads/' + item.ImageName) : '';

        // mark viewed (if storyID exists)
        if (item.StoryID) markViewed(item.StoryID);

        modal.style.display = 'flex';
        startProgress(() => {
            // move to next story or next user
            currentStoryIdx++;
            if (!user.items || currentStoryIdx >= user.items.length) {
                currentUserIdx++;
                if (currentUserIdx >= storiesData.length) {
                    closeStory();
                    return;
                }
                currentStoryIdx = 0;
            }
            showStory();
        });
    }

    function closeStory() {
        document.getElementById('storyModal').style.display = 'none';
        stopProgress();
    }

    function startProgress(onComplete) {
        stopProgress();
        const el = document.getElementById('storyProgress');
        let pct = 0;
        el.style.width = '0%';
        progressInterval = setInterval(() => {
            pct += 1.8; // speed -> adjust
            el.style.width = pct + '%';
            if (pct >= 100) {
                stopProgress();
                onComplete && onComplete();
            }
        }, 50);
    }

    function stopProgress() {
        if (progressInterval) clearInterval(progressInterval);
        progressInterval = null;
        const el = document.getElementById('storyProgress');
        if (el) el.style.width = '0%';
    }

    function markViewed(storyID) {
        fetch('modul/story_view.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'storyID=' + encodeURIComponent(storyID)
        }).catch(e => console.error(e));
    }

    function escapeHtml(s) {
        if (!s) return '';
        return String(s).replace(/[&<>"']/g, function(m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [m];
        });
    }

    // auto refresh stories tiap 30 detik
    loadStories();
    setInterval(loadStories, 30000);

    // close modal on click outside
    document.getElementById('storyModal').addEventListener('click', function(e) {
        if (e.target === this) closeStory();
    });
    </script>
    <!-- ================= END STORIES BAR ================= -->

    <div class="container container-main">
        <div class="row g-4">
            <!-- SIDEBAR KIRI -->
            <div class="col-md-3">
                <div class="sidebar">
                    <a href="index.php" class="menu-item">
                        <i class="fa-solid fa-house me-2 text-primary"></i> Beranda
                    </a>

                    <a href="modul/teman.php" class="menu-item">
                        <i class="fa-solid fa-user-group me-2 text-success"></i> Teman
                    </a>

                    <a href="modul/pesan.php" class="menu-item">
                        <i class="fa-solid fa-message me-2 text-info"></i> Pesan
                    </a>

                    <a href="modul/notif_api.php" class="menu-item position-relative">
                        <i class="fa-solid fa-bell me-2 text-warning"></i> Notifikasi
                    </a>
                    <a href="modul/grup.php" class="menu-item">
                        <i class="fa-solid fa-users me-2 text-primary"></i> Grup
                    </a>
                    <a href="modul/marketplace.php" class="menu-item">
                        <i class="fa-solid fa-store me-2 text-danger"></i> Marketplace
                    </a>



                    <hr>

                    <button class="dark-toggle w-100 text-start" id="modeToggle">
                        <i class="fa-solid fa-moon me-2 text-secondary"></i> Ganti Mode
                    </button>

                    <div class="friend-list mt-3">
                        <h6 class="fw-semibold mb-2">Teman Kamu</h6>
                        <?php if (mysqli_num_rows($friends) > 0): ?>
                        <?php while ($f = mysqli_fetch_assoc($friends)):
                $friendPhoto = (!empty($f['Photo']) && file_exists('uploads/'.$f['Photo'])) 
                  ? 'uploads/'.$f['Photo'] 
                  : 'https://ui-avatars.com/api/?name='.urlencode($f['UserName']).'&background=1877f2&color=fff';
            ?>
                        <a href="modul/profil.php?user=<?= urlencode($f['UserName']) ?>" class="friend-item">
                            <img src="<?= htmlspecialchars($friendPhoto) ?>" alt="foto">
                            <span><?= htmlspecialchars($f['UserName']) ?></span>
                        </a>
                        <?php endwhile; else: ?>
                        <p class="text-muted small">Belum ada teman.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- KONTEN TENGAH -->
            <div class="col-md-6">
                <div class="create-post">
                    <form method="post" enctype="multipart/form-data">
                        <textarea name="text" class="form-control mb-3" rows="3"
                            placeholder="Apa yang Anda pikirkan?"></textarea>
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="file" name="image" class="form-control w-50" />
                            <button type="submit" class="btn btn-primary">Posting</button>
                        </div>
                    </form>
                </div>

                <?php while ($row = mysqli_fetch_assoc($result)): 
          $postID = $row['PostID'];
          $likeCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM likes WHERE PostID=$postID"))['total'];
          $isLiked = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM likes WHERE PostID=$postID AND UserName='$username'")) > 0;
        ?>
                <div class="post-card position-relative" id="post<?= $postID ?>">
                    <h6><?= htmlspecialchars($row['UserName']) ?></h6>
                    <small><?= date("d M Y", strtotime($row['Date'])) ?> pukul <?= $row['Time'] ?></small>
                    <p class="mt-2 mb-1"><?= nl2br(htmlspecialchars($row['Text'])) ?></p>
                    <?php if ($row['ImageName']): ?>
                    <img src="uploads/<?= htmlspecialchars($row['ImageName']) ?>" class="rounded mb-2 w-100"
                        alt="post image">
                    <?php endif; ?>

                    <div class="post-actions">
                        <form method="post" class="like-form position-relative">
                            <button type="submit" name="like_post" value="<?= $postID ?>"
                                class="<?= $isLiked ? 'active' : '' ?>">
                                <i class="<?= $isLiked ? 'fa-solid fa-thumbs-up' : 'fa-regular fa-thumbs-up' ?>"></i>
                                Suka (<?= $likeCount ?>)
                            </button>
                        </form>
                        <button class="btn-comment" type="button" data-bs-toggle="collapse"
                            data-bs-target="#comment<?= $postID ?>">
                            <i class="fa-regular fa-comment"></i> Komentar
                        </button>
                    </div>

                    <div class="collapse mt-2" id="comment<?= $postID ?>">
                        <?php
              $comments = mysqli_query($conn, "SELECT * FROM comment WHERE PostID=$postID ORDER BY CommentID DESC");
              if ($comments && mysqli_num_rows($comments) > 0) {
                while ($c = mysqli_fetch_assoc($comments)) {
                  echo "<div class='comment-item'><strong>" . htmlspecialchars($c['UserName']) . ":</strong> " . htmlspecialchars($c['Text']) . "</div>";
                }
              } else {
                echo "<div class='comment-item text-muted fst-italic'>Belum ada komentar.</div>";
              }
            ?>
                        <form method="post" class="mt-2">
                            <div class="input-group">
                                <input type="text" name="comment_text" class="form-control"
                                    placeholder="Tulis komentar..." required>
                                <button type="submit" name="comment_post" value="<?= $postID ?>"
                                    class="btn btn-primary">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- SIDEBAR KANAN -->
            <div class="col-md-3">
                <div class="rightbar">
                    <img src="<?= htmlspecialchars($photo_url) ?>" alt="Foto Profil">
                    <h6><?= htmlspecialchars($username) ?></h6>
                    <a href="modul/profil.php?user=<?= urlencode($username) ?>" class="btn btn-primary w-100 mt-2">Lihat
                        Profil</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const toggle = document.getElementById("modeToggle");
    const body = document.body;

    if (localStorage.getItem("darkmode") === "true") {
        body.classList.add("dark");
    }

    toggle.addEventListener("click", () => {
        body.classList.toggle("dark");
        localStorage.setItem("darkmode", body.classList.contains("dark"));
    });
    </script>
    <script>
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('keyup', () => {
        const q = searchInput.value.trim();
        if (q.length === 0) {
            searchResults.style.display = 'none';
            return;
        }

        fetch('modul/search.php?q=' + encodeURIComponent(q))
            .then(res => res.text())
            .then(data => {
                searchResults.innerHTML = data;
                searchResults.style.display = 'block';
            });
    });

    document.addEventListener('click', (e) => {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });
    </script>
    <script>
    let chatPopup = null;

    function openChatPopup(username) {
        if (chatPopup) chatPopup.remove();
        fetch('modul/chat_popup.php?user=' + encodeURIComponent(username))
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