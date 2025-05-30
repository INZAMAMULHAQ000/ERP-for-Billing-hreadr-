<?php
session_start();
require_once "config/database.php";

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if($username === "admin" && $password === "admin123") {
        $_SESSION['loggedin'] = true;
        header("location: billing.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supermarket Billing System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #000;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px #0ff,
                        inset 0 0 20px rgba(0, 255, 255, 0.5);
            max-width: 400px;
            width: 90%;
        }
        .neon-text {
            color: #fff;
            text-shadow: 0 0 5px #fff,
                         0 0 10px #0ff,
                         0 0 20px #0ff,
                         0 0 40px #0ff;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #0ff;
            color: #fff;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #0ff;
            box-shadow: 0 0 10px #0ff;
            color: #fff;
        }
        .btn-neon {
            background: transparent;
            border: 2px solid #0ff;
            color: #fff;
            text-shadow: 0 0 5px #0ff;
            box-shadow: 0 0 10px #0ff;
            transition: all 0.3s ease;
        }
        .btn-neon:hover {
            background: #0ff;
            color: #000;
            box-shadow: 0 0 20px #0ff;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4 neon-text">Billing System Login</h2>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn btn-neon w-100">Login</button>
        </form>
        <div class="mt-3 text-center">
            <small class="text-muted">Default credentials: admin / admin123</small>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 