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
        $make = $_POST['make'];
        $type = $_POST['type'];
        $class = $_POST['class'];
        $year = $_POST['year'];
        $model = $_POST['model'];
        $price = $_POST['price'];

        // Insert new vehicle into database
        $insertStmt = $conn->prepare("INSERT INTO vehicles (make_id, type_id, class_id, year, model, price) VALUES ((SELECT make_id FROM makes WHERE make_name = :make), (SELECT type_id FROM types WHERE type_name = :type), (SELECT class_id FROM classes WHERE class_name = :class), :year, :model, :price)");
        $insertStmt->bindParam(':make', $make, PDO::PARAM_STR);
        $insertStmt->bindParam(':type', $type, PDO::PARAM_STR);
        $insertStmt->bindParam(':class', $class, PDO::PARAM_STR);
        $insertStmt->bindParam(':year', $year, PDO::PARAM_INT);
        $insertStmt->bindParam(':model', $model, PDO::PARAM_STR);
        $insertStmt->bindParam(':price', $price, PDO::PARAM_INT);
        $insertStmt->execute();

        // Check if the insertion was successful
        $rowCount = $insertStmt->rowCount();
        if ($rowCount > 0) {
            echo "Vehicle added successfully.";
        } else {
            echo "Error: Vehicle not added.";
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
    <title>Zippy Admin - Add Vehicle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
    <h1>Zippy Admin</h1>
        <h2>Add Vehicle</h2>
        <form method="POST">
            <div class="form-group">
                <label for="make">Make:</label>
                <input type="text" id="make" name="make" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="class">Class:</label>
                <input type="text" id="class" name="class" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="model">Model:</label>
                <input type="text" id="model" name="model" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Vehicle</button>
        </form>
        <a href="admin.php" class="btn btn-secondary mt-3">Back to Admin</a>
    </div>
</body>
</html>
