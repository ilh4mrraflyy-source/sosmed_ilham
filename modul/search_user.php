<?php
session_start();
$conn = mysqli_connect("localhost", "root", "rpl12345", "db_medsos");

$username = $_SESSION['user']['Username'];
$q = mysqli_real_escape_string($conn, $_GET['q'] ?? '');

if ($q === '') {
  $res = mysqli_query($conn, "
    SELECT CASE 
             WHEN UserA = '$username' THEN UserB 
             ELSE UserA 
           END AS FriendName
    FROM friends 
    WHERE UserA = '$username' OR UserB = '$username'
  ");
} else {
  $res = mysqli_query($conn, "
    SELECT Username FROM user 
    WHERE Username LIKE '%$q%' AND Username != '$username'
  ");
}

while ($r = mysqli_fetch_assoc($res)) {
  $user = $r['FriendName'] ?? $r['Username'];
  echo "
    <a href='?chat=" . urlencode($user) . "' class='friend-item'>
      <img src='https://ui-avatars.com/api/?name=$user&background=1877f2&color=fff'>
      <div>
        <strong>$user</strong><br>
        <small>Aktif sekarang</small>
      </div>
      <span class='online-dot'></span>
    </a>
  ";
}
?>