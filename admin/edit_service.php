<?php
$DB_HOST='localhost';
$DB_USER='root';
$DB_PASS='';
$DB_NAME='lawyer_booking';
$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($conn->connect_error) die("DB Connection Failed: ".$conn->connect_error);

$id = $_GET['id'] ?? null;
if(!$id || !is_numeric($id)){
    die("⚠️ Invalid service ID.");
}
$id = intval($id);

$msg="";
$stmt=$conn->prepare("SELECT * FROM services WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$res=$stmt->get_result();
$service=$res->fetch_assoc();
$stmt->close();

if(!$service) die("⚠️ Service not found.");

if($_SERVER['REQUEST_METHOD']==='POST'){
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $expertise = trim($_POST['expertise'] ?? '');
    if($title && $description){
        $stmt=$conn->prepare("UPDATE services SET title=?, subtitle=?, description=?, expertise=? WHERE id=?");
        $stmt->bind_param("ssssi",$title,$subtitle,$description,$expertise,$id);
        if($stmt->execute()){
            $msg="✅ Service updated successfully!";
            $service['title']=$title;
            $service['subtitle']=$subtitle;
            $service['description']=$description;
            $service['expertise']=$expertise;

              header("Location: manage_service.php?msg=success");
        }else $msg="❌ Error: ".$conn->error;
        $stmt->close();
    }else $msg="⚠️ Title and Description are required.";
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Service</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:'Segoe UI',Arial,sans-serif;background:#f7f7f7;padding:40px;color:#111;}
.container{max-width:650px;margin:0 auto;background:#fff;padding:30px;border-radius:14px;box-shadow:0 6px 20px rgba(0,0,0,0.1);}
h1{text-align:center;margin-bottom:25px;font-size:28px;}
form label{display:block;font-weight:600;margin:14px 0 6px;}
input,textarea{width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;font-size:15px;transition:.3s;}
input:focus,textarea:focus{border-color:#000;outline:none;box-shadow:0 0 0 3px rgba(0,0,0,0.08);}
textarea{min-height:120px;resize:vertical;}
button{display:block;margin:20px auto 0;padding:12px 24px;background:#111;color:#fff;font-size:16px;border:none;border-radius:8px;cursor:pointer;transition:.3s;}
button:hover{background:#333;transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,0.2);}
.msg{text-align:center;margin-bottom:15px;font-weight:600;color:#007700;}
.msg.error{color:#bb0000;}
</style>
</head>
<body>
<div class="container">
<h1>Edit Service</h1>
<?php if($msg): ?>
<div class="msg <?= strpos($msg,'Error')!==false||strpos($msg,'⚠️')!==false?'error':''?>">
<?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>
<form method="post">
<label for="title">Service Title *</label>
<input type="text" id="title" name="title" value="<?= htmlspecialchars($service['title']); ?>" required>
<label for="subtitle">Subtitle</label>
<input type="text" id="subtitle" name="subtitle" value="<?= htmlspecialchars($service['subtitle']); ?>">
<label for="description">Description *</label>
<textarea id="description" name="description" required><?= htmlspecialchars($service['description']); ?></textarea>
<label for="expertise">Expertise (comma separated)</label>
<input type="text" id="expertise" name="expertise" value="<?= htmlspecialchars($service['expertise']); ?>">
<button type="submit">💾 Update Service</button>
</form>
</div>
</body>
</html>
