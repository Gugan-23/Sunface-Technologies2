<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch current user data including login_method
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM signup WHERE id = '$user_id'";
$result = $conn->query($query);
$user_data = $result->fetch_assoc();
// SECOND DB: Connect to the external ticket database
$ticket_host = "crossover.proxy.rlwy.net";
$ticket_port = 32488;
$ticket_user = "root";
$ticket_pass = "OHtebhVoTYDpgZgrVwjtJJnBDnAUGScb";
$ticket_db   = "railway";

// Create connection
$ticket_conn = new mysqli($ticket_host, $ticket_user, $ticket_pass, $ticket_db, $ticket_port);
if ($ticket_conn->connect_error) {
    die("❌ Ticket DB connection failed: " . $ticket_conn->connect_error);
}

// Fetch tickets using email
$tickets = [];
$user_email = $user_data['email'];

$ticket_sql = "SELECT id AS ticket_id, ticket_no, service_type, status, created_at 
               FROM tickets 
               WHERE email = ?";
$stmt = $ticket_conn->prepare($ticket_sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}

$stmt->close();
$ticket_conn->close(); // Close after fetching

// Store user_created in session if missing
if (!isset($_SESSION['user_created'])) {
    $_SESSION['user_created'] = $user_data['created'];
}

// Store login method in session if not already set
if (!isset($_SESSION['login_method'])) {
    $_SESSION['login_method'] = $user_data['login_method'];
}

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $address = $conn->real_escape_string($_POST['address']);
    $user_id = $_SESSION['user_id'];

    // Don't update email if login method is google
    // Don't update mobile if login method is otp
    $update_query = "UPDATE signup SET 
                    name = '$name', 
                    address = '$address'";
    
    // Only update mobile if login method is not otp
    if ($_SESSION['login_method'] !== 'otp') {
        $update_query .= ", mobile = '$mobile'";
    }

    // Allow email update only for OTP login
    if ($_SESSION['login_method'] === 'otp') {
        $email = $conn->real_escape_string($_POST['email']);
        $update_query .= ", email = '$email'";
    }
    
    $update_query .= " WHERE id = '$user_id'";

    if ($conn->query($update_query)) {
        // Update session variables
        $_SESSION['user_name'] = $name;
        $_SESSION['user_mobile'] = $mobile;
        $_SESSION['user_address'] = $address;
        
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background: transparent;
        }
        .profile-container {
            max-width: 100%;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #101d42 0%, #2a52be 100%);
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            font-weight: bold;
        }
        .user-detail {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        .user-detail i {
            width: 30px;
            font-size: 20px;
            color: #101d42;
        }
        .detail-content {
            flex: 1;
        }
        .detail-label {
            font-weight: 600;
            color: #101d42;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #555;
        }
        .ticket-section {
            background: #f5f7fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .ticket-section i {
            font-size: 24px;
            color: #e10000;
            margin-bottom: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            flex: 1;
            min-width: 120px;
        }
        .btn-edit {
            background: #101d42;
            color: white;
        }
        .btn-save {
            background: #28a745;
            color: white;
            display: none;
        }
        .btn-cancel {
            background: #6c757d;
            color: white;
            display: none;
        }
        .btn-logout {
            background: #ff4757;
            color: white;
            text-align: center;
        }
        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
            display: none;
        }
        .view-mode {
            display: block;
        }
        .edit-mode {
            display: none;
        }
        .edit-active .view-mode {
            display: none;
        }
        .edit-active .edit-mode {
            display: block;
        }
        .edit-active .btn-edit {
            display: none;
        }
        .edit-active .btn-save,
        .edit-active .btn-cancel {
            display: block;
        }
        /* Add this new style */
        .member-since {
            background: rgba(16, 29, 66, 0.1);
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            margin-top: 15px;
            font-size: 14px;
            color: #101d42;
        }
       #loadingOverlay {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.9);
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

#loadingOverlay video {
    width: 150px;
    height: 150px;
    object-fit: contain;
}

/* Optional: Add loading text below the video */
#loadingOverlay::after {
    content: "Logging out...";
    display: block;
    margin-top: 20px;
    font-weight: 600;
    color: #101d42;
}
    </style>
</head>
<body>
    <!-- Loading overlay -->
    <!-- Loading overlay -->
