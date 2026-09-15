<?php
require_once '../config.php';
requireLogin();
if (!isStudent()) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Count statistics
$lost_items = mysqli_query($conn, "SELECT COUNT(*) as count FROM lost_items WHERE user_id = $user_id");
$found_items = mysqli_query($conn, "SELECT COUNT(*) as count FROM found_items");
$my_claims = mysqli_query($conn, "SELECT COUNT(*) as count FROM claims WHERE user_id = $user_id");

$lost_count = mysqli_fetch_assoc($lost_items)['count'];
$found_count = mysqli_fetch_assoc($found_items)['count'];
$claims_count = mysqli_fetch_assoc($my_claims)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Campus Lost & Found</h2>
            <!-- <div class="user-info">
                <p>Welcome, <?php echo $_SESSION['user_name']; ?></p>
                <p>(Student)</p>
            </div> -->
            <nav>
                <ul>
                    <li class="active"><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="report_lost.php">Report Lost Item</a></li>
                    <li><a href="browse_found.php">Browse Found Items</a></li>
                    <li><a href="claim_item.php">My Claims</a></li>
                    <li><a href="../profile.php">Profile</a></li>
                    <li><a href="../logout.php">Logout</a></li>

                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Student Dashboard</h1>
            </div>

            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="card">
                    <h3>My Lost Items</h3>
                    <p class="count"><?php echo $lost_count; ?></p>
                </div>
                <div class="card">
                    <h3>Found Items</h3>
                    <p class="count"><?php echo $found_count; ?></p>
                </div>
                <div class="card">
                    <h3>My Claims</h3>
                    <p class="count"><?php echo $claims_count; ?></p>
                </div>
            </div>

            <!-- Recent Lost Items -->
            <div class="section">
                <h2>My Recent Lost Items</h2>
                <?php
                $recent_lost = mysqli_query($conn, 
                    "SELECT * FROM lost_items WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5");
                
                if (mysqli_num_rows($recent_lost) > 0) {
                    echo '<div class="table-responsive">';
                    echo '<table>';
                    echo '<tr><th>Item Name</th><th>Category</th><th>Date Lost</th><th>Status</th></tr>';
                    
                    while($item = mysqli_fetch_assoc($recent_lost)) {
                        echo '<tr>';
                        echo '<td>' . $item['item_name'] . '</td>';
                        echo '<td>' . $item['category'] . '</td>';
                        echo '<td>' . $item['date_lost'] . '</td>';
                        echo '<td><span class="status ' . $item['status'] . '">' . $item['status'] . '</span></td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<p>No lost items reported yet.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>