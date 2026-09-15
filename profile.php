<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user details
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    $update_fields = "name = '$name', phone = '$phone'";
    
    // Update password if provided
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $error = "Current password is required to set new password";
        } elseif (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_fields .= ", password = '$hashed_password'";
        } else {
            $error = "Current password is incorrect";
        }
    }
    
    if (!$error) {
        $update_sql = "UPDATE users SET $update_fields WHERE id = '$user_id'";
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['user_name'] = $name;
            $success = "Profile updated successfully!";
            header("refresh:2");
        } else {
            $error = "Update failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Campus Lost & Found</h2>
            <nav>
                <ul>
                    <?php if(isStudent()): ?>
                        <li><a href="student/dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="admin/dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <!-- <li class="active"><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <ul> -->
                    <!-- <li><a href="dashboard.php">Dashboard</a></li> -->
                    <li><a href="manage_item.php">Manage Items</a></li>
                    <li><a href="manage_claims.php">Manage Claims</a></li>
                    <!-- <li><a href="reports.php"> Reports</a></li> -->
                    <li class="active" ><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <!-- </ul> -->
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>My Profile</h1>
            </div>

            <div class="form-container">
                <?php if($success): ?>
                    <div class="alert success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" value="<?php echo $user['email']; ?>" disabled>
                        <small>Email cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" id="student_id" value="<?php echo $user['student_id']; ?>" disabled>
                    </div>
                    
                    <hr>
                    
                    <h3>Change Password</h3>
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password">
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>