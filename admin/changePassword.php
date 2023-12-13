<?php
$db = require './common.php';
require 'nav.php';
?>

<style>
    body {
        font-family: 'Poppins', Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .change-password-container {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: 50px auto;
    }

    .change-password-container h2 {
        margin-bottom: 20px;
        color: #9575cd;
        text-align: center;
    }

    .change-password-container input[type="password"] {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #9575cd;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .change-password-container input[type="password"]:focus {
        border-color: #9575cd;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
    }

    .change-password-container input[type="submit"] {
        width: 100%;
        padding: 12px;
        background-color: #9575cd;
        border: none;
        border-radius: 5px;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .change-password-container input[type="submit"]:hover {
        background-color: #9575cd;
    }

    .error {
        color: red;
        margin-bottom: 20px;
        text-align: center;
        font-size: 14px;
    }
</style>

<div class="change-password-container">
    <h2>Change Password</h2>
    <form action="changePassword.php" method="post" onsubmit="return validateForm()">
        <input type="password" id="oldPassword" name="oldPassword" placeholder="Old Password" required>
        <input type="password" id="newPassword" name="newPassword" placeholder="New Password" required>
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm New Password" required>
        <div id="error" class="error"></div>
        <input type="submit" value="Change Password">
    </form>
</div>

<script>
    function validateForm() {
        const oldPassword = document.getElementById('oldPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const errorElement = document.getElementById('error');

        if (oldPassword === newPassword) {
            errorElement.textContent = 'Old password and new password cannot be the same.';
            return false;
        }

        if (newPassword !== confirmPassword) {
            errorElement.textContent = 'New password and confirm password do not match.';
            return false;
        }

        return true;
    }
</script>

<?php
if (isPostRequest()) {
    $oldPassword = $_POST['oldPassword'] ?? null;
    $newPassword = $_POST['newPassword'] ?? null;
    if ($oldPassword == null || $newPassword == null) {
        exit('<script>alert(\'Invalid old-password or new-password\')</script>');
    }
    
    if ($_SESSION['role'] == 'admin') {
        logger('Admin changed password');
        $update = 'update `Admins` set `Password` = "' . $newPassword . '" where `Username` = "' . $_SESSION['user'] . '" and `Password` = "' . $oldPassword . '"';
    } else {
        logger('Officer changed password');
        $update = 'update `Officers` set `Password` = "' . $newPassword . '" where `Username` = "' . $_SESSION['user'] . '" and `Password` = "' . $oldPassword . '"';
    }

    $r = $db->update($update);
    if (!$r) exit('<script>alert(\'Invalid old password\')</script>');
    exit('<script>window.location.href = "./logout.php"</script>');
}
?>
