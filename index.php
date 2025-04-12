<?php
session_start();
require 'config.php';

$error_message = ""; // Inisialisasi pesan error

// Cek apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirect ke halaman dashboard jika sudah login
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi panjang password
    if (strlen($password) < 8) {
        $error_message = "Password harus terdiri dari minimal 8 karakter.";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && password_verify($password, $result['password'])) {
            $_SESSION['user_id'] = $result['id'];

            // Ambil tugas untuk pengguna yang login
            $user_id = $result['id'];
            $task_stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
            $task_stmt->bind_param("i", $user_id);
            $task_stmt->execute();
            $tasks = $task_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Simpan tugas ke dalam session
            $_SESSION['tasks'] = $tasks;

            header("Location: dashboard.php"); // Redirect ke halaman dashboard
            exit;
        } else {
            $error_message = "Username atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            background: url('pink.png') no-repeat;
            background-size: cover;
            background-position: center;
        }

        .wrapper {
            width: 420px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(20px);
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
            color: #fff;
            border-radius: 10px;
            padding: 30px 40px;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
            margin-bottom: 20px;
        }

        .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            margin: 30px 0;
        }

        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            outline: none;
            border: 2px solid rgba(255, 255, 255, .2);
            border-radius: 40px;
            font-size: 16px;
            color: #fff;
            padding: 20px 45px 20px 20px;
        }

        .input-box input::placeholder {
            color: #fff;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            font-size: 14.5px;
            margin: -15px 0 15px;
        }

        .remember-forgot label input {
            accent-color: #fff;
            margin-right: 3px
        }

        .remember-forgot a {
            color: #fff;
            text-decoration: none;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            height: 45px;
            background: #fff;
            border: none;
            outline: none;
            border-radius: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            cursor: pointer;
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }

        .error-message {
            color: #f44336;
            text-align: center;
            margin-bottom: 10px;
        }
</style>
</head>
<body>
<div class="wrapper">
<form method="POST" action="">
    <h1>Login</h1>
    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
    <div class="input-box">
        <input type="text" name="username" placeholder="Username" required>
        <i class='bx bxs-user'></i>
    </div>
    <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
        <i class='bx bxs-lock-alt'></i>
    </div>
    <div class="remember-forgot">
        <label><input type="checkbox" name="remember">Remember me</label>
        <a href="#">Forgot password?</a>
    </div>
    <button type="submit" class="btn">Login</button>
    <div class="regis-link">
        <p>Don't have an account? <a href="register.php" style="color: white; text-decoration: none;">Register</a></p>
    </div>
</form>
</div>
</body>
</html>