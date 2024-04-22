<?php
    require 'conn.php';
    session_start();
    if(!isset($_SESSION['user_id']))
        header('Location: login.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id = $_SESSION['user_id'];
        $file = $_FILES['file'];
    
        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (in_array($file['type'], $allowed_types) && $file['size'] <= 10485760) {
            // Secure file name
            $filename = uniqid() . '_' . basename($file['name']);
            $filepath = 'uploads/' . $filename;
    
            // Move file to uploads directory
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Insert file record into database
                try {
                    $stmt = $pdo->prepare('INSERT INTO uploads (user_id, filename, filepath) VALUES (?, ?, ?)');
                    $stmt->execute([$user_id, $filename, $filepath]);
                    $msg = "File Uploaded Successfully";
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    $error = "Error!";
                }
            } else {
                $error = 'Failed to upload file.';
            }
        } else {
            $error = 'Invalid file type or size.';
        }
    }
?>
<html>
<head>
    <title>Demo Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<body class="bg-light">
    <div class="container">
      <div class="py-5 text-center">
        <h2>Upload form</h2><a href="logout.php" class="link-underline-light">Logout</a>      
      </div>
      <div class="row">
        <div class="col-md-8 order-md-1">
          <h4 class="mb-3">Upload Form</h4>
          <?php if(!empty($error)){ ?>
            <div class="alert alert-danger" role="alert"><?=$error;?></div>
          <?php }elseif(!empty($msg)){ ?>
            <div class="alert alert-success" role="alert"><?=$msg;?></div>
          <?php } ?>
          <form class="needs-validation" action="" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="file">Select file(only image/pdf files):</label>
                <input type="file" id="file" name="file" required>
              </div>
            <hr class="mb-4">
            <button class="btn btn-primary btn-block" type="submit">Upload</button>
          </form>
        </div>
      </div>
    </div>
</body>
</html>