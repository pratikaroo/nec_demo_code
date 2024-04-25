<?php
require 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');  //Cleans the inputs
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

    $password = $_POST['password'];

    if ($username && $email && $password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $exists = $stmt->fetchColumn();
            
            if ($exists) {
                $_SESSION['reg_error'] = "Username or email already exists. Please choose a different one.";
                header('Location: register.php');
                exit;
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <form class="form-signin" action="" method="post" id="registerForm">       
        <h2 class="form-signin-heading">Demo Login</h2>
        <?php if(!empty($error)){ ?>
            <div class="alert alert-danger" role="alert"><?=$error;?></div>
        <?php }elseif(isset($_SESSION['reg_error'])){ ?>
            <div class="alert alert-danger" role="alert"><?=$_SESSION['reg_error'];?></div>
        <?php unset($_SESSION['reg_error']); } ?>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
            <div class="error" id="usernameError"></div>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
            <div class="error" id="emailError"></div>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <div class="error" id="passwordError"></div>
        </div>   
        <button class="btn btn-primary btn-block" type="submit" >Sign Up</button><br/>
        <p>Existing User? <a class="link-underline-light" href="login.php">Login</a></p>
    </form>
  </div>
</body>
<script>
$(document).ready(function() {
    $('#registerForm').on('submit', function(event) {
        // Initialize isValid flag
        let isValid = true;

        // Clear any previous errors
        $('.error').text('');

        // Validate username
        const username = $('#username').val();
        if (!/^[a-zA-Z0-9_]{4,30}$/.test(username)) {
            isValid = false;
            $('#usernameError').text('Username must be 4-30 characters and can contain letters, numbers, and underscores only.');
        }

        // Validate email
        const email = $('#email').val();
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(email)) {
            isValid = false;
            $('#emailError').text('Invalid email format.');
        }

        // Validate password
        const password = $('#password').val();
        if (password.length < 6) {
            isValid = false;
            $('#passwordError').text('Password must be at least 6 characters long.');
        }

        // If validation fails, prevent form submission
        if (!isValid) {
            event.preventDefault();
        }
    });
});
</script>
</html>