<?php
require_once '../config.php';
requireLogin();
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Count statistics
$total_users = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$total_lost = mysqli_query($conn, "SELECT COUNT(*) as count FROM lost_items");
$total_found = mysqli_query($conn, "SELECT COUNT(*) as count FROM found_items");
$pending_claims = mysqli_query($conn, "SELECT COUNT(*) as count FROM claims WHERE status = 'pending'");

$users_count = mysqli_fetch_assoc($total_users)['count'];
$lost_count = mysqli_fetch_assoc($total_lost)['count'];
$found_count = mysqli_fetch_assoc($total_found)['count'];
$claims_count = mysqli_fetch_assoc($pending_claims)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Campus Lost & Found</h2>
            <!-- <div class="user-info">
                <p>Welcome, <?php echo $_SESSION['user_name']; ?></p>
                <p>(Administrator)</p>
            </div> -->
            <nav>
                <ul>
                    <li class="active"><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="manage_item.php">Manage Items</a></li>
                    <li><a href="manage_claims.php">Manage Claims</a></li>
                    <!-- <li><a href="reports.php">Reports</a></li> -->
                    <li><a href="../profile.php">Profile</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Admin Dashboard</h1>
            </div>

            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="card">
                    <h3>Total Users</h3>
                    <p class="count"><?php echo $users_count; ?></p>
                </div>
                <div class="card">
                    <h3>Lost Items</h3>
                    <p class="count"><?php echo $lost_count; ?></p>
                </div>
                <div class="card">
                    <h3>Found Items</h3>
                    <p class="count"><?php echo $found_count; ?></p>
                </div>
                <div class="card">
                    <h3>Pending Claims</h3>
                    <p class="count"><?php echo $claims_count; ?></p>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="section">
                <h2>Recent Claims</h2>
                <?php
                $recent_claims = mysqli_query($conn, 
                    "SELECT c.*, u.name, f.item_name 
                     FROM claims c 
                     JOIN users u ON c.user_id = u.id 
                     JOIN found_items f ON c.found_item_id = f.id 
                     ORDER BY c.claimed_at DESC LIMIT 5");
                
                if (mysqli_num_rows($recent_claims) > 0) {
                    echo '<div class="table-responsive">';
                    echo '<table>';
                    echo '<tr><th>Claimant</th><th>Item</th><th>Date</th><th>Status</th><th>Action</th></tr>';
                    
                    while($claim = mysqli_fetch_assoc($recent_claims)) {
                        echo '<tr>';
                        echo '<td>' . $claim['name'] . '</td>';
                        echo '<td>' . $claim['item_name'] . '</td>';
                        echo '<td>' . $claim['claimed_at'] . '</td>';
                        echo '<td><span class="status ' . $claim['status'] . '">' . $claim['status'] . '</span></td>';
                        echo '<td><a href="manage_claims.php?action=review&id=' . $claim['id'] . '" class="btn btn-sm">Review</a></td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<p>No claims found.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>