<?php
require_once '../config.php';
requireLogin();
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Handle claim actions
if (isset($_POST['action'])) {
    $claim_id = sanitize($_POST['claim_id']);
    $remarks = sanitize($_POST['remarks']);
    
    if ($_POST['action'] == 'approve') {
        $status = 'approved';
    } else {
        $status = 'rejected';
    }
    
    // Update claim status
    $update_sql = "UPDATE claims SET status = '$status', admin_remarks = '$remarks' WHERE id = '$claim_id'";
    mysqli_query($conn, $update_sql);
    
    // If approved, update found item status
    if ($status == 'approved') {
        $item_sql = "UPDATE found_items f 
                     JOIN claims c ON f.id = c.found_item_id 
                     SET f.status = 'claimed' 
                     WHERE c.id = '$claim_id'";
        mysqli_query($conn, $item_sql);
    }
}

// Get all claims
$query = "SELECT c.*, u.name as claimant, u.email, f.item_name, f.image_path 
          FROM claims c 
          JOIN users u ON c.user_id = u.id 
          JOIN found_items f ON c.found_item_id = f.id 
          ORDER BY c.claimed_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Claims</title>
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
                    <li><a href="manage_item.php">Manage Items</a></li>
                    <li class="active"><a href="manage_claims.php">Manage Claims</a></li>
                    <!-- <li><a href="reports.php"> Reports</a></li> -->
                    <li><a href="../profile.php">Profile</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Manage Claims</h1>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Claimant</th>
                            <th>Item</th>
                            <th>Claim Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($claim = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>#' . $claim['id'] . '</td>';
                            echo '<td>' . $claim['claimant'] . '<br><small>' . $claim['email'] . '</small></td>';
                            echo '<td>' . $claim['item_name'] . '</td>';
                            echo '<td>' . $claim['claimed_at'] . '</td>';
                            echo '<td><span class="status ' . $claim['status'] . '">' . $claim['status'] . '</span></td>';
                            echo '<td>';
                            echo '<button class="btn btn-sm view-details" data-id="' . $claim['id'] . '">View</button>';
                            if ($claim['status'] == 'pending') {
                                echo '<button class="btn btn-sm btn-success approve-claim" data-id="' . $claim['id'] . '">Approve</button>';
                                echo '<button class="btn btn-sm btn-danger reject-claim" data-id="' . $claim['id'] . '">Reject</button>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for claim details -->
    <div id="claimModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="claimDetails"></div>
        </div>
    </div>

    <!-- Modal for approve/reject -->
    <div id="actionModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modalTitle"></h3>
            <form method="POST" action="">
                <input type="hidden" id="claim_id" name="claim_id">
                <input type="hidden" id="action_type" name="action">
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>