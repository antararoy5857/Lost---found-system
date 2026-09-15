<?php
require_once '../config.php';
requireLogin();
if (!isStudent()) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$item = null;
$success = '';
$error = '';

// MODE 1: If we have an item ID - show item for claiming
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $item_id = sanitize($_GET['id']);
    
    // Get item details
    $item_query = "SELECT * FROM found_items WHERE id = '$item_id' AND status = 'pending'";
    $item_result = mysqli_query($conn, $item_query);

    // Check if item exists
    if (mysqli_num_rows($item_result) == 0) {
        $_SESSION['error'] = "Item not found or already claimed!";
        header('Location: browse_found.php');
        exit();
    }

    $item = mysqli_fetch_assoc($item_result);
    
    // Handle claim submission (ONLY for Mode 1)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $claim_description = sanitize($_POST['claim_description']);
        $proof_details = sanitize($_POST['proof_details']);
        
        // Check if already claimed by this user
        $check_claim = "SELECT id FROM claims WHERE found_item_id = '$item_id' AND user_id = '$user_id'";
        $existing = mysqli_query($conn, $check_claim);
        
        if (mysqli_num_rows($existing) > 0) {
            $error = "You have already claimed this item.";
        } else {
            $sql = "INSERT INTO claims (found_item_id, user_id, claim_description, proof_details) 
                    VALUES ('$item_id', '$user_id', '$claim_description', '$proof_details')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Claim submitted successfully! The admin will review your claim.";
                header("refresh:2;url=claim_item.php");
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// MODE 2: Get user's claims for display (when no item ID)
$user_claims_query = "SELECT c.*, f.item_name, f.image_path, f.status as item_status 
                      FROM claims c 
                      JOIN found_items f ON c.found_item_id = f.id 
                      WHERE c.user_id = '$user_id' 
                      ORDER BY c.claimed_at DESC";
$user_claims_result = mysqli_query($conn, $user_claims_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $item ? 'Claim Item' : 'My Claims'; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .claims-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .claim-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .claim-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .claim-image {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .claim-details p {
            margin-bottom: 10px;
        }
        .no-claims {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
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
                    <li><a href="browse_found.php">Browse Found Items</a></li>
                    <li class="active"><a href="claim_item.php">My Claims</a></li>
                    <li><a href="../profile.php">Profile</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1><?php echo $item ? 'Claim Item: ' . htmlspecialchars($item['item_name']) : 'My Claims'; ?></h1>
            </div>

            <?php if($item): ?>
                <!-- MODE 1: Show item to claim -->
                <div class="claim-container">
                    <!-- Item Details -->
                    <div class="item-preview">
                        <h3>Item Details</h3>
                        <div class="item-info">
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                     class="item-large-image">
                            <?php endif; ?>
                            <div class="item-description">
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></p>
                                <p><strong>Found Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                                <p><strong>Date Found:</strong> <?php echo htmlspecialchars($item['date_found']); ?></p>
                                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Claim Form -->
                    <div class="form-container">
                        <?php if($success): ?>
                            <div class="alert success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <?php if($error): ?>
                            <div class="alert error"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="claim_description">Why do you think this is your item? *</label>
                                <textarea id="claim_description" name="claim_description" rows="4" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="proof_details">Provide any proof or identifying details *</label>
                                <textarea id="proof_details" name="proof_details" rows="4" required></textarea>
                                <small>Describe unique features, serial numbers, or any identifying marks</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Claim</button>
                            <a href="browse_found.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- MODE 2: Show user's claims list -->
                <div class="claims-container">
                    <?php
                    if (mysqli_num_rows($user_claims_result) > 0) {
                        while($claim = mysqli_fetch_assoc($user_claims_result)) {
                            echo '<div class="claim-card">';
                            echo '<div class="claim-header">';
                            echo '<h3>' . htmlspecialchars($claim['item_name']) . '</h3>';
                            echo '<span class="status ' . $claim['status'] . '">' . $claim['status'] . '</span>';
                            echo '</div>';
                            
                            if (!empty($claim['image_path'])) {
                                echo '<img src="../' . htmlspecialchars($claim['image_path']) . '" alt="' . htmlspecialchars($claim['item_name']) . '" class="claim-image">';
                            }
                            
                            echo '<div class="claim-details">';
                            echo '<p><strong>Claim Date:</strong> ' . $claim['claimed_at'] . '</p>';
                            echo '<p><strong>Claim Description:</strong> ' . htmlspecialchars($claim['claim_description']) . '</p>';
                            echo '<p><strong>Proof Details:</strong> ' . htmlspecialchars($claim['proof_details']) . '</p>';
                            
                            if (!empty($claim['admin_remarks'])) {
                                echo '<p><strong>Admin Remarks:</strong> ' . htmlspecialchars($claim['admin_remarks']) . '</p>';
                            }
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-claims">';
                        echo '<h3>No Claims Yet</h3>';
                        echo '<p>You haven\'t made any claims yet.</p>';
                        echo '<a href="browse_found.php" class="btn btn-primary">Browse Found Items to Claim</a>';
                        echo '</div>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>