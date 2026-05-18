<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$error = '';
$success = '';

// Define upload directory
$uploadDir = __DIR__ . '/../assets/uploads/papers/';

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $abstract = sanitize($_POST['abstract']);
    
    if (empty($title) || empty($_FILES['file']['name'])) {
        $error = "Title and file are required";
    } else {
        $allowed = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $error = "Only PDF, DOC, DOCX allowed";
        } elseif ($_FILES['file']['size'] > 10 * 1024 * 1024) {
            $error = "File too large (max 10MB)";
        } else {
            $newName = time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = $uploadDir . $newName;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                $stmt = $pdo->prepare("INSERT INTO papers (user_id, title, abstract, file_path) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$_SESSION['user_id'], $title, $abstract, $newName])) {
                    $success = "Paper uploaded successfully";
                } else {
                    $error = "Database error";
                }
            } else {
                $error = "Upload failed. Check folder permissions.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html><head><title>Upload Paper</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4">
    <div class="row justify-content-center"><div class="col-md-8">
        <div class="card"><div class="card-header">Upload Research Paper</div>
        <div class="card-body">
            <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label>Abstract</label><textarea name="abstract" class="form-control" rows="5"></textarea></div>
                <div class="mb-3"><label>File (PDF/DOC/DOCX, max 10MB)</label><input type="file" name="file" class="form-control" required></div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div></div>
    </div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>