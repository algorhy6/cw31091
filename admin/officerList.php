<?php 
$db = require './common.php';
require 'nav.php';
if (isPostRequest()) {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    if (!$username || !$password) {
        echo '<script>alert("Please fill in all fields");window.location.href="officerList.php"</script>';
        exit();
    }
    if ($db->get_row("SELECT * from `officers` where Username = '$username'")) {
        echo '<script>alert("Officer already exists");window.location.href="officerList.php"</script>';
        exit();
    }
    $sql = "INSERT INTO `officers` (Username, Password) VALUES ('$username', '$password')";
    $db->update($sql);
    logger('add officer');
    echo '<script>alert("Officer added successfully");window.location.href="officerList.php"</script>';
    exit();
}

$sql = 'select * from officers';
$results = $db->get_rows($sql);
?>

<style>
    .search-container {
        margin: 20px auto;
        text-align: center;
    }

    .search-container input[type="text"],
    .search-container input[type="password"] {
        width: 300px;
        padding: 10px;
        margin-right: 10px;
        border: 1px solid #9575cd;
        border-radius: 5px;
        outline: none;
        font-size: 14px;
    }

    .search-container input[type="text"]:focus,
    .search-container input[type="password"]:focus {
        border-color: #9575cd;
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
        font-size: 14px;
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

    .results-table th,
    .results-table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: center;
    }

    .results-table th {
        background-color: #9575cd;
        color: #fff;
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

    .modal {
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-dialog {
        position: relative;
        margin: auto;
        padding: 20px;
        width: 80%;
        max-width: 500px;
    }

    .modal-content {
        background-color: #fff;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 5px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #9575cd;
        border-radius: 5px;
    }

    .btn {
        padding: 10px 20px;
        background-color: #9575cd;
        border: none;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        font-size: 14px;
    }

    .btn:hover {
        background-color: #9575cd;
    }

    .button {
        padding: 10px 20px;
        background-color: #9575cd;
        border: none;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        outline: none;
        font-size: 14px;
    }

    .button:hover {
        background-color: #9575cd;
    }
</style>

<!-- Button to trigger modal -->
<div style="padding: 20px; text-align: center;">
    Tool: <button type="button" class="button" onclick="openModal()">
        Create
    </button>
</div>

<!-- Modal -->
<div id="createOfficerModal" class="modal" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create officer</h2>
                <button type="button" class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" style="padding: 10px">
                <form action="officerList.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input style="display: block;width: 100%" type="password" class="form-control" id="password" name="password"
                               placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>

<table class="results-table">
    <thead>
    <tr>
        <th>Officer Name</th>
        <th>Officer Password</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($results as $row): ?>
        <tr style="text-align: center">
            <td><?= htmlspecialchars($row['Username']) ?></td>
            <td><?= htmlspecialchars($row['Password']) ?></td>
            <td>
                <form action="login.php" method="post">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($row['Username']) ?>">
                    <input type="hidden" name="password" value="<?= htmlspecialchars($row['Password']) ?>">
                    <input type="submit" value="Login" class="sm-button">
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<script>
    function openModal() {
        document.getElementById('createOfficerModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('createOfficerModal').style.display = 'none';
    }

    window.onclick = function (event) {
        if (event.target == document.getElementById('createOfficerModal')) {
            closeModal();
        }
    }
</script>
