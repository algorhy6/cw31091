<?php 
require './common.php';
require 'nav.php';

i\n$incidentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($incidentId <= 0) {
    echo '<script>alert("Invalid ID."); window.location.href="searchIncidents.php";</script>';
    exit();
}

i\n$incidentQuery = $db->get_row(
    "SELECT i.*, f.Fine_Amount, f.Fine_Points FROM Incident i LEFT JOIN Fines f ON i.Incident_ID = f.Incident_ID WHERE i.Incident_ID = ?", 
    [$incidentId]
);

i\nif (!$incidentQuery) {
    echo '<script>alert("Incident not found."); window.location.href="searchIncidents.php";</script>';
    exit();
}

i\nif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $incidentData = [
        $_POST['incident_date'] ?? '',
        $_POST['incident_report'] ?? '',
        $_POST['offence_id'] ?? '',
        $_POST['vehicle_id'] ?? '',
        $_POST['people_id'] ?? '',
        $incidentId
    ];
    $db->update("UPDATE Incident SET Incident_Date = ?, Incident_Report = ?, Offence_ID = ?, Vehicle_ID = ?, People_ID = ? WHERE Incident_ID = ?", $incidentData);

i\n    if ($_SESSION['role'] === 'admin') {
        $fineData = [$_POST['fine_amount'] ?? '', $_POST['fine_points'] ?? '', $incidentId];
        $db->update("INSERT INTO Fines (Fine_Amount, Fine_Points, Incident_ID) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE Fine_Amount = VALUES(Fine_Amount), Fine_Points = VALUES(Fine_Points)", $fineData);
    }
    
    echo '<script>alert("Incident updated successfully."); window.location.href="searchIncidents.php";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Incident</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Incident</h2>
        <form method="post">
            <label>Date</label>
            <input type="date" name="incident_date" value="<?= htmlspecialchars($incidentQuery['Incident_Date']) ?>" required>

            <label>Report</label>
            <textarea name="incident_report" required><?= htmlspecialchars($incidentQuery['Incident_Report']) ?></textarea>

            <label>Offence</label>
            <select name="offence_id">
                <?php foreach ($db->get_rows("SELECT * FROM Offence") as $offence): ?>
                    <option value="<?= $offence['Offence_ID'] ?>" <?= $offence['Offence_ID'] == $incidentQuery['Offence_ID'] ? 'selected' : '' ?>>
                        <?= $offence['Offence_description'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Vehicle</label>
            <select name="vehicle_id">
                <?php foreach ($db->get_rows("SELECT * FROM Vehicle") as $vehicle): ?>
                    <option value="<?= $vehicle['Vehicle_ID'] ?>" <?= $vehicle['Vehicle_ID'] == $incidentQuery['Vehicle_ID'] ? 'selected' : '' ?>>
                        <?= $vehicle['Vehicle_plate'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Person</label>
            <select name="people_id">
                <?php foreach ($db->get_rows("SELECT * FROM People") as $person): ?>
                    <option value="<?= $person['People_ID'] ?>" <?= $person['People_ID'] == $incidentQuery['People_ID'] ? 'selected' : '' ?>>
                        <?= $person['People_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

i\n            <?php if ($_SESSION['role'] === 'admin'): ?>
                <label>Fine Amount</label>
                <input type="number" name="fine_amount" value="<?= htmlspecialchars($incidentQuery['Fine_Amount']) ?>" required>

                <label>Fine Points</label>
                <input type="number" name="fine_points" value="<?= htmlspecialchars($incidentQuery['Fine_Points']) ?>" required>
            <?php endif; ?>

i\n            <button type="submit">Update Incident</button>
        </form>
    </div>
</body>
</html>