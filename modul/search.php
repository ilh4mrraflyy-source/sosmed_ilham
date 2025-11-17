<?php
session_start();
$conn = mysqli_connect("localhost", "root", "rpl12345", "db_medsos");
if (!$conn) die("Koneksi gagal");

$q = mysqli_real_escape_string($conn, $_GET['q'] ?? '');

if ($q === '') {
  echo '';
  exit;
}

$results = [];

// Cari User
$userQuery = mysqli_query($conn, "SELECT Username, Photo FROM user WHERE Username LIKE '%$q%' LIMIT 5");
while ($u = mysqli_fetch_assoc($userQuery)) {
  $u['type'] = 'user';
  $results[] = $u;
}

// Cari Grup
$groupQuery = mysqli_query($conn, "SELECT GroupID, GroupName, GroupPhoto FROM tbl_groups WHERE GroupName LIKE '%$q%' LIMIT 5");
while ($g = mysqli_fetch_assoc($groupQuery)) {
  $g['type'] = 'group';
  $results[] = $g;
}

// Cari Post
$postQuery = mysqli_query($conn, "SELECT PostID, Text, UserName FROM post WHERE Text LIKE '%$q%' LIMIT 5");
while ($p = mysqli_fetch_assoc($postQuery)) {
  $p['type'] = 'post';
  $results[] = $p;
}

// Output hasil
if (count($results) == 0) {
  echo '<div class="p-2 text-muted small">Tidak ada hasil ditemukan</div>';
} else {
  foreach ($results as $r) {
    if ($r['type'] === 'user') {
      $photo = (!empty($r['Photo']) && file_exists('../uploads/'.$r['Photo'])) ? '../uploads/'.$r['Photo'] : 'https://ui-avatars.com/api/?name='.urlencode($r['Username']).'&background=1877f2&color=fff';
      echo "
        <a href='modul/profil.php?user={$r['Username']}' class='d-flex align-items-center p-2 text-dark text-decoration-none border-bottom'>
          <img src='$photo' width='40' height='40' class='rounded-circle me-2'>
          <span>{$r['Username']}</span>
        </a>
      ";
    } elseif ($r['type'] === 'group') {
      $photo = (!empty($r['GroupPhoto']) && file_exists('../uploads/'.$r['GroupPhoto'])) ? '../uploads/'.$r['GroupPhoto'] : 'https://source.unsplash.com/60x60/?group';
      echo "
        <a href='modul/grup_detail.php?id={$r['GroupID']}' class='d-flex align-items-center p-2 text-dark text-decoration-none border-bottom'>
          <img src='$photo' width='40' height='40' class='rounded-circle me-2'>
          <span>{$r['GroupName']}</span>
        </a>
      ";
    } else {
      echo "
        <a href='index.php#post{$r['PostID']}' class='d-block p-2 text-dark text-decoration-none border-bottom'>
          <strong>{$r['UserName']}</strong><br>
          <span class='small text-muted'>".htmlspecialchars(substr($r['Text'],0,60))."...</span>
        </a>
      ";
    }
  }
}
?>