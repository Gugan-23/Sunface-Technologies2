<?php
session_start();
include 'db_connect.php';
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_mobile']);

// Fetch recommended plans for logged-in users
$recommendedPlans = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Get user's active plan category
    $activePlanQuery = "SELECT p.category 
                       FROM user_recharges ur
                       JOIN plans p ON ur.plan_id = p.id
                       WHERE ur.user_id = ? AND ur.expiry_date >= CURDATE()
                       ORDER BY ur.expiry_date DESC
                       LIMIT 1";
    $stmt = $conn->prepare($activePlanQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category = $row['category'];
        
        // Get recommended plans in same category
        $recQuery = "SELECT * FROM plans 
                    WHERE category = ?
                    ORDER BY 
                        CASE 
                            WHEN validity LIKE '%90%' THEN 1 
                            WHEN validity LIKE '%60%' THEN 2 
                            ELSE 3 
                        END,
                        price ASC
                    LIMIT 3";
        $recStmt = $conn->prepare($recQuery);
        $recStmt->bind_param("s", $category);
        $recStmt->execute();
        $recResult = $recStmt->get_result();
        
        while ($recRow = $recResult->fetch_assoc()) {
            $recommendedPlans[] = $recRow;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Reset and base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: #f5f7fa;
      color: #333;
    }

    /* Header */
    header {
      background: #101d42;
      color: white;
      position: sticky;
      top: 0;
      width: 100%;
      z-index: 999;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    .header-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 30px;
    }
    .logo {
      height: 50px;
    }
    .nav-links {
      list-style: none;
      display: flex;
      gap: 30px;
    }
    .nav-links li a {
      text-decoration: none;
      color: white;
      font-weight: 600;
      transition: color 0.3s;
    }
    .nav-links li a:hover {
      color: #00bcd4;
    }
    .blue-icon {
      color: #00bcd4;
      margin-right: 5px;
    }

    /* Sidebar Styles */
    .sidebar {
      position: fixed;
      top: 0;
      left: -250px;
      width: 220px;
      height: 100%;
      background-color: #101d42;
      padding-top: 60px;
      transition: 0.3s ease;
      z-index: 999;
    }
    .sidebar.active {
      left: 0;
    }
    .sidebar a {
      display: block;
      color: white;
      padding: 12px 20px;
      text-decoration: none;
      font-weight: 600;
    }
    .sidebar a:hover {
      background-color: #00bcd4;
      color: #101d42;
    }
    .sidebar-close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 28px;
      background: none;
      color: white;
      border: none;
      cursor: pointer;
    }

    /* Hamburger Menu */
    .hamburger {
      display: none;
      font-size: 28px;
      background: none;
      border: none;
      color: white;
      cursor: pointer;
    }

    .airtel-slider {
    max-width: 900px;
    margin: 30px auto 0;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(225, 0, 0, 0.15);
    position: relative;
    background: linear-gradient(135deg, #101d42 0%, #2c3e50 100%);
  }
  
  .slides {
    display: flex;
    transition: transform 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
    width: 100%;
  }
  
  .slide {
    width: 100%;
    flex-shrink: 0;
    flex-grow: 0;
    position: relative;
  }
  
  .slide-img {
    width: 100%;
    height: 350px;
    object-fit: cover;
    display: block;
  }
  
  .slide-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(16, 29, 66, 0.9), transparent);
    padding: 30px;
    color: white;
    text-align: left;
  }
  
  .slide-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
  }
  
  .slide-desc {
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    font-size: 1.1rem;
    max-width: 70%;
    margin-bottom: 20px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
  }
  
  .slide-button {
    background: #e10000;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 50px;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(225, 0, 0, 0.3);
  }
  
  .slide-button:hover {
    background: #ff2929;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(225, 0, 0, 0.4);
  }
  
  /* Navigation Arrows */
  .slider-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    z-index: 10;
    font-size: 1.5rem;
  }
  
  .slider-nav:hover {
    background: #e10000;
    transform: translateY(-50%) scale(1.1);
  }
  
  .slider-nav.prev {
    left: 20px;
  }
  
  .slider-nav.next {
    right: 20px;
  }
  
  .slider-progress {
    height: 6px;
    background: rgba(255, 255, 255, 0.2);
    width: 100%;
  }
  
  .progress-bar {
    height: 100%;
    width: 25%;
    background: #e10000;
    transition: transform 0.4s ease;
  }
    /* Plans Section */
    .plans {
      text-align: center;
      padding: 40px 20px;
    }
    .plans h2 {
      font-size: 2.5rem;
      margin-bottom: 30px;
    }
    .plan-buttons {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }
    .plan-card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      width: 200px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
    }
    .plan-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    .plan-card i {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #0077ff;
      display: block;
    }
    .plan-icon {
      width: 36px;
      height: 36px;
      margin-bottom: 10px;
      display: block;
      object-fit: contain;
    }

    /* NEW: Recharge-style Plan Cards */
    .current-plans-recharge .card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(225,0,0,0.10);
      max-width: 900px;
      margin: 20px auto;
      position: relative;
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .current-plans-recharge .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    .current-plans-recharge .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      background: linear-gradient(135deg, #101d42 0%, #00bcd4 100%);
      color: white;
    }

    .current-plans-recharge .card-header h2 {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 1.3rem;
    }

    .current-plans-recharge .status {
      font-size: 14px;
      font-weight: 600;
      padding: 6px 15px;
      border-radius: 50px;
      color: white;
    }

    .current-plans-recharge .status.active {
      background: #27ae60;
    }

    .current-plans-recharge .status.expired {
      background: #c0392b;
    }

    .current-plans-recharge .current-plan {
      display: flex;
      gap: 20px;
      padding: 20px;
    }

    .current-plans-recharge .plan-image {
      width: 100px;
      height: 100px;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #f5f7fa 0%, #e6e9f0 100%);
    }

    .current-plans-recharge .plan-image img {
      width: 80%;
      height: 80%;
      object-fit: contain;
    }

    .current-plans-recharge .plan-details {
      flex: 1;
    }

    .current-plans-recharge .plan-details h3 {
      color: #212529;
      font-weight: 600;
      font-size: 22px;
      margin-bottom: 10px;
    }

    .current-plans-recharge .plan-details-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-top: 15px;
    }

    .current-plans-recharge .grid-item {
      background: white;
      border-radius: 12px;
      padding: 12px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .current-plans-recharge .grid-label {
      font-weight: 500;
      color: #6c757d;
      font-size: 13px;
      margin-bottom: 5px;
    }

    .current-plans-recharge .grid-value {
      font-size: 16px;
      font-weight: 600;
      margin-top: 5px;
    }

    .current-plans-recharge .days-remaining {
      display: inline-block;
      padding: 5px 15px;
      background: #f8f9fa;
      border-radius: 50px;
      font-weight: 600;
      color: #212529;
    }

    .current-plans-recharge .days-remaining.low {
      background: #ffebee;
      color: #e74c3c;
    }

    .current-plans-recharge .days-remaining.expired {
      background: #ffcdd2;
      color: #b71c1c;
    }

    .current-plans-recharge .expired-alert,
    .current-plans-recharge .expiring-alert {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 12px 20px;
      border-radius: 30px;
      font-weight: 600;
      margin-top: 15px;
      width: fit-content;
      max-width: 100%;
    }

    .current-plans-recharge .expiring-alert {
      background: linear-gradient(135deg, #ffb347 0%, #ffcc33 100%);
      color: #7d6608;
    }

    .current-plans-recharge .expired-alert {
      background: linear-gradient(135deg, #ff6b6b 0%, #c0392b 100%);
      color: white;
    }

    /* Tabs */
    .tabs {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #e10000;
    }

    .tab-button {
      padding: 10px 20px;
      background: none;
      border: none;
      font-weight: 600;
      color: #333;
      cursor: pointer;
      font-size: 1rem;
      position: relative;
      margin: 0 5px;
    }

    .tab-button.active {
      color: #e10000;
    }

    .tab-button.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 100%;
      height: 3px;
      background: #e10000;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    /* Renew Button */
    .renew-button {
      display: inline-block;
      margin-top: 15px;
      padding: 8px 20px;
      background: #e10000;
      color: white;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s;
    }

    .renew-button:hover {
      background: #ff0000;
    }


    /* Responsive: Hamburger Menu */
    .hamburger {
      display: none;
      font-size: 28px;
      background: none;
      border: none;
      color: white;
      cursor: pointer;
    }

    .mobile-nav {
      display: none;
      flex-direction: column;
      background: #101d42;
      padding: 15px 30px;
    }

    .mobile-nav a {
      text-decoration: none;
      color: white;
      font-weight: 600;
      margin: 10px 0;
      transition: color 0.3s;
    }

    .mobile-nav a:hover {
      color: #00bcd4;
    }

    .mobile-nav.active {
      display: flex;
    }

    .notification {
      position: fixed;
      padding: 15px 20px;
      bottom: 20px;
      right: 20px;
      background: white;
      color: #212529;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      z-index: 1000;
      animation: slideIn 0.5s ease;
      max-width: 400px;
      border-left: 5px solid;
    }
    
    .notification.danger {
      border-left-color: #c0392b;
      background: linear-gradient(135deg, #ff6b6b 0%, #c0392b 100%);
      color: white;
    }
    
    .notification.warning {
      border-left-color: #ffbc51;
      background: linear-gradient(135deg, #ffb347 0%, #ffcc33 100%);
      color: #7d6608;
    }

    
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    .notification i {
      font-size: 24px;
    }

    /* Adjustments for recommended plans */
    .recommended-plans-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    
    .recommended-plan-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      text-align: center;
    }
    
    .recommended-plan-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .recommended-plan-image {
      width: 100px;
      height: 100px;
      object-fit: contain;
      margin: 0 auto 15px;
      display: block;
    }

    /*Please login css style */
    /* Airtel-style Login Prompt */
.login-prompt-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #e6e9f0 100%);
    border-radius: 20px;
    margin: 30px auto;
    max-width: 900px;
    box-shadow: 0 8px 32px rgba(225, 0, 0, 0.07);
}

.login-prompt-card {
    background: white;
    border-radius: 24px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 450px;
    position: relative;
    overflow: hidden;
}

.login-prompt-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: linear-gradient(90deg, #e10000 0%, #101d42 100%);
}

.airtel-logo-container {
    display: flex;
    justify-content: center;
    margin-bottom: 25px;
    position: relative;
    height: 80px;
}

.airtel-red-circle {
    width: 60px;
    height: 60px;
    background: #e10000;
    border-radius: 50%;
    position: absolute;
    top: 10px;
    z-index: 1;
}

.airtel-black-circle {
    width: 60px;
    height: 60px;
    background: #101d42;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: calc(50% + 10px);
    z-index: 2;
}

.login-heading {
    font-size: 28px;
    font-weight: 700;
    color: #101d42;
    margin-bottom: 15px;
    letter-spacing: -0.5px;
}

.login-message {
    color: #6c757d;
    font-size: 16px;
    margin-bottom: 30px;
    line-height: 1.6;
}

.airtel-login-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    background: linear-gradient(135deg, #e10000 0%, #b80000 100%);
    color: white;
    padding: 16px 40px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 18px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 15px rgba(225, 0, 0, 0.25);
    border: none;
    cursor: pointer;
    width: 100%;
    max-width: 280px;
    margin: 0 auto 25px;
}

.airtel-login-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(225, 0, 0, 0.35);
    background: linear-gradient(135deg, #c90000 0%, #9e0000 100%);
}

.login-footer {
    font-size: 15px;
    color: #6c757d;
    margin-top: 20px;
}

.signup-link {
    color: #e10000;
    font-weight: 600;
    text-decoration: none;
    margin-left: 8px;
    transition: color 0.2s;
}

.signup-link:hover {
    color: #101d42;
    text-decoration: underline;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.login-prompt-card {
    animation: fadeIn 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}

    @media (max-width: 768px) {
      .nav-links {
        display: none;
      }

      .hamburger {
        display: block;
      }

      .header-container {
        padding: 15px 20px;
      }
      
      .current-plans-recharge .current-plan {
        flex-direction: column;
      }
      
      .current-plans-recharge .plan-image {
        margin: 0 auto;
      }
      
      .current-plans-recharge .plan-details-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 600px) {
      .current-plans-recharge .plan-details-grid {
        grid-template-columns: 1fr;
      }
      
      .current-plans-recharge .expired-alert,
      .current-plans-recharge .expiring-alert {
        width: 100%;
        text-align: center;
        justify-content: center;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  
<div class="sidebar" id="sidebar">
  <button class="sidebar-close" onclick="toggleSidebar()">&times;</button>
  <a href="index.php">Home</a>
  <a href="services.php">Plans & Services</a>
  <a href="achievement.php">Achievements</a>
  <a href="about.php">About Us</a>
  <a href="contact.php">Contact Us</a>
  <?php if ($isLoggedIn): ?>
    <a href="raise_tickets.php">Tickets</a>
    <a href="profile.php">Profile</a>
  <?php else: ?>
    <a href="login.php">Account</a>
  <?php endif; ?>
</div>

  <!-- Header -->
  <header>
    <div class="container header-container">
      <button class="hamburger" onclick="toggleSidebar()">&#9776;</button>
      <img src="images/logo.jpeg" alt="Logo" class="logo" />
      <nav>
        <ul class="nav-links">
  <li><a href="index.php">Home</a></li>
  <li><a href="services.php">Plans & Services</a></li>
  <li><a href="achievement.php">Achievements</a></li>
  <li><a href="about.php">About Us</a></li>
  <li><a href="contact.php">Contact Us</a></li>
  <?php if ($isLoggedIn): ?>
    <li><a href="raise_ticket.php">Tickets</a></li>
    <li><a href="profile.php">Profile</a></li>
  <?php else: ?>
    <li><a href="login.php">Account</a></li>
  <?php endif; ?>
</ul>

      </nav>
    </div>
  </header>

 <!-- Banner Slider -->
  <section class="airtel-slider">
  <div class="slides" id="slides">
    <div class="slide">
      <img src="images/Screenshot 2025-06-25 123503.png" alt="Banner 1" class="slide-img" />
      <div class="slide-overlay">
        <h3 class="slide-title">Custom Android App</h3>
        <p class="slide-desc">Creating Exclusive Android</p>
        <button class="slide-button" onclick="window.location.href='mobile_app.php'">Explore Now</button>
      </div>
    </div>
    <div class="slide">
      <img src="images/banner4.jpg" alt="Banner 2" class="slide-img" />
      <div class="slide-overlay">
        <h3 class="slide-title">Dual Pack</h3>
        <p class="slide-desc">Subscribe Dual Pack for Web and App</p>
        <button class="slide-button" onclick="window.location.href='services.php'">Explore Now</button>
      </div>
    </div>
    <div class="slide">
      <img src="images/banner3.png" alt="Banner 3" class="slide-img" />
      <div class="slide-overlay">
        <h3 class="slide-title">Digital Marketing</h3>
        <p class="slide-desc">More Reaches and Connectivity!</p>
        <button class="slide-button" onclick="window.location.href='digital_marketing.php'">Explore Now</button>
      </div>
    </div>
    <div class="slide">
      <img src="images/banner1.jpg" alt="Banner 4" class="slide-img" />
      <div class="slide-overlay">
        <h3 class="slide-title">Multi Page Websites</h3>
        <p class="slide-desc">Purchase your Own Company Websites</p>
       <button class="slide-button" onclick="window.location.href='website_plans.php'">Explore Now</button>
      </div>
    </div>
  </div>
  
  <!-- Navigation Arrows -->
  <button class="slider-nav prev" id="prev">
    <i class="fas fa-chevron-left"></i>
  </button>
  <button class="slider-nav next" id="next">
    <i class="fas fa-chevron-right"></i>
  </button>
  
  <div class="slider-progress">
    <div class="progress-bar"></div>
  </div>
</section>


 <!-- Plans Section -->
  <section class="plans">

<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Database connection
$host = "crossover.proxy.rlwy.net";
$port = 32488;
$username = "root";
$password = "OHtebhVoTYDpgZgrVwjtJJnBDnAUGScb";
$database = "railway";

$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>

<h2>Plans</h2>
<div class="plan-buttons">
<?php
// Fetch data from category table
$sql = "SELECT category_name, image_url, link_url FROM category";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['category_name']);
        $img = htmlspecialchars($row['image_url']);
        $link = $row['link_url'] ? htmlspecialchars($row['link_url']) : "#";

        echo "<div class='plan-card' onclick=\"location.href='$link'\">
                <img src='$img' alt='$name' class='plan-icon' />
                <p>$name</p>
              </div>";
    }
} else {
    echo "<p>No categories found.</p>";
}

?>
</div>
</section>
<!-- Current Plans Display -->
<section class="current-plans" style="padding: 30px;">
  <?php
  if (isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];
      $query = "SELECT ur.*, p.plan_name, p.price, p.validity, p.image, p.category 
                FROM user_recharges ur 
                JOIN plans p ON ur.plan_id = p.id 
                WHERE ur.user_id = '$user_id'";
      $result = mysqli_query($conn, $query);

      if (!$result) {
          echo '<p style="color:red;">Query Failed: ' . mysqli_error($conn) . '</p>';
      } else {
          if (mysqli_num_rows($result) > 0) {
              // Separate plans into active and expired
              $activePlans = [];
              $expiredPlans = [];
              $today = date('Y-m-d');
              
              while ($row = mysqli_fetch_assoc($result)) {
                  $expiry = $row['expiry_date'];
                  $row['days_left'] = floor((strtotime($expiry) - strtotime($today)) / (60 * 60 * 24));
                  
                  if ($expiry >= $today) {
                      $activePlans[] = $row;
                  } else {
                      $expiredPlans[] = $row;
                  }
              }

              echo '<div class="tabs">';
              echo '<button class="tab-button active" data-tab="active">Active Plans</button>';
              echo '<button class="tab-button" data-tab="expired">Expired Plans</button>';
              echo '<button class="tab-button" data-tab="recommended">Recommended Plans</button>';
              echo '</div>';
              
              // Active Plans Tab
              echo '<div id="active-tab" class="tab-content active">';
              if (!empty($activePlans)) {
                  echo '<div class="current-plans-recharge">';
                  foreach ($activePlans as $plan) {
                      $daysLeft = $plan['days_left'];
                      $isExpiringSoon = ($daysLeft <= 7 && $daysLeft > 0);
                      
                      echo '<div class="card">';
                      echo '<div class="card-header">';
                      echo '<h2><i class="fas fa-crown"></i> ' . htmlspecialchars($plan['category']) . ' Plan</h2>';
                      echo '<div class="status active">Active</div>';
                      echo '</div>';
                      
                      echo '<div class="current-plan">';
                      echo '<div class="plan-image">';
                      echo '<img src="' . htmlspecialchars($plan['image']) . '" alt="' . htmlspecialchars($plan['plan_name']) . '"/>';
                      echo '</div>';
                      
                      echo '<div class="plan-details">';
                      echo '<h3>' . htmlspecialchars($plan['plan_name']) . '</h3>';
                      
                      echo '<div class="plan-details-grid">';
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Price</div>';
                      echo '<div class="grid-value">₹' . number_format($plan['price'], 2) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Validity</div>';
                      echo '<div class="grid-value">' . htmlspecialchars($plan['validity']) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Category</div>';
                      echo '<div class="grid-value">' . htmlspecialchars($plan['category']) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Recharge Date</div>';
                      echo '<div class="grid-value">' . date('M d, Y', strtotime($plan['recharge_date'])) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Expiry Date</div>';
                      echo '<div class="grid-value">' . date('M d, Y', strtotime($plan['expiry_date'])) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Days Remaining</div>';
                      echo '<div class="grid-value">';
                      echo '<span class="days-remaining' . ($daysLeft <= 7 ? ' low' : '') . '">' . $daysLeft . ' days</span>';
                      echo '</div>';
                      echo '</div>';
                      
                      echo '</div>'; // plan-details-grid
                      
                      if ($isExpiringSoon) {
                          echo '<div class="expiring-alert">';
                          echo '<i class="fas fa-exclamation-triangle"></i>';
                          echo '<strong>Your plan expires soon!</strong> Renew now to avoid service interruption.';
                          echo '</div>';
                      }
                      
                      if ($daysLeft <= 7) {
                          echo '<a href="#" class="renew-button">Renew Now</a>';
                      }
                      
                      echo '</div>'; // plan-details
                      echo '</div>'; // current-plan
                      echo '</div>'; // card
                  }
                  echo '</div>'; // current-plans-recharge
              } else {
                  echo '<p>No active plans found.</p>';
              }
              echo '</div>'; // active-tab
              
              // Expired Plans Tab
              echo '<div id="expired-tab" class="tab-content">';
              if (!empty($expiredPlans)) {
                  echo '<div class="current-plans-recharge">';
                  foreach ($expiredPlans as $plan) {
                      $daysLeft = $plan['days_left'];
                      
                      echo '<div class="card">';
                      echo '<div class="card-header">';
                      echo '<h2><i class="fas fa-crown"></i> ' . htmlspecialchars($plan['category']) . ' Plan</h2>';
                      echo '<div class="status expired">Expired</div>';
                      echo '</div>';
                      
                      echo '<div class="current-plan">';
                      echo '<div class="plan-image">';
                      echo '<img src="' . htmlspecialchars($plan['image']) . '" alt="' . htmlspecialchars($plan['plan_name']) . '"/>';
                      echo '</div>';
                      
                      echo '<div class="plan-details">';
                      echo '<h3>' . htmlspecialchars($plan['plan_name']) . '</h3>';
                      
                      echo '<div class="plan-details-grid">';
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Price</div>';
                      echo '<div class="grid-value">₹' . number_format($plan['price'], 2) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Validity</div>';
                      echo '<div class="grid-value">' . htmlspecialchars($plan['validity']) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Category</div>';
                      echo '<div class="grid-value">' . htmlspecialchars($plan['category']) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Recharge Date</div>';
                      echo '<div class="grid-value">' . date('M d, Y', strtotime($plan['recharge_date'])) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Expiry Date</div>';
                      echo '<div class="grid-value">' . date('M d, Y', strtotime($plan['expiry_date'])) . '</div>';
                      echo '</div>';
                      
                      echo '<div class="grid-item">';
                      echo '<div class="grid-label">Days Remaining</div>';
                      echo '<div class="grid-value">';
                      echo '<span class="days-remaining expired">Expired</span>';
                      echo '</div>';
                      echo '</div>';
                      
                      echo '</div>'; // plan-details-grid
                      
                      echo '<div class="expired-alert">';
                      echo '<i class="fas fa-exclamation-triangle"></i>';
                      echo '<strong>Your plan has expired!</strong> Renew now to restore services.';
                      echo '</div>';
                      
                      echo '<a href="#" class="renew-button">Renew Now</a>';
                      
                      echo '</div>'; // plan-details
                      echo '</div>'; // current-plan
                      echo '</div>'; // card
                  }
                  echo '</div>'; // current-plans-recharge
              } else {
                  echo '<p>No expired plans found.</p>';
              }
              echo '</div>'; // expired-tab
            }            

            // Recommended Plans Tab
            echo '<div id="recommended-tab" class="tab-content">';
            if (!empty($recommendedPlans)) {
                echo '<div class="recommended-plans-container">';
                foreach ($recommendedPlans as $plan) {
                    echo '<div class="recommended-plan-card">';
                    echo '<img src="' . htmlspecialchars($plan['image']) . '" alt="' . htmlspecialchars($plan['plan_name']) . '" class="recommended-plan-image">';
                    echo '<h4>' . htmlspecialchars($plan['plan_name']) . '</h4>';
                    echo '<p>Price: ₹' . number_format($plan['price'], 2) . '</p>';
                    echo '<p>Validity: ' . htmlspecialchars($plan['validity']) . '</p>';
                    echo '<p>Category: ' . htmlspecialchars($plan['category']) . '</p>';
                    echo '<button class="renew-button" style="margin-top: 15px;" onclick="location.href=\'services.php\'">View Details</button>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No recommended plans found. Browse our plans <a href="services.php">here</a>.</p>';
            }

          }

          ///please login section
        }
        else {
          echo '<div class="login-prompt-container">';
          echo '<div class="login-prompt-card">';
          echo '<div class="airtel-logo-container">';
          echo '<div class="airtel-red-circle"></div>';
          echo '<div class="airtel-black-circle"></div>';
          echo '</div>';
          echo '<h3 class="login-heading">Access Your Plans</h3>';
          echo '<p class="login-message">Sign in to view your current plans and manage your account</p>';
          echo '<a href="login.php" class="airtel-login-button">';
          echo '<i class="fas fa-lock"></i> Log In';
          echo '</a>';
          echo '<div class="login-footer">';
          echo '<span>New to Sunface Technologies?</span>';
          echo '<a href="signup.php" class="signup-link">Create Account</a>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
      }
  ?>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>
    

  <!-- Notification Container -->
  <div id="notification-container"></div>
  

  <!-- Signup Modal -->
  <div id="signupModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(16,29,66,0.25); z-index:2000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; box-shadow:0 8px 32px rgba(16,29,66,0.18); width:95vw; max-width:400px; padding:0; position:relative; display:flex; flex-direction:column; align-items:center;">
      <button onclick="closeSignupModal()" style="position:absolute; top:10px; right:18px; background:none; border:none; font-size:2rem; color:#e10000; cursor:pointer; z-index:10;">&times;</button>
      <iframe id="signupFrame" src="signup.php" style="border:none; width:100%; height:540px; border-radius:16px;"></iframe>
    </div>
  </div>

  <!-- Profile Dialog -->
<div id="profileModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(16,29,66,0.25); z-index:2000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; box-shadow:0 8px 32px rgba(16,29,66,0.18); width:95vw; max-width:500px; padding:0; position:relative; display:flex; flex-direction:column; align-items:center;">
        <button onclick="closeProfileModal()" style="position:absolute; top:10px; right:18px; background:none; border:none; font-size:2rem; color:#e10000; cursor:pointer; z-index:10;">&times;</button>
        <iframe id="profileFrame" src="" style="border:none; width:100%; height:600px; border-radius:16px;"></iframe>
    </div>
</div>

<!-- Login Modal -->
<div id="loginModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(16,29,66,0.25); z-index:2000; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:16px; box-shadow:0 8px 32px rgba(16,29,66,0.18); width:95vw; max-width:400px; padding:0; position:relative; display:flex; flex-direction:column; align-items:center;">
    <button onclick="closeLoginModal()" style="position:absolute; top:10px; right:18px; background:none; border:none; font-size:2rem; color:#e10000; cursor:pointer; z-index:10;">&times;</button>
    <iframe id="loginFrame" src="login.php" style="border:none; width:100%; height:540px; border-radius:16px;"></iframe>
  </div>
</div>

  <script>
      // Enhanced Slider Functionality
  const slides = document.getElementById('slides');
  const slideElements = document.querySelectorAll('.slide');
  const totalSlides = slideElements.length;
  const progressBar = document.querySelector('.progress-bar');
  let index = 0;
  let interval;
  
  function updateSlidePosition() {
    slides.style.transform = `translateX(-${index * 100}%)`;
    progressBar.style.transform = `translateX(${index * 100}%)`;
  }
  
  function nextSlide() {
    index = (index + 1) % totalSlides;
    updateSlidePosition();
    resetInterval();
  }
  
  function prevSlide() {
    index = (index - 1 + totalSlides) % totalSlides;
    updateSlidePosition();
    resetInterval();
  }
  
  function resetInterval() {
    clearInterval(interval);
    interval = setInterval(nextSlide, 5000);
  }
  
  document.getElementById('next').onclick = nextSlide;
  document.getElementById('prev').onclick = prevSlide;
  
  // Initialize
  updateSlidePosition();
  interval = setInterval(nextSlide, 5000);
  
  // Pause on hover
  slides.addEventListener('mouseenter', () => clearInterval(interval));
  slides.addEventListener('mouseleave', resetInterval);




      // Sidebar toggle
      function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
      }

      // Current plans tab functionality

      document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        
        tabButtons.forEach(button => {
          button.addEventListener('click', () => {
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(content => {
              content.classList.remove('active');
            });
            
            // Show selected tab content
            const tabId = button.getAttribute('data-tab') + '-tab';
            document.getElementById(tabId).classList.add('active');
          });
        });
      });

      document.querySelectorAll('.renew-button').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          alert('Redirecting to recharge page...');
          //window.location.href = 'recharge.php';
        });
      });
      
      // Add this to your main page's JavaScript
      window.addEventListener('message', function(event) {
          if (event.data.action === 'loginSuccess') {
              // Close the login modal
              closeLoginModal();
              
              // Redirect to main page
              window.location.href = event.data.redirectUrl;
          }
      });

      // Open signup modal
      document.querySelectorAll('a[href="signup.php"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          document.getElementById('signupModal').style.display = 'flex';
          document.getElementById('signupFrame').src = 'signup.php';
        });
      });

      function closeSignupModal() {
        document.getElementById('signupModal').style.display = 'none';
        document.getElementById('signupFrame').src = '';
      }

      // Open login modal
      document.querySelectorAll('a[href="login.php"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          document.getElementById('loginModal').style.display = 'flex';
          document.getElementById('loginFrame').src = 'login.php';
        });
      });

      function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
        document.getElementById('loginFrame').src = '';
      }

      // Open profile modal
      document.querySelectorAll('a[href="profile.php"]').forEach(function(link) {
          link.addEventListener('click', function(e) {
              e.preventDefault();
              document.getElementById('profileModal').style.display = 'flex';
              document.getElementById('profileFrame').src = 'profile.php';
          });
      });

      function closeProfileModal() {
          document.getElementById('profileModal').style.display = 'none';
          document.getElementById('profileFrame').src = '';
      }

      // Function to show notification
      function showNotification(type, message) {
        const container = document.getElementById('notification-container');
        const icon = type === 'danger' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
          <i class="fas ${icon}"></i>
          <span>${message}</span>
        `;
        
        container.appendChild(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
          notification.style.opacity = '0';
          setTimeout(() => {
            notification.remove();
          }, 300);
        }, 5000);
      }

      // Check for expired plans and show notification
      document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['user_id']) && !empty($expiredPlans)): ?>
          showNotification('danger', 'You have expired plans! Renew now to continue services.');
        <?php endif; ?>
        
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
          button.addEventListener('click', () => {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            document.querySelectorAll('.tab-content').forEach(content => {
              content.classList.remove('active');
            });
            
            const tabId = button.getAttribute('data-tab') + '-tab';
            document.getElementById(tabId).classList.add('active');
          });
        });
      });

  </script>
</body>
</html>