<?php
require 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Login failed.';
        }
    } else {
        $error = 'Invalid input.';
    }
}
?>
<html>
<head>
    <title>Demo Login</title>
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
        <input type="text" class="form-control" name="username" placeholder="username" required="" autofocus="" />
        <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
        <button class="btn btn-primary btn-block" type="submit">Login</button><br/>
        <p>New User? <a class="link-underline-light" href="register.php">Sign Up</a></p>
    </form>
  </div>
</body>
</html>