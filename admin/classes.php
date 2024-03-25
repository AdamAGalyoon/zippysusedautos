<?php
$db_host = 'localhost';
$db_name = 'zippyusedautos';
$db_user = 'root';
$db_pass = '';

$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zippy Admin - Vehicle Classes</title>
    <!-- Bootstrap CSS -->
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
        <?php
        $stmt = $conn->query("SELECT * FROM classes");
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($classes) > 0) {
            echo '<table class="table">';
            echo '<tr><th>Name</th><th>Remove</th></tr>';
            foreach ($classes as $class) {
                echo '<tr>';
                echo '<td>' . $class['class_name'] . '</td>';
                echo '<td><a href="delete_class.php?id=' . $class['class_id'] . '">Remove</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo 'No classes found.';
        }
        ?>
        <a href="applications/XAMPP/htdocs/zippysusedauto/admin.php" class="btn btn-secondary mt-3">Back to Admin</a>
    </div>
</body>
</html>
