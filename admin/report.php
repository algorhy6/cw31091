<?php 
$db = require './common.php';
require 'nav.php';
if (isPostRequest()) {
    $incident_date = $_POST['incident_date'] ?? null;
    $incident_report = $_POST['incident_report'] ?? null;
    $offence_id = $_POST['offence_id'] ?? null;
    $vehicle_id = $_POST['vehicle_id'] ?? null;
    $people_id = $_POST['people_id'] ?? null;
    $incident_date = date('Y-m-d', strtotime($incident_date));

    $latest_id_sql = 'select max(Incident_ID) as max_id from Incident';
    $latest_id = $db->get_row($latest_id_sql)['max_id'] + 1;

    $sql = "INSERT INTO `Incident` (Incident_ID, Incident_Date, Incident_Report, Offence_ID, Vehicle_ID, People_ID) VALUES ('$latest_id', '$incident_date', '$incident_report', '$offence_id', '$vehicle_id', '$people_id')";
    if (!$db->update($sql)) exit('Error: ' . $db->error);
    logger('add report');
    echo '<script>alert("Report added successfully");window.location.href="report.php"</script>';
    exit();
}
?>

<style>
    body {
        font-family: 'Poppins', Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .form-container {
        margin: 40px auto;
        max-width: 600px;
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #9575cd;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
        text-align: center;
        color: #9575cd;
        margin-bottom: 20px;
    }

    .form-container form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-container label {
        font-size: 14px;
        color: #333;
        font-weight: bold;
    }

    .form-container input,
    .form-container select {
        width: 100%;
        padding: 10px;
        border: 1px solid #9575cd;
        border-radius: 5px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s;
    }

    .form-container input:focus,
    .form-container select:focus {
        border-color: #9575cd;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
    }

    .form-container input[type="submit"] {
        background-color: #9575cd;
        border: none;
        color: #fff;
        padding: 10px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .form-container input[type="submit"]:hover {
        background-color: #9575cd;
    }
</style>

<div class="form-container">
    <h2>Report Incident</h2>
    <form action="report.php" method="post">

        <label for="incident_date">Incident Date</label>
        <input type="date" id="incident_date" name="incident_date" placeholder="Incident Date" required>

        <label for="incident_report">Incident Report</label>
        <input type="text" id="incident_report" name="incident_report" placeholder="Incident Report" required>

        <label for="offence_id">Offence</label>
        <select id="offence_id" name="offence_id">
            <?php
            $offence = $db->get_rows("SELECT * from `Offence`");
            ?>
            <?php foreach ($offence as $owner): ?>
                <option value="<?= htmlspecialchars($owner['Offence_ID']) ?>"><?= htmlspecialchars($owner['Offence_description']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="vehicle_id">Vehicle</label>
        <select id="vehicle_id" name="vehicle_id">
            <?php
            $vehicle = $db->get_rows("SELECT * from `Vehicle`");
            ?>
            <?php foreach ($vehicle as $owner): ?>
                <option value="<?= htmlspecialchars($owner['Vehicle_ID']) ?>"><?= htmlspecialchars($owner['Vehicle_plate']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="people_id">People</label>
        <select id="people_id" name="people_id">
            <?php
            $people = $db->get_rows("SELECT * from `People`");
            ?>
            <?php foreach ($people as $owner): ?>
                <option value="<?= htmlspecialchars($owner['People_ID']) ?>"><?= htmlspecialchars($owner['People_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Submit">
    </form>
</div>
