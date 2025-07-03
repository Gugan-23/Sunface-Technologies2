<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_mobile']);
?>
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
    }

</style>
<!-- Header & Sidebar -->
<div class="sidebar" id="sidebar">
  <button class="sidebar-close" onclick="toggleSidebar()">&times;</button>
  <a href="index.php">Home</a>
  <a href="services.php">Plans & Services</a>
  <a href="achievement.php">Achievements</a>
  <a href="about.php">About Us</a>
  <a href="contact.php">Contact Us</a>
  <?php if ($isLoggedIn): ?>
    <a href="raise_ticket.php">Tickets</a>
    <a href="profile.php">Profile</a>
  <?php else: ?>
    <a href="login.php">Account</a>
  <?php endif; ?>
</div>

<header>
  <div class="container header-container">
    <button class="hamburger" id="hamburger" onclick="toggleSidebar()">&#9776;</button>
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

<!-- Signup Modal -->
<div id="signupModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(16,29,66,0.25); z-index:2000; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:16px; box-shadow:0 8px 32px rgba(16,29,66,0.18); width:95vw; max-width:400px; padding:0; position:relative; display:flex; flex-direction:column; align-items:center;">
    <button onclick="closeSignupModal()" style="position:absolute; top:10px; right:18px; background:none; border:none; font-size:2rem; color:#e10000; cursor:pointer; z-index:10;">&times;</button>
    <iframe id="signupFrame" src="signup.php" style="border:none; width:100%; height:540px; border-radius:16px;"></iframe>
  </div>
</div>

<!-- Profile Modal -->
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
  // Sidebar toggle
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
  }

  // Signup modal
  document.querySelectorAll('a[href="signup.php"], .open-signup').forEach(function(link) {
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

  // Login modal
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

  // Profile modal
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
</script>
