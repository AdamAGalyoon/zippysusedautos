<?php

require_once 'config.php';

// Delete vehicle if delete request is received
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteStmt = $conn->prepare("DELETE FROM vehicles WHERE vehicle_id = :id");
    $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $deleteStmt->execute();
    header("Location: admin.php");
    exit();
}

// Fetch vehicles for display
$vehiclesStmt = $conn->query("SELECT v.vehicle_id, v.year, m.make_name AS make, v.model, t.type_name AS type, c.class_name AS class, v.price FROM vehicles v JOIN makes m ON v.make_id = m.make_id JOIN types t ON v.type_id = t.type_id JOIN classes c ON v.class_id = c.class_id");
$vehicles = $vehiclesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zippy Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Zippy Admin</h1>

    <!-- All Makes, Types, Classes Links -->
    <div>
        <a href="admin/makes.php" class="btn btn-primary">View/Edit Vehicle Makes</a>
        <a href="admin/types.php" class="btn btn-primary">View/Edit Vehicle Types</a>
        <a href="admin/classes.php" class="btn btn-primary">View/Edit Vehicle Classes</a>
    </div>

    <!-- List of Vehicles with Delete Option -->
    <h2>List of Vehicles</h2>
    <table>
        <tr>
            <th>Year</th>
            <th>Make</th>
            <th>Model</th>
            <th>Type</th>
            <th>Class</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach ($vehicles as $vehicle) : ?>
            <tr>
                <td><?= $vehicle['year'] ?></td>
                <td><?= $vehicle['make'] ?></td>
                <td><?= $vehicle['model'] ?></td>
                <td><?= $vehicle['type'] ?></td>
                <td><?= $vehicle['class'] ?></td>
                <td>$<?= number_format($vehicle['price'], 2) ?></td>
                <td><a href="?action=delete&id=<?= $vehicle['vehicle_id'] ?>" class="btn btn-danger">Remove</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Link to Add New Vehicle -->
    <a href="admin/add_vehicle.php" class="btn btn-success mt-3">Click here to add a vehicle</a>
    
</body>
</html>