<div id="loadingOverlay">
    <video autoplay loop muted playsinline>
        <source src="images/loading2.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
    <div class="profile-container" id="profileForm">
        <div class="profile-header">
            <div class="profile-pic">
                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
            </div>
            <h2>My Profile</h2>
            <p>Logged in via <?= ucfirst($user_data['login_method']) ?></p>
            <!-- Member since element -->
            <div class="member-since">
                <i class="fas fa-calendar-alt"></i> Member since <?= date('M Y', strtotime($user_data['created'])) ?>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST" action="profile.php">
            <div class="user-detail">
                <i class="fas fa-user"></i>
                <div class="detail-content">
                    <span class="detail-label">Name</span>
                    <div class="view-mode"><?= htmlspecialchars($user_data['name']) ?></div>
                    <input type="text" name="name" class="edit-mode" value="<?= htmlspecialchars($user_data['name']) ?>" required>
                </div>
            </div>

            <div class="user-detail">
                <i class="fas fa-envelope"></i>
                <div class="detail-content">
                    <span class="detail-label">Email</span>
                    <?php if ($_SESSION['login_method'] === 'otp'): ?>
                        <div class="view-mode"><?= htmlspecialchars($user_data['email'] ?: 'Not provided') ?></div>
                        <input type="email" name="email" class="edit-mode" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                        <small>(You can add or update your email)</small>
                    <?php else: ?>
                        <div><?= htmlspecialchars($user_data['email']) ?></div>
                        <?php if ($_SESSION['login_method'] === 'google'): ?>
                            <small>(Google account email cannot be changed)</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="user-detail">
                <i class="fas fa-phone"></i>
                <div class="detail-content">
                    <span class="detail-label">Mobile</span>
                    <div class="view-mode">
                        <?= $user_data['mobile'] ? htmlspecialchars($user_data['mobile']) : 'Not provided' ?>
                    </div>
                    <input type="tel" name="mobile" class="edit-mode" 
                        value="<?= htmlspecialchars($user_data['mobile']) ?>"
                        <?= ($_SESSION['login_method'] === 'otp') ? 'readonly' : '' ?>>
                    <?php if ($_SESSION['login_method'] === 'otp'): ?>
                        <small>(Mobile cannot be changed for OTP login)</small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="user-detail">
                <i class="fas fa-map-marker-alt"></i>
                <div class="detail-content">
                    <span class="detail-label">Address</span>
                    <div class="view-mode"><?= $user_data['address'] ? nl2br(htmlspecialchars($user_data['address'])) : 'Not provided' ?></div>
                    <textarea name="address" class="edit-mode" rows="3" 
                        <?php if ($_SESSION['login_method'] === 'otp'): ?>required<?php endif; ?>
                    ><?= htmlspecialchars($user_data['address']) ?></textarea>
                    <?php if ($_SESSION['login_method'] === 'otp'): ?>
                        <small>(You can add or update your address)</small>
                    <?php endif; ?>
                </div>
            </div>

           <div class="ticket-section">
            <i class="fas fa-ticket-alt"></i>
            <h3>Your Tickets</h3>
            
        <?php if (!empty($tickets)): ?>
            <?php foreach ($tickets as $ticket): ?>
                <div class="user-detail" style="justify-content: start; text-align: left;">
                    <i class="fas fa-file-alt"></i>
                    <div class="detail-content" style="background:#fff; padding:10px; border-radius:8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <strong>Ticket No:</strong> <?= htmlspecialchars($ticket['ticket_no']) ?><br>
                        <strong>Service:</strong> <?= htmlspecialchars($ticket['service_type']) ?><br>
                        <strong>Status:</strong> <?= htmlspecialchars($ticket['status']) ?><br>
                        <strong>Created on:</strong> <?= date('d M Y, h:i A', strtotime($ticket['created_at'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tickets available</p>
        <?php endif; ?>
        </div>


            <div class="action-buttons">
                <button type="button" id="editBtn" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <button type="submit" name="update_profile" class="btn btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button type="button" id="cancelBtn" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editBtn = document.getElementById('editBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const profileForm = document.getElementById('profileForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            editBtn.addEventListener('click', function() {
                profileForm.classList.add('edit-active');
            });
            
            cancelBtn.addEventListener('click', function() {
                profileForm.classList.remove('edit-active');
            });
            
            // If there was an error, keep the form in edit mode
            <?php if (isset($error_message)): ?>
                profileForm.classList.add('edit-active');
            <?php endif; ?>
            
            // Show loading and redirect to logout.php on logout
            document.querySelector('.btn-logout').addEventListener('click', function(e) {
                e.preventDefault();
                loadingOverlay.style.display = 'flex';
                setTimeout(function() {
                    if (window.parent) {
                        window.parent.location.href = 'logout.php';
                    } else {
                        window.location.href = 'logout.php';
                    }
                }, 1200);
            });
        });
    </script>
</body>
</html>
