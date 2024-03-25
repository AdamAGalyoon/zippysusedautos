<?php
// Database connection details
$db_host = 'localhost';
$db_name = 'zippyusedautos';
$db_user = 'root';
$db_pass = '';

// Establish a database connection
$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if delete request is received
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteStmt = $conn->prepare("DELETE FROM classes WHERE class_id = :id");
    $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $deleteStmt->execute();
    // Redirect back to classes page after deletion
    header("Location: classes.php");
    exit();
}

// Handle form submission to add new class
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve form data
        $className = $_POST['name'];

        // Insert new class into database
        $insertStmt = $conn->prepare("INSERT INTO classes (class_name) VALUES (:name)");
        $insertStmt->bindParam(':name', $className, PDO::PARAM_STR);
        $insertStmt->execute();

        // Check if the insertion was successful
        $rowCount = $insertStmt->rowCount();
        if ($rowCount > 0) {
            echo "Class added successfully.";
        } else {
            echo "Error: Class not added.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Display the error message
    }
}

// Fetch all classes for display
$stmt = $conn->query("SELECT * FROM classes");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zippy Admin - Vehicle Classes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Zippy Admin</h1>
        <h2>Vehicle Class List</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Class</button>
        </form>
        <hr>
        <h3>View Full Vehicle Class List</h3>
        <!-- Display the list of classes from the database -->
        <?php if (count($classes) > 0): ?>
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Remove</th>
                </tr>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?= $class['class_name'] ?></td>
                        <td>
                            <a href="?action=delete&id=<?= $class['class_id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')" class="btn btn-danger">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No classes found.</p>
        <?php endif; ?>
        <a href="admin.php" class="btn btn-secondary mt-3">Back to Admin</a>
    </div>
</body>
</html>
