<?php
require_once '../config.php';
requireLogin();
if (!isStudent()) {
    header('Location: ../login.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = sanitize($_POST['item_name']);
    $category = sanitize($_POST['category']);
    $description = sanitize($_POST['description']);
    $location = sanitize($_POST['location']);
    $date_lost = sanitize($_POST['date_lost']);
    $user_id = $_SESSION['user_id'];
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_path = uploadFile($_FILES['image'], 'lost');
        if (!$image_path) {
            $error = "Failed to upload image. Please try again.";
        }
    }
    
    if (!$error) {
        $sql = "INSERT INTO lost_items (user_id, item_name, category, description, location, date_lost, image_path) 
                VALUES ('$user_id', '$item_name', '$category', '$description', '$location', '$date_lost', '$image_path')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Lost item reported successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar (same as dashboard) -->
        <div class="sidebar">
            <h2>Campus Lost & Found</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li class="active"><a href="report_lost.php">Report Lost Item</a></li>
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
                <h1>Report Lost Item</h1>
            </div>

            <div class="form-container">
                <?php if($success): ?>
                    <div class="alert success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="item_name">Item Name *</label>
                        <input type="text" id="item_name" name="item_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="Electronics">Electronics</option>
                            <option value="Books">Books</option>
                            <option value="Clothing">Clothing</option>
                            <option value="Accessories">Accessories</option>
                            <option value="Documents">Documents</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location Where Lost *</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_lost">Date Lost *</label>
                        <input type="date" id="date_lost" name="date_lost" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Upload Image (Optional)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Report Lost Item</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>