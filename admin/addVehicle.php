<?php 
$db = require './common.php';
require 'nav.php';
$owners = $db->get_rows("SELECT People_ID, People_name FROM People");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicleData = [
        'plate' => $_POST['vehicle_plate'] ?? '',
        'brand' => $_POST['vehicle_brand'] ?? '',
        'model' => $_POST['vehicle_model'] ?? '',
        'color' => $_POST['vehicle_color'] ?? ''
    ];
    $vehicleData['type'] = $vehicleData['brand'] . ' ' . $vehicleData['model'];
    
    $ownerId = $_POST['owner_id'] ?? '';
    if ($ownerId === 'new') {
        $newOwner = [
            'name' => $_POST['new_owner_name'] ?? '',
            'address' => $_POST['new_owner_address'] ?? '',
            'licence' => $_POST['new_owner_licence'] ?? ''
        ];
        
        if ($db->get_row("SELECT 1 FROM People WHERE People_licence = ?", [$newOwner['licence']])) {
            echo "<script>alert('Owner with the same license already exists');window.location.href='addVehicle.php';</script>";
            exit();
        }
        
        $db->update("INSERT INTO People (People_name, People_address, People_licence) VALUES (?, ?, ?)", array_values($newOwner));
        $ownerId = $db->last_insert_id();
    }
    
    $db->update("INSERT INTO Vehicle (Vehicle_type, Vehicle_colour, Vehicle_plate) VALUES (?, ?, ?)", [
        $vehicleData['type'], $vehicleData['color'], $vehicleData['plate']
    ]);
    $vehicleId = $db->last_insert_id();
    
    $db->update("INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES (?, ?)", [$ownerId, $vehicleId]);
    
    echo "<script>alert('Vehicle added successfully');window.location.href='addVehicle.php';</script>";
    exit();
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #eef;
        margin: 20px;
    }
    .form-container {
        max-width: 600px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    input, select {
        width: 100%;
        padding: 8px;
        margin: 5px 0;
    }
    .hidden { display: none; }
</style>

<div class="form-container">
    <form action="addVehicle.php" method="post">
        <label>Vehicle Plate</label>
        <input type="text" name="vehicle_plate" required>

        <label>Vehicle Brand</label>
        <input type="text" name="vehicle_brand" required>

        <label>Vehicle Model</label>
        <input type="text" name="vehicle_model" required>

        <label>Vehicle Color</label>
        <input type="text" name="vehicle_color" required>

        <label>Owner</label>
        <select name="owner_id" id="ownerSelect" onchange="toggleOwnerInput()">
            <option value="new">Create New Owner</option>
            <?php foreach ($owners as $owner): ?>
                <option value="<?= htmlspecialchars($owner['People_ID']) ?>">
                    <?= htmlspecialchars($owner['People_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div id="newOwnerFields" class="hidden">
            <label>New Owner Name</label>
            <input type="text" name="new_owner_name">
            
            <label>Owner Address</label>
            <input type="text" name="new_owner_address">
            
            <label>Owner License</label>
            <input type="text" name="new_owner_licence">
        </div>
        
        <button type="submit">Add Vehicle</button>
    </form>
</div>

<script>
    function toggleOwnerInput() {
        document.getElementById('newOwnerFields').classList.toggle('hidden', document.getElementById('ownerSelect').value !== 'new');
    }
</script>