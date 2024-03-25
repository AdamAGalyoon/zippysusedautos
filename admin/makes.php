<?php
$db_host = 'localhost';
$db_name = 'zippyusedautos';
$db_user = 'root';
$db_pass = '';

// Establish a database connection
$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle form submission to add new make
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve form data
        $makeName = $_POST['name'];

        // Insert new make into database
        $insertStmt = $conn->prepare("INSERT INTO makes (make_name) VALUES (:name)");
        $insertStmt->bindParam(':name', $makeName, PDO::PARAM_STR);
        $insertStmt->execute();

        // Check if the insertion was successful
        $rowCount = $insertStmt->rowCount();
        if ($rowCount > 0) {
            echo "Make added successfully.";
        } else {
            echo "Error: Make not added.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Display the error message
    }
}

// Check if delete request is received
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteStmt = $conn->prepare("DELETE FROM makes WHERE make_id = :id");
    $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $deleteStmt->execute();
    // Redirect back to makes page after deletion
    header("Location: makes.php");
    exit();
}

// Fetch all makes for display
$stmt = $conn->query("SELECT * FROM makes");
$makes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zippy Admin - Vehicle Makes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Zippy Admin</h1>
        <h2>Vehicle Make List</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Make</button>
        </form>
        <hr>
        <h3>View Full Vehicle Make List</h3>
        <!-- Display the list of makes from the database -->
        <?php if (count($makes) > 0): ?>
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Remove</th>
                </tr>
                <?php foreach ($makes as $make): ?>
                    <tr>
                        <td><?= $make['make_name'] ?></td>
                        <td>
                            <a href="?action=delete&id=<?= $make['make_id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')" class="btn btn-danger">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No makes found.</p>
        <?php endif; ?>
        <a href="admin.php" class="btn btn-secondary mt-3">Back to Admin</a>
    </div>
</body>
</html>
