<?php
session_start();
include 'includes/db.php';
include 'includes/header.php'; // Your header with navbar

// =============================================
// AUTHENTICATION CHECK
// =============================================
if (!isset($_SESSION['user_id']) && !isset($_SESSION['lawyer_id'])) {
    die("Unauthorized access.");
}

$isLawyer = isset($_SESSION['lawyer_id']);
$userId = $isLawyer ? $_SESSION['lawyer_id'] : $_SESSION['user_id'];
$appointmentId = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

if (!$appointmentId) die("Invalid appointment.");

// =============================================
// VERIFY APPOINTMENT BELONGS TO THIS USER
// =============================================
if ($isLawyer) {
    $check = $conn->prepare("SELECT * FROM appointments WHERE id = ? AND lawyer_id = ? AND status = 'confirmed'");
    $check->bind_param("ii", $appointmentId, $userId);
} else {
    $check = $conn->prepare("SELECT * FROM appointments WHERE id = ? AND user_id = ? AND status = 'confirmed'");
    $check->bind_param("ii", $appointmentId, $userId);
}
$check->execute();
$appointment = $check->get_result()->fetch_assoc();
$check->close();

if (!$appointment) die("Chat not available for this appointment.");

// =============================================
// ✅ FIXED: FETCH PARTICIPANTS (Correct Lawyer/Client Show)
// =============================================
if ($isLawyer) {
    // Fetch logged-in lawyer info
    $stmt = $conn->prepare("SELECT id, name, profile_pic FROM lawyers WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['lawyer_id']);
    $stmt->execute();
    $lawyer = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Fetch the correct client based on this appointment
    $stmt = $conn->prepare("
        SELECT u.id, u.username AS name, u.profile_pic
        FROM users u
        INNER JOIN appointments a ON a.user_id = u.id
        WHERE a.id = ? AND a.lawyer_id = ?
    ");
    $stmt->bind_param("ii", $appointmentId, $_SESSION['lawyer_id']);
    $stmt->execute();
    $client = $stmt->get_result()->fetch_assoc();
    $stmt->close();

} else {
    // Fetch logged-in client info
    $stmt = $conn->prepare("SELECT id, username AS name, profile_pic FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $client = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Fetch the correct lawyer from the appointment
    $stmt = $conn->prepare("
        SELECT l.id, l.name, l.profile_pic
        FROM lawyers l
        INNER JOIN appointments a ON a.lawyer_id = l.id
        WHERE a.id = ? AND a.user_id = ?
    ");
    $stmt->bind_param("ii", $appointmentId, $_SESSION['user_id']);
    $stmt->execute();
    $lawyer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Safe defaults
$lawyer['img'] = $lawyer['profile_pic'] ? $lawyer['profile_pic'] : null;
$lawyer['initial'] = strtoupper(substr($lawyer['name'], 0, 1));
$client['img'] = $client['profile_pic'] ? $client['profile_pic'] : null;
$client['initial'] = strtoupper(substr($client['name'], 0, 1));

// =============================================
// SEND MESSAGE
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $senderType = $isLawyer ? 'lawyer' : 'client';
    $stmt = $conn->prepare("INSERT INTO messages (appointment_id, sender_type, sender_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $appointmentId, $senderType, $userId, $msg);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ======================================================= */
/* === NAVBAR/SEARCH BAR FIXES === */
/* ======================================================= */
.navbar a { text-decoration: none !important; }
.navbar .search-container, .navbar .search-form { 
    background-color: transparent !important;
    box-shadow: none !important;
    border: none !important;
}
.navbar .search-input, 
.navbar input[type="text"] {
    background-color: transparent !important;
    border: 1px solid #444;
    color: #fff;
}
.navbar .search-input:focus,
.navbar input[type="text"]:focus {
    outline: none;
    box-shadow: none;
}
.navbar .search-icon {
    background-color: #3b82f6;
    color: white;
    border-radius: 50%;
}

/* ======================================================= */
/* === CHAT BOX STYLES === */
/* ======================================================= */
body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; }
.chat-box { max-width: 800px; margin: 40px auto; background: #fff; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.15); overflow: hidden; }
.chat-header { background-color: #1e293b; color: #fff; padding: 15px; display: flex; align-items: center; gap: 10px; }
.chat-header img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; }
.chat-header .profile-icon { width: 45px; height: 45px; border-radius: 50%; background-color: #3b82f6; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; }
.chat-header .name { font-weight: 600; font-size: 18px; }
.chat-header .status { font-size: 13px; color: #a0aec0; }

.messages { height: 450px; overflow-y: auto; padding: 20px; background: #f8fafc; }
.message { margin-bottom: 15px; display: flex; align-items: flex-end; }
.message.you { justify-content: flex-end; }
.message.them { justify-content: flex-start; }
.message img { width: 35px; height: 35px; border-radius: 50%; margin-right: 8px; }
.message .profile-icon { width: 35px; height: 35px; border-radius: 50%; background-color: #3b82f6; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; margin-right: 8px; }
.message.you img, .message.you .profile-icon { display: none; }

.bubble { max-width: 70%; padding: 10px 15px; border-radius: 20px; word-wrap: break-word; }
.message.you .bubble { background-color: #3b82f6; color: white; border-bottom-right-radius: 0; }
.message.them .bubble { background-color: #e2e8f0; color: #1e293b; border-bottom-left-radius: 0; }

.typing { font-style: italic; color: gray; padding-left: 20px; }

form { display: flex; gap: 10px; padding: 15px; background: #f1f5f9; }
form input { flex: 1; border: 1px solid #ccc; border-radius: 20px; padding: 10px 15px; }
form button { border: none; background: #3b82f6; color: white; padding: 10px 20px; border-radius: 20px; }
form button:hover { background: #2563eb; }
</style>
</head>
<body>

<div class="chat-box">
  <div class="chat-header">
    <?php if ($isLawyer): ?>
      <?php if ($client['img']): ?>
        <img src="<?= htmlspecialchars($client['img']) ?>" alt="">
      <?php else: ?>
        <div class="profile-icon"><?= htmlspecialchars($client['initial']) ?></div>
      <?php endif; ?>
      <div>
        <div class="name"><?= htmlspecialchars($client['name']) ?></div>
        <div class="status" id="status">Active now</div>
      </div>
    <?php else: ?>
      <?php if ($lawyer['img']): ?>
        <img src="<?= htmlspecialchars($lawyer['img']) ?>" alt="">
      <?php else: ?>
        <div class="profile-icon"><?= htmlspecialchars($lawyer['initial']) ?></div>
      <?php endif; ?>
      <div>
        <div class="name"><?= htmlspecialchars($lawyer['name']) ?></div>
        <div class="status" id="status">Active now</div>
      </div>
    <?php endif; ?>
  </div>

  <div class="messages" id="messages"></div>
  <div id="typing" class="typing"></div>

  <form method="POST" id="chatForm">
    <input type="text" name="message" id="msgInput" placeholder="Type your message..." autocomplete="off" required>
    <button>Send</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const messagesDiv = document.getElementById("messages");
const typingDiv = document.getElementById("typing");
const form = document.getElementById("chatForm");
const msgInput = document.getElementById("msgInput");

function loadMessages() {
  fetch("fetch_messages.php?appointment_id=<?= $appointmentId ?>&view=<?= $isLawyer ? 'lawyer' : 'client' ?>")
    .then(res => res.text())
    .then(html => {
      const shouldScroll = messagesDiv.scrollTop + messagesDiv.clientHeight >= messagesDiv.scrollHeight - 20;
      messagesDiv.innerHTML = html;
      if (shouldScroll) messagesDiv.scrollTop = messagesDiv.scrollHeight;
    });
}
loadMessages();
setInterval(loadMessages, 3000);

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(form);
  await fetch("chat.php?appointment_id=<?= $appointmentId ?>", { method: "POST", body: formData });
  msgInput.value = "";
  loadMessages();
});

msgInput.addEventListener("input", () => {
  fetch("typing.php?appointment_id=<?= $appointmentId ?>&sender=<?= $isLawyer ? 'lawyer' : 'client' ?>");
});

setInterval(() => {
  fetch("typing_status.php?appointment_id=<?= $appointmentId ?>&viewer=<?= $isLawyer ? 'lawyer' : 'client' ?>")
    .then(res => res.text())
    .then(status => typingDiv.textContent = status);
}, 2000);
</script>

</body>
</html>
