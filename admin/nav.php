<?php 
$username = require 'user_verify.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', Arial, sans-serif;
        }

        /* Navbar container */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #9575cd; 
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Navbar links */
        .navbar a {
            color: #fff;
            padding: 10px 15px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .navbar a:hover {
            background-color: #7e57c2;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .navbar .active {
            background-color: #7e57c2;
        }

        /* Right section for profile and buttons */
        .navbar .actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Welcome text */
        .welcome-text {
            color: #fff;
            font-size: 16px;
            font-weight: bold;
        }

        /* Buttons */
        .navbar .actions a {
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .navbar .actions a.change-password {
            background-color: #ffa000; 
            color: #fff;
        }

        .navbar .actions a.change-password:hover {
            background-color: #ff8f00; 
        }

        .navbar .actions a.logout {
            background-color: #f44336; 
            color: #fff;
        }

        .navbar .actions a.logout:hover {
            background-color: #d32f2f; 
        }
    </style>
</head>
<body>
<?php

function checkActive($page): string
{
    $current = basename($_SERVER['PHP_SELF']);
    return $current == $page ? 'class="active"' : '';
}

?>
<div class="navbar">
    <nav>
        <a href="index.php" <?= checkActive('index.php'); ?>>Home</a>
        <a href="searchPeople.php" <?= checkActive('searchPeople.php'); ?>>Search People</a>
        <a href="searchVehicle.php" <?= checkActive('searchVehicle.php'); ?>>Search Vehicle</a>
        <a href="addVehicle.php" <?= checkActive('addVehcle.php'); ?>>Add Vehicle</a>
        <a href="report.php" <?= checkActive('report.php'); ?>>Report Incident</a>
        <a href="searchIncidents.php" <?= checkActive('searchIncidents.php'); ?>>Search Incidents</a>
        <?php if ($_SESSION['role'] == 'admin') { ?>
            <a href="officerList.php" <?= checkActive('officerList.php'); ?>>Officer List</a>
        <?php } ?>
        <a href="operateLog.php" <?= checkActive('operateLog.php'); ?>>Operate Log</a>
    </nav>
    <div class="actions">
        <span class="welcome-text">Welcome, <?php echo $username; ?>!</span>
        <a href="changePassword.php" class="change-password">Change Password</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>
</body>
</html>
