<?php
include 'includes/db.php';
session_start();

$appointment_id = intval($_GET['appointment_id']);
$view = $_GET['view'] ?? '';

$isLawyer = ($view === 'lawyer');

// get appointment info
$appointment = $conn->query("SELECT * FROM appointments WHERE id=$appointment_id")->fetch_assoc();
if (!$appointment) exit;

// get lawyer info
$lawyer = $conn->query("SELECT id, name, profile_pic FROM lawyers WHERE id={$appointment['lawyer_id']}")->fetch_assoc();
$lawyer['img'] = $lawyer['profile_pic'] ? $lawyer['profile_pic'] : null;
$lawyer['initial'] = strtoupper(substr($lawyer['name'],0,1));

// get client info
$client = $conn->query("SELECT id, username AS name, profile_pic FROM users WHERE id={$appointment['user_id']}")->fetch_assoc();
$client['img'] = $client['profile_pic'] ? $client['profile_pic'] : null;
$client['initial'] = strtoupper(substr($client['name'],0,1));

$msgs = $conn->prepare("SELECT * FROM messages WHERE appointment_id = ? ORDER BY created_at ASC");
$msgs->bind_param("i", $appointment_id);
$msgs->execute();
$res = $msgs->get_result();

while($m = $res->fetch_assoc()):
  $sender = ($m['sender_type']==='lawyer') ? $lawyer : $client;
?>
  <div class="message <?= ($isLawyer && $m['sender_type'] === 'lawyer') || (!$isLawyer && $m['sender_type'] === 'client') ? 'you' : 'them' ?>">
    <?php if (($isLawyer && $m['sender_type'] === 'client') || (!$isLawyer && $m['sender_type'] === 'lawyer')): ?>
      <?php if ($sender['img']): ?>
        <img src="<?= $sender['img'] ?>" alt="">
      <?php else: ?>
        <div class="profile-icon"><?= $sender['initial'] ?></div>
      <?php endif; ?>
    <?php endif; ?>
    <div class="bubble"><?= htmlspecialchars($m['message']) ?></div>
  </div>
<?php endwhile; ?>
