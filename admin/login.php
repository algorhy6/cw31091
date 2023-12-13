<?php
$db = require './common.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(to right, #7e57c2, #9575cd);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            width: 80%;
            max-width: 1200px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .left, .right {
            flex: 1;
            padding: 40px;
        }

        .left {
            background-color: #7e57c2;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }

        .left h1 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .left p {
            font-size: 18px;
            line-height: 1.6;
        }

        .right {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .login-container {
            width: 100%;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Poppins', Arial, sans-serif;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .login-container input[type="submit"] {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, #7e57c2, #9575cd);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .login-container input[type="submit"]:hover {
            background: linear-gradient(to right, #9575cd, #7e57c2);
            transform: translateY(-2px);
        }

        .error-message {
            margin-bottom: 20px;
            color: red;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

    </style>
</head>
<body>
<?php
$error = false;
if (isPostRequest()) {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    if (!$username || !$password) {
        $error = true;
    } else {
        $sql = "SELECT * FROM `Officers` WHERE username = '$username' AND password = '$password'";
        $result = $db->get_row($sql);
        if ($result == null) {
            $sql = "SELECT * FROM `Admins` WHERE username = '$username' AND password = '$password'";
            $result = $db->get_row($sql);
            if ($result == null) {
                $error = true;
            } else {
                $_SESSION['user'] = $result['Username'];
                $_SESSION['role'] = 'admin';
                logger("Admin logged in");
                echo "<script>alert('Login success');window.location.href=\"./index.php\"</script>";
            }
        } else {
            $_SESSION['user'] = $result['Username'];
            $_SESSION['role'] = 'officer';
            logger("Officer logged in");
            echo "<script>alert('Login success');window.location.href=\"./index.php\"</script>";
        }
    }
}
?>
<div class="container">
    <div class="left">
        <h1>Welcome Back!</h1>
        <p>Please login to your account</p>
    </div>
    <div class="right">
        <div class="login-container">
            <h2>Login</h2>
            <form action="login.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>

                <?php if ($error): ?>
                    <div class="error-message">
                        Incorrect username or password
                    </div>
                <?php endif; ?>
                <input type="submit" value="Login">
            </form>
        </div>
    </div>
</div>
</body>
</html>
