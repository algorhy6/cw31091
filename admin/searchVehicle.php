<?php
require './common.php';
require 'nav.php';

$searchTerm = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

totalSql = "SELECT COUNT(*) as total FROM Vehicle";
if ($searchTerm) {
    $totalSql .= " WHERE Vehicle_plate LIKE '%$searchTerm%'";
}
$totalRecords = $db->get_row($totalSql)['total'];
$totalPages = ceil($totalRecords / $limit);

$sql = "SELECT Vehicle.Vehicle_plate, Vehicle.Vehicle_ID, Vehicle.Vehicle_type, Vehicle.Vehicle_colour, People.People_name, People.People_licence
        FROM Vehicle
        LEFT JOIN Ownership ON Vehicle.Vehicle_ID = Ownership.Vehicle_ID
        LEFT JOIN People ON Ownership.People_ID = People.People_ID";

if ($searchTerm) {
    $sql .= " WHERE Vehicle_plate LIKE '%$searchTerm%'";
}
$sql .= " ORDER BY Vehicle.Vehicle_ID DESC LIMIT $limit OFFSET $offset";

$results = $db->get_rows($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Vehicles</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="search-container">
        <form action="searchVehicle.php" method="get">
            <input type="text" name="search" placeholder="Please input vehicle plate" value="<?= htmlspecialchars($searchTerm) ?>">
            <input type="submit" value="Search">
        </form>
    </div>

    <?php if ($results): ?>
        <table class="results-table">
            <thead>
            <tr>
                <th>Vehicle ID</th>
                <th>Car-owner Name / Licence</th>
                <th>Vehicle Plate</th>
                <th>Vehicle Color</th>
                <th>Vehicle Type</th>
                <th>Incidences</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Vehicle_ID']) ?></td>
                    <td><?= htmlspecialchars($row['People_name'] ?? 'Unknown') ?> / <?= htmlspecialchars($row['People_licence'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($row['Vehicle_plate']) ?></td>
                    <td><?= htmlspecialchars($row['Vehicle_colour']) ?></td>
                    <td><?= htmlspecialchars($row['Vehicle_type']) ?></td>
                    <td><a href="./searchIncidents.php?search=<?= htmlspecialchars($row['Vehicle_ID']) ?>">Details</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>">&lt;</a>
            <?php endif; ?>

            <?php if ($page > 3): ?>
                <a href="?page=1&search=<?= htmlspecialchars($searchTerm) ?>">1</a>
                <span>...</span>
            <?php endif; ?>

            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($searchTerm) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>">&gt;</a>
            <?php endif; ?>
        </div>
    <?php elseif ($searchTerm): ?>
        <p style="text-align: center; font-size: 16px; color: #666;">No results found for "<?= htmlspecialchars($searchTerm) ?>"</p>
    <?php endif; ?>
</body>
</html>
