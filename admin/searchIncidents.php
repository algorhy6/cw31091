<?php 
$db = require './common.php';
require 'nav.php';
$searchTerm = $_GET['search'] ?? '';
$results = [];

if ($searchTerm) {
    $sql = "select incident.Incident_ID,Vehicle_plate,people.People_name,People_licence,Incident_Report,Offence_description,Fine_Amount,Fine_Points from Incident
    left join vehicle on vehicle.Vehicle_ID = incident.Vehicle_ID = vehicle.Vehicle_ID
    left join people on people.People_ID = incident.People_ID
    left join offence on offence.Offence_ID = incident.Offence_ID
    left join fines on fines.Incident_ID = incident.Incident_ID
    where incident.Incident_ID = '" . $searchTerm . "' or incident.Vehicle_ID = '" . $searchTerm . "'";
    $results = $db->get_rows($sql);
}else{
    $sql = "select incident.Incident_ID,Vehicle_plate,people.People_name,People_licence,Incident_Report,Offence_description,Fine_Amount,Fine_Points from Incident
    left join vehicle on vehicle.Vehicle_ID = incident.Vehicle_ID = vehicle.Vehicle_ID
    left join people on people.People_ID = incident.People_ID
    left join offence on offence.Offence_ID = incident.Offence_ID
    left join fines on fines.Incident_ID = incident.Incident_ID order by incident.Incident_ID";
    $results = $db->get_rows($sql);
}
?>

    <style>
        .search-container {
            display: flex;
            justify-content: center;
            margin: 20px;
        }

        .search-container input[type="text"] {
            width: 300px;
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #9575cd;
            border-radius: 5px;
            outline: none;
        }

        .search-container input[type="text"]:focus {
            border: 1px solid #9575cd;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .search-container input[type="submit"] {
            padding: 10px 20px;
            background-color: #9575cd;
            border: none;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            outline: none;
            transition: background-color 0.3s ease;
        }

        .search-container input[type="submit"]:hover {
            background-color: #9575cd;
        }

        .results-table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .results-table th, .results-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .results-table th {
            background-color: #9575cd;
            color: #fff;
            text-align: center;
        }

        .results-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .results-table tr:hover {
            background-color: #f1f1f1;
        }

        .sm-button {
            padding: 5px 10px;
            background-color: #9575cd;
            border: none;
            border-radius: 3px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .sm-button:hover {
            background-color: #9575cd;
        }
    </style>

    <div class="search-container">
        <form action="searchIncidents.php" method="get">
            <input type="text" name="search" placeholder="Please Input incident ID"
                   value="<?= htmlspecialchars($searchTerm) ?>">
            <input type="submit" value="Search">
        </form>
    </div>

<?php if ($results): ?>
    <table class="results-table">
        <thead>
        <tr>
            <th>Incidents ID</th>
            <th>Vehicle Plate</th>
            <th>People Name</th>
            <th>People Licence</th>
            <th>Offence Description</th>
            <th>Incident Report</th>
            <th>Fine Points</th>
            <th>Fine Amount</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Incident_ID']) ?></td>
                <td><?= htmlspecialchars($row['Vehicle_plate'] ?? 'unknown') ?></td>
                <td><?= htmlspecialchars($row['People_name']) ?></td>
                <td><?= htmlspecialchars($row['People_licence']) ?></td>
                <td><?= htmlspecialchars($row['Offence_description']) ?></td>
                <td><?= htmlspecialchars($row['Incident_Report']) ?></td>
                <td><?= htmlspecialchars($row['Fine_Points'] ?? 'none') ?></td>
                <td><?= htmlspecialchars($row['Fine_Amount'] ?? 'none') ?></td>
                <td>
                    <button class="sm-button" onclick="window.location.href='./edit.php?id=<?= $row['Incident_ID'] ?>'">Edit</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($searchTerm): ?>
    <p style="text-align: center; color: #666;">No results found for "<?= htmlspecialchars($searchTerm) ?>"</p>
<?php endif; ?>
