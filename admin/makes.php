<?php
// Database connection
$db_host = 'localhost';
$db_name = 'zippyusedautos';
$db_user = 'root';
$db_pass = '';

$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle form submission for adding a new vehicle type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_type'])) {
    try {
        $typeName = $_POST['name'];

        // Check if the type already exists
        $checkStmt = $conn->prepare("SELECT * FROM types WHERE type_name = :typeName");
        $checkStmt->bindParam(':typeName', $typeName, PDO::PARAM_STR);
        $checkStmt->execute();
        $rowCount = $checkStmt->rowCount();

        if ($rowCount > 0) {
            echo "Error: Vehicle type already exists.";
        } else {
            // Insert new vehicle type into database
            $insertStmt = $conn->prepare("INSERT INTO types (type_name) VALUES (:typeName)");
            $insertStmt->bindParam(':typeName', $typeName, PDO::PARAM_STR);
            $insertStmt->execute();
            echo "Vehicle type added successfully.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Display the error message
    }
}

// Handle form submission for removing a vehicle type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_type'])) {
    try {
        $typeId = $_POST['type_id'];

        // Check if there are any vehicles associated with this type
        $checkVehiclesStmt = $conn->prepare("SELECT * FROM vehicles WHERE type_id = :typeId");
        $checkVehiclesStmt->bindParam(':typeId', $typeId, PDO::PARAM_INT);
        $checkVehiclesStmt->execute();
        $vehicleCount = $checkVehiclesStmt->rowCount();

        if ($vehicleCount > 0) {
            echo "Error: Cannot delete type. There are associated vehicles.";
        } else {
            // Delete the selected vehicle type from the database
            $deleteStmt = $conn->prepare("DELETE FROM types WHERE type_id = :typeId");
            $deleteStmt->bindParam(':typeId', $typeId, PDO::PARAM_INT);
            $deleteStmt->execute();
            echo "Vehicle type removed successfully.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Display the error message
    }
}

// Fetch all vehicle types from the database
$typesStmt = $conn->query("SELECT * FROM types");
$types = $typesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zippy Admin - Vehicle Type List</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Zippy Admin</h1>
        <h2>Vehicle Type List</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td><?php echo $type['type_name']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="type_id" value="<?php echo $type['type_id']; ?>">
                                <button type="submit" name="remove_type" class="btn btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2>Add Vehicle Type</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <button type="submit" name="add_type" class="btn btn-primary">Add Type</button>
        </form>
        <a href="admin.php" class="btn btn-secondary mt-3">Back to Admin</a>
    </div>
</body>
</html>
