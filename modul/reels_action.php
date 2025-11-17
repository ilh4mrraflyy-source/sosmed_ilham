<?php
session_start();
header('Content-Type: application/json');
$host = "localhost"; $user = "root"; $pass = "rpl12345"; $db = "db_medsos";
$conn = mysqli_connect($host,$user,$pass,$db);
if (!$conn) { echo json_encode(['error'=>'db']); exit; }

if (!isset($_SESSION['user'])) { echo json_encode(['error'=>'auth']); exit; }
$username = $_SESSION['user']['Username'];

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'like') {
    $reelID = intval($_POST['reelID']);
    // check exist
    $check = mysqli_query($conn, "SELECT * FROM reels_likes WHERE ReelID=$reelID AND UserName='".mysqli_real_escape_string($conn,$username)."'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO reels_likes (ReelID, UserName) VALUES ($reelID, '".mysqli_real_escape_string($conn,$username)."')");
        mysqli_query($conn, "UPDATE reels SET LikesCount = LikesCount + 1 WHERE ReelID=$reelID");
        echo json_encode(['status'=>'liked']);
    } else {
        mysqli_query($conn, "DELETE FROM reels_likes WHERE ReelID=$reelID AND UserName='".mysqli_real_escape_string($conn,$username)."'");
        mysqli_query($conn, "UPDATE reels SET LikesCount = GREATEST(LikesCount - 1, 0) WHERE ReelID=$reelID");
        echo json_encode(['status'=>'unliked']);
    }
    exit;
}

if ($action === 'comment') {
    $reelID = intval($_POST['reelID']);
    $text = trim($_POST['text'] ?? '');
    if ($text === '') { echo json_encode(['error'=>'empty']); exit; }
    mysqli_query($conn, "INSERT INTO reels_comments (ReelID, UserName, Text) VALUES ($reelID, '".mysqli_real_escape_string($conn,$username)."', '".mysqli_real_escape_string($conn,$text)."')");
    mysqli_query($conn, "UPDATE reels SET CommentsCount = CommentsCount + 1 WHERE ReelID=$reelID");
    // return new comment
    $id = mysqli_insert_id($conn);
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM reels_comments WHERE CommentID=$id"));
    echo json_encode(['status'=>'ok','comment'=>$row]);
    exit;
}

// fetch comments
if ($action === 'comments') {
    $reelID = intval($_GET['reelID']);
    $res = mysqli_query($conn, "SELECT * FROM reels_comments WHERE ReelID=$reelID ORDER BY CommentID ASC");
    $out = [];
    while ($r = mysqli_fetch_assoc($res)) $out[] = $r;
    echo json_encode($out);
    exit;
}

// fetch feed (simple)
if ($action === 'feed') {
    $limit = intval($_GET['limit'] ?? 20);
    $res = mysqli_query($conn, "SELECT * FROM reels ORDER BY CreatedAt DESC LIMIT $limit");
    $out = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $r['isLiked'] = (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reels_likes WHERE ReelID=".$r['ReelID']." AND UserName='".mysqli_real_escape_string($conn,$username)."'"))>0)?1:0;
        $out[] = $r;
    }
    echo json_encode($out);
    exit;
}

// get single reel link info (for share)
if ($action === 'get') {
    $reelID = intval($_GET['reelID']);
    $r = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM reels WHERE ReelID=$reelID"));
    if (!$r) { echo json_encode(['error'=>'notfound']); exit; }
    echo json_encode($r);
    exit;
}

echo json_encode(['error'=>'noaction']);