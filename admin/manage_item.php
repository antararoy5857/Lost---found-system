<?php
require_once '../config.php';
requireLogin();
if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = sanitize($_GET['id']);
    $type = sanitize($_GET['type']);
    
    if ($_GET['action'] == 'delete') {
        if ($type == 'lost') {
            $sql = "DELETE FROM lost_items WHERE id = '$id'";
        } else {
            $sql = "DELETE FROM found_items WHERE id = '$id'";
        }
        mysqli_query($conn, $sql);
    } elseif ($_GET['action'] == 'update_status') {
        $status = sanitize($_GET['status']);
        if ($type == 'lost') {
            $sql = "UPDATE lost_items SET status = '$status' WHERE id = '$id'";
        } else {
            $sql = "UPDATE found_items SET status = '$status' WHERE id = '$id'";
        }
        mysqli_query($conn, $sql);
    }
}

// Get all items
$lost_items = mysqli_query($conn, "SELECT l.*, u.name FROM lost_items l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC");
$found_items = mysqli_query($conn, "SELECT f.*, u.name FROM found_items f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- <style><?php include 'style.css'; ?></style> -->
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Campus Lost & Found</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li class="active"><a href="manage_item.php">Manage Items</a></li>
                    <li><a href="manage_claims.php">Manage Claims</a></li>
                    <!-- <li><a href="admin_reports.php">Reports</a></li> -->
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <h1>Manage Items</h1>
            </div>

            <!-- Lost Items -->
            <div class="section">
                <h2>Lost Items</h2>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th>Item Name</th>
                            <th>Reported By</th>
                            <th>Category</th>
                            <th>Date Lost</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        <?php
                        while($item = mysqli_fetch_assoc($lost_items)) {
                            echo '<tr>';
                            echo '<td>' . $item['item_name'] . '</td>';
                            echo '<td>' . $item['name'] . '</td>';
                            echo '<td>' . $item['category'] . '</td>';
                            echo '<td>' . $item['date_lost'] . '</td>';
                            echo '<td><span class="status ' . $item['status'] . '">' . $item['status'] . '</span></td>';
                            echo '<td>';
                            echo '<select onchange="updateStatus(this.value, ' . $item['id'] . ', \'lost\')">';
                            echo '<option value="pending"' . ($item['status'] == 'pending' ? ' selected' : '') . '>Pending</option>';
                            echo '<option value="found"' . ($item['status'] == 'found' ? ' selected' : '') . '>Found</option>';
                            echo '<option value="closed"' . ($item['status'] == 'closed' ? ' selected' : '') . '>Closed</option>';
                            echo '</select>';
                            echo ' <a href="?action=delete&id=' . $item['id'] . '&type=lost" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </div>

            <!-- Found Items -->
            <div class="section">
                <h2>Found Items</h2>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th>Item Name</th>
                            <th>Found By</th>
                            <th>Category</th>
                            <th>Date Found</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        <?php
                        while($item = mysqli_fetch_assoc($found_items)) {
                            echo '<tr>';
                            echo '<td>' . $item['item_name'] . '</td>';
                            echo '<td>' . $item['name'] . '</td>';
                            echo '<td>' . $item['category'] . '</td>';
                            echo '<td>' . $item['date_found'] . '</td>';
                            echo '<td><span class="status ' . $item['status'] . '">' . $item['status'] . '</span></td>';
                            echo '<td>';
                            echo '<select onchange="updateStatus(this.value, ' . $item['id'] . ', \'found\')">';
                            echo '<option value="pending"' . ($item['status'] == 'pending' ? ' selected' : '') . '>Pending</option>';
                            echo '<option value="claimed"' . ($item['status'] == 'claimed' ? ' selected' : '') . '>Claimed</option>';
                            echo '<option value="returned"' . ($item['status'] == 'returned' ? ' selected' : '') . '>Returned</option>';
                            echo '</select>';
                            echo ' <a href="?action=delete&id=' . $item['id'] . '&type=found" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updateStatus(status, id, type) {
        window.location.href = `?action=update_status&id=${id}&status=${status}&type=${type}`;
    }
    </script>
</body>
</html>