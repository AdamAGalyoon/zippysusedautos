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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zippy Admin - Vehicle Makes</title>
    <!-- Bootstrap CSS -->
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
        <?php
        $stmt = $conn->query("SELECT * FROM makes");
        $makes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($makes) > 0) {
            echo '<ul>';
            foreach ($makes as $make) {
                echo '<li>' . $make['make_name'] . ' <a href="delete_make.php?id=' . $make['make_id'] . '">Remove</a></li>';
            }
            echo '</ul>';
        } else {
            echo 'No makes found.';
        }
        ?>
        <a href="Applications/XAMPP/htdocs/zippysusedauto/admin.php" class="btn btn-secondary mt-3">Back to Admin</a>
    </div>
</body>
</html>
