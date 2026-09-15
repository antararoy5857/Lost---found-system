<?php
require_once '../config.php';
requireLogin();
if (!isStudent()) {
    header('Location: ../login.php');
    exit();
}

// Handle search
$search = '';
$category = '';

if (isset($_GET['search'])) {
    $search = sanitize($_GET['search']);
}

if (isset($_GET['category'])) {
    $category = sanitize($_GET['category']);
}

// Build query
$query = "SELECT * FROM found_items WHERE status = 'pending'";
if ($search) {
    $query .= " AND (item_name LIKE '%$search%' OR description LIKE '%$search%')";
}
if ($category && $category != 'all') {
    $query .= " AND category = '$category'";
}
$query .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Found Items</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Campus Lost & Found</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="report_lost.php">Report Lost Item</a></li>
                    <li class="active"><a href="browse_found.php">Browse Found Items</a></li>
                    <li><a href="claim_item.php">My Claims</a></li>
                    <li><a href="../profile.php">Profile</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Browse Found Items</h1>
            </div>

            <!-- Search and Filter -->
            <div class="search-container">
                <form method="GET" action="">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search items..." value="<?php echo $search; ?>">
                        <select name="category">
                            <option value="all">All Categories</option>
                            <option value="Electronics" <?php echo $category == 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                            <option value="Books" <?php echo $category == 'Books' ? 'selected' : ''; ?>>Books</option>
                            <option value="Clothing" <?php echo $category == 'Clothing' ? 'selected' : ''; ?>>Clothing</option>
                            <option value="Accessories" <?php echo $category == 'Accessories' ? 'selected' : ''; ?>>Accessories</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>

            <!-- Items Grid -->
            <div class="items-grid">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($item = mysqli_fetch_assoc($result)) {
                        echo '<div class="item-card">';
                        echo '<div class="item-image">';
                        if ($item['image_path']) {
                            echo '<img src="../' . $item['image_path'] . '" alt="' . $item['item_name'] . '">';
                        } else {
                            echo '<div class="no-image">No Image</div>';
                        }
                        echo '</div>';
                        echo '<div class="item-details">';
                        echo '<h3>' . $item['item_name'] . '</h3>';
                        echo '<p><strong>Category:</strong> ' . $item['category'] . '</p>';
                        echo '<p><strong>Found at:</strong> ' . $item['location'] . '</p>';
                        echo '<p><strong>Date Found:</strong> ' . $item['date_found'] . '</p>';
                        echo '<p class="description">' . substr($item['description'], 0, 100) . '...</p>';
                        echo '<a href="claim_item.php?id=' . $item['id'] . '" class="btn btn-primary">Claim This Item</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No found items available.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>