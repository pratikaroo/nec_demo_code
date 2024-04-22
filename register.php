<?php
require 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }

    if ($username && $email && $password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $exists = $stmt->fetchColumn();
            
            if ($exists) {
                $error = "Username or email already exists.";
            }

            // Insert user into the database
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$username, $email, $hashed_password]);
            
            // Registration successful
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['message'] = "Registration successful. Welcome!";
            header('Location: dashboard.php');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Registration failed due to a server error. Please try again.";            
        }
    } else {
        $error = "Invalid input.";
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
<div class="wrapper">
    <form class="form-signin" action="" method="post">       
        <h2 class="form-signin-heading">Demo Login</h2>
        <?php if(!empty($error)){ ?>
            <div class="alert alert-danger" role="alert"><?=$error;?></div>
        <?php } ?>
        <input type="text" class="form-control" name="email" placeholder="email" required="" autofocus="" />
        <input type="text" class="form-control" name="username" placeholder="username" required="" autofocus="" />
        <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
        <button class="btn btn-primary btn-block" type="submit">Sign Up</button><br/>
        <p>Existing User? <a class="link-underline-light" href="login.php">Login</a></p>
    </form>
  </div>
</body>

</html>