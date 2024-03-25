<?php
require_once 'config.php';

// Initialize filter variables and default values
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'price';  // Default sorting by price
$filter_make = isset($_GET['make']) ? $_GET['make'] : '';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_class = isset($_GET['class']) ? $_GET['class'] : '';

// Build the SQL query to fetch vehicles with make, type, and class names
$sql = "SELECT v.year, m.make_name AS make, v.model, t.type_name AS type, c.class_name AS class, v.price
        FROM vehicles v
        JOIN makes m ON v.make_id = m.make_id
        JOIN types t ON v.type_id = t.type_id
        JOIN classes c ON v.class_id = c.class_id";

// Add filters to the SQL query based on user selection
$conditions = []; 

if (!empty($filter_make)) {
    $conditions[] = "v.make_id = :make_id";
}
if (!empty($filter_type)) {
    $conditions[] = "v.type_id = :type_id";
}
if (!empty($filter_class)) {
    $conditions[] = "v.class_id = :class_id";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Add sorting to SQL query
$sql .= " ORDER BY ";
if ($sort_by == 'year') {
    $sql .= "v.year DESC";
} else {
    $sql .= "v.price DESC";
}

// Prepare and executeSQL query
$stmt = $conn->prepare($sql);

if (!empty($filter_make)) {
    $stmt->bindParam(':make_id', $filter_make, PDO::PARAM_INT);
}
if (!empty($filter_type)) {
    $stmt->bindParam(':type_id', $filter_type, PDO::PARAM_INT);
}
if (!empty($filter_class)) {
    $stmt->bindParam(':class_id', $filter_class, PDO::PARAM_INT);
}

$stmt->execute();
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zippy Used Autos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS styles */
        body {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-group {
            margin-right: 10px;
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
    <h1>Welcome to Zippy Used Autos</h1>

    <!-- Sorting and Filtering Options -->
    <form action="" method="GET">
        <label for="sort">Sort By:</label>
        <select name="sort" id="sort">
            <option value="price" <?= ($sort_by == 'price') ? 'selected' : ''; ?>>Price (High to Low)</option>
            <option value="year" <?= ($sort_by == 'year') ? 'selected' : ''; ?>>Year (Newest to Oldest)</option>
        </select>

        <label for="make">Make:</label>
        <select name="make" id="make">
            <option value="">All Makes</option>
            <?php
            $makes_query = $conn->query("SELECT * FROM makes");
            while ($make = $makes_query->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$make['make_id']}'";
                if ($filter_make == $make['make_id']) {
                    echo " selected";
                }
                echo ">{$make['make_name']}</option>";
            }
            ?>
        </select>

        <label for="type">Type:</label>
        <select name="type" id="type">
            <option value="">All Types</option>
            <?php
            $types_query = $conn->query("SELECT * FROM types");
            while ($type = $types_query->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$type['type_id']}'";
                if ($filter_type == $type['type_id']) {
                    echo " selected";
                }
                echo ">{$type['type_name']}</option>";
            }
            ?>
        </select>

        <label for="class">Class:</label>
        <select name="class" id="class">
            <option value="">All Classes</option>
            <?php
            $classes_query = $conn->query("SELECT * FROM classes");
            while ($class = $classes_query->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$class['class_id']}'";
                if ($filter_class == $class['class_id']) {
                    echo " selected";
                }
                echo ">{$class['class_name']}</option>";
            }
            ?>
        </select>

        <button type="submit">Apply Filters</button>
    </form>

    <!-- Display Vehicles -->
    <h2>Our Inventory</h2>
    <table>
        <tr>
            <th>Year</th>
            <th>Make</th>
            <th>Model</th>
            <th>Type</th>
            <th>Class</th>
            <th>Price</th>
        </tr>
        <?php foreach ($vehicles as $vehicle) : ?>
            <tr>
                <td><?= $vehicle['year'] ?></td>
                <td><?= $vehicle['make'] ?></td>
                <td><?= $vehicle['model'] ?></td>
                <td><?= $vehicle['type'] ?></td>
                <td><?= $vehicle['class'] ?></td>
                <td>$<?= number_format($vehicle['price'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>

