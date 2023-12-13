<?php 
require './common.php';
require 'nav.php';
$incidentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$incidentId) {
    echo '<script>alert("Invalid ID."); window.location.href="searchIncidents.php";</script>';
    exit();
}

$query = "SELECT i.Incident_ID, i.Vehicle_ID, i.People_ID, i.Incident_Date, i.Incident_Report, i.Offence_ID, f.Fine_Amount, f.Fine_Points 
          FROM Incident i 
          LEFT JOIN Fines f ON f.Incident_ID = i.Incident_ID 
          WHERE i.Incident_ID = ?";
$incidentData = $db->get_row($query, [$incidentId]);

if (!$incidentData) {
    echo '<script>alert("Incident not found."); window.location.href="searchIncidents.php";</script>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $incidentDate = $_POST['incident_date'] ?? '';
    $incidentReport = $_POST['incident_report'] ?? '';
    $offenceId = $_POST['offence_id'] ?? '';
    $vehicleId = $_POST['vehicle_id'] ?? '';
    $peopleId = $_POST['people_id'] ?? '';
    
    $updateQuery = "UPDATE Incident SET Incident_Date = ?, Incident_Report = ?, Offence_ID = ?, Vehicle_ID = ?, People_ID = ? WHERE Incident_ID = ?";
    $db->update($updateQuery, [$incidentDate, $incidentReport, $offenceId, $vehicleId, $peopleId, $incidentId]);
    
    if ($_SESSION['role'] === 'admin') {
        $fineAmount = $_POST['fine_amount'] ?? '';
        $finePoints = $_POST['fine_points'] ?? '';
        
        $fineExists = $db->get_row("SELECT * FROM Fines WHERE Incident_ID = ?", [$incidentId]);
        if ($fineExists) {
            $db->update("UPDATE Fines SET Fine_Amount = ?, Fine_Points = ? WHERE Incident_ID = ?", [$fineAmount, $finePoints, $incidentId]);
        } else {
            $db->update("INSERT INTO Fines (Fine_Amount, Fine_Points, Incident_ID) VALUES (?, ?, ?)", [$fineAmount, $finePoints, $incidentId]);
        }
    }
    
    echo '<script>alert("Incident updated successfully."); window.location.href="searchIncidents.php";</script>';
    exit();
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 600px;
        margin: auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
    }
    label {
        font-weight: bold;
        margin-top: 10px;
    }
    input, select {
        width: 100%;
        padding: 8px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    input[type="submit"] {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px;
        cursor: pointer;
        margin-top: 10px;
    }
</style>

<div class="container">
    <h2>Edit Incident</h2>
    <form method="post">
        <label>Incident Date</label>
        <input type="date" name="incident_date" required value="<?= htmlspecialchars($incidentData['Incident_Date']) ?>">
        
        <label>Incident Report</label>
        <input type="text" name="incident_report" required value="<?= htmlspecialchars($incidentData['Incident_Report']) ?>">
        
        <label>Offence</label>
        <select name="offence_id">
            <?php foreach ($db->get_rows("SELECT * FROM Offence") as $offence): ?>
                <option value="<?= $offence['Offence_ID'] ?>" <?= $offence['Offence_ID'] == $incidentData['Offence_ID'] ? 'selected' : '' ?>>
                    <?= $offence['Offence_description'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Vehicle</label>
        <select name="vehicle_id">
            <?php foreach ($db->get_rows("SELECT * FROM Vehicle") as $vehicle): ?>
                <option value="<?= $vehicle['Vehicle_ID'] ?>" <?= $vehicle['Vehicle_ID'] == $incidentData['Vehicle_ID'] ? 'selected' : '' ?>>
                    <?= $vehicle['Vehicle_plate'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Person</label>
        <select name="people_id">
            <?php foreach ($db->get_rows("SELECT * FROM People") as $person): ?>
                <option value="<?= $person['People_ID'] ?>" <?= $person['People_ID'] == $incidentData['People_ID'] ? 'selected' : '' ?>>
                    <?= $person['People_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <label>Fine Amount</label>
            <input type="text" name="fine_amount" required value="<?= htmlspecialchars($incidentData['Fine_Amount']) ?>">
            
            <label>Fine Points</label>
            <input type="text" name="fine_points" required value="<?= htmlspecialchars($incidentData['Fine_Points']) ?>">
        <?php endif; ?>
        
        <input type="submit" value="Update Incident">
    </form>
</div>
