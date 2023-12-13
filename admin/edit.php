<?php 
$db = require './common.php';
require 'nav.php';
$incidentId = $_GET['id'] ?? null;

if (!$incidentId) {
    echo '<script>alert("No ID specified.");window.location.href="searchIncidents.php";</script>';
    exit();
}

$incidentData = $db->get_row("SELECT i.Incident_ID, i.Vehicle_ID, i.People_ID, i.Incident_Date, i.Incident_Report, i.Offence_ID, f.Fine_Amount, f.Fine_Points FROM `Incident` i LEFT JOIN `Fines` f ON f.Incident_ID = i.Incident_ID WHERE i.Incident_ID = $incidentId");

if (!$incidentData) {
    echo '<script>alert("Report not found.");window.location.href="searchIncidents.php";</script>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'Incident_Date' => $_POST['incident_date'] ?? null,
        'Incident_Report' => $_POST['incident_report'] ?? null,
        'Offence_ID' => $_POST['offence_id'] ?? null,
        'Vehicle_ID' => $_POST['vehicle_id'] ?? null,
        'People_ID' => $_POST['people_id'] ?? null,
    ];
    
    $db->update("UPDATE `Incident` SET Incident_Date = ?, Incident_Report = ?, Offence_ID = ?, Vehicle_ID = ?, People_ID = ? WHERE Incident_ID = ?", array_values($data), [$incidentId]);
    
    if ($_SESSION['role'] === 'admin') {
        $fineData = [
            'Fine_Amount' => $_POST['fine_amount'] ?? null,
            'Fine_Points' => $_POST['fine_points'] ?? null,
        ];
        
        if ($db->get_row("SELECT * FROM `Fines` WHERE Incident_ID = ?", [$incidentId])) {
            $db->update("UPDATE `Fines` SET Fine_Amount = ?, Fine_Points = ? WHERE Incident_ID = ?", array_values($fineData), [$incidentId]);
        } else {
            $newFineId = $db->get_row("SELECT MAX(Fine_ID) AS max_id FROM `Fines`")["max_id"] + 1;
            $db->update("INSERT INTO `Fines` (Fine_ID, Fine_Amount, Fine_Points, Incident_ID) VALUES (?, ?, ?, ?)", [$newFineId, ...array_values($fineData), $incidentId]);
        }
    }

    echo '<script>alert("Report updated successfully.");window.location.href="searchIncidents.php";</script>';
    exit();
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #eef2f7;
        margin: 0;
        padding: 0;
    }
    .form-container {
        margin: 40px auto;
        max-width: 600px;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .form-container h2 {
        text-align: center;
        color: #333;
        margin-bottom: 15px;
    }
    .form-container input,
    .form-container select {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .form-container input[type="submit"] {
        background-color: #5c67f2;
        color: #fff;
        font-weight: bold;
        cursor: pointer;
    }
    .form-container input[type="submit"]:hover {
        background-color: #4b54d6;
    }
</style>

<div class="form-container">
    <h2>Edit Incident</h2>
    <form action="edit.php?id=<?= htmlspecialchars($incidentId) ?>" method="post">
        <label for="incident_date">Incident Date</label>
        <input type="date" id="incident_date" name="incident_date" required value="<?= htmlspecialchars($incidentData['Incident_Date']) ?>">
        
        <label for="incident_report">Incident Report</label>
        <input type="text" id="incident_report" name="incident_report" required value="<?= htmlspecialchars($incidentData['Incident_Report']) ?>">
        
        <label for="offence_id">Offence</label>
        <select id="offence_id" name="offence_id">
            <?php
            foreach ($db->get_rows("SELECT * FROM `Offence`") as $offence) {
                $selected = $offence['Offence_ID'] == $incidentData['Offence_ID'] ? 'selected' : '';
                echo "<option value='{$offence['Offence_ID']}' $selected>{$offence['Offence_description']}</option>";
            }
            ?>
        </select>
        
        <label for="vehicle_id">Vehicle</label>
        <select id="vehicle_id" name="vehicle_id">
            <?php
            foreach ($db->get_rows("SELECT * FROM `Vehicle`") as $vehicle) {
                $selected = $vehicle['Vehicle_ID'] == $incidentData['Vehicle_ID'] ? 'selected' : '';
                echo "<option value='{$vehicle['Vehicle_ID']}' $selected>{$vehicle['Vehicle_plate']}</option>";
            }
            ?>
        </select>
        
        <label for="people_id">People</label>
        <select id="people_id" name="people_id">
            <?php
            foreach ($db->get_rows("SELECT * FROM `People`") as $person) {
                $selected = $person['People_ID'] == $incidentData['People_ID'] ? 'selected' : '';
                echo "<option value='{$person['People_ID']}' $selected>{$person['People_name']}</option>";
            }
            ?>
        </select>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <label for="fine_amount">Fine Amount</label>
            <input type="text" id="fine_amount" name="fine_amount" required value="<?= htmlspecialchars($incidentData['Fine_Amount']) ?>">
            
            <label for="fine_points">Fine Points</label>
            <input type="text" id="fine_points" name="fine_points" required value="<?= htmlspecialchars($incidentData['Fine_Points']) ?>">
        <?php endif; ?>
        
        <input type="submit" value="Update Report">
    </form>
</div>
