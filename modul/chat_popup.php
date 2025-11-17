<?php
session_start();
$conn = mysqli_connect("localhost", "root", "rpl12345", "db_medsos");
if (!$conn) die("Koneksi gagal");

if (!isset($_SESSION['user'])) {
  exit("Belum login");
}

$user = $_SESSION['user']['Username'];
$friend = $_GET['user'] ?? '';

if ($friend === '') {
  exit("Tidak ada penerima pesan");
}

?>
<div class="chat-box card shadow-sm"
    style="width:300px; height:400px; position:fixed; bottom:10px; right:10px; z-index:9999;">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><?= htmlspecialchars($friend) ?></span>
        <button class="btn-close btn-close-white btn-sm" onclick="closeChatPopup()"></button>
    </div>

    <div class="card-body bg-light overflow-auto" id="chatMessages" style="height:300px;"></div>

    <div class="card-footer p-2">
        <form id="chatForm" class="d-flex">
            <input type="hidden" name="to" value="<?= htmlspecialchars($friend) ?>">
            <input type="text" name="message" id="chatMessageInput" class="form-control me-2"
                placeholder="Tulis pesan..." autocomplete="off">
            <button class="btn btn-primary"><i class="fa fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<script>
function fetchMessages() {
    fetch('modul/fetch_messages.php?user=<?= urlencode($friend) ?>')
        .then(res => res.text())
        .then(data => {
            document.getElementById('chatMessages').innerHTML = data;
            document.getElementById('chatMessages').scrollTop = document.getElementById('chatMessages')
                .scrollHeight;
        });
}

setInterval(fetchMessages, 1500);
fetchMessages();

document.getElementById('chatForm').addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch('modul/send_message.php', {
            method: 'POST',
            body: formData
        })
        .then(() => {
            document.getElementById('chatMessageInput').value = '';
            fetchMessages();
        });
});
</script>