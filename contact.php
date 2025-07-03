<?php
// Database connection setup (db_connect.php should contain your connection details)
include "db_connect.php";

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $conn->real_escape_string(trim($_POST['name']));
    $mobile   = $conn->real_escape_string(trim($_POST['mobile']));
    $location = $conn->real_escape_string(trim($_POST['location']));
    $email    = $conn->real_escape_string(trim($_POST['email']));
    $message  = $conn->real_escape_string(trim($_POST['message']));

    // Validate mobile number
    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
        $error = "Invalid mobile number format. Please enter a 10-digit Indian number.";
    } else {
        // Check for duplicate email if provided
        if (!empty($email)) {
            $checkDuplicate = "SELECT id FROM contact WHERE email='$email' LIMIT 1";
            $res = $conn->query($checkDuplicate);

            if ($res->num_rows > 0) {
                $error = "This email already exists. Please use another.";
            } else {
                $sql = "INSERT INTO contact (name, mobile, location, email, message)
                        VALUES ('$name', '$mobile', '$location', '$email', '$message')";
            }
        } else {
            $sql = "INSERT INTO contact (name, mobile, location, message)
                    VALUES ('$name', '$mobile', '$location', '$message')";
        }

        if (isset($sql) && $conn->query($sql) === TRUE) {
            $success = "Thank you for contacting us! We'll get in touch soon.";
            // Clear form fields on success
            $name = $mobile = $location = $email = $message = '';
        } else {
            $error = "Database error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Sunface Technologies</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --primary: #e10000;
      --primary-gradient: linear-gradient(135deg, #e10000 0%, #ff512f 100%);
      --secondary: #101d42;
      --light: #f8f9fa;
      --dark: #212529;
      --shadow: 0 8px 30px rgba(0,0,0,0.12);
      --transition: all 0.3s ease;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: #fafafa;
      color: #333;
      line-height: 1.6;
      position: relative;
      overflow-x: hidden;
    }
    
    /* Contact Section */
    .contact-section {
      padding: 80px 20px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .contact-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 40px;
      margin-bottom: 60px;
    }
    
    .contact-info {
      background: white;
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 40px;
    }
    
    .contact-info h2 {
      font-size: 2rem;
      color: var(--secondary);
      margin-bottom: 30px;
      position: relative;
    }
    
    .contact-info h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--primary-gradient);
      border-radius: 2px;
    }
    
    .contact-methods {
      display: grid;
      grid-template-columns: 1fr;
      gap: 25px;
    }
    
    .contact-method {
      display: flex;
      align-items: flex-start;
      gap: 20px;
    }
    
    .contact-icon {
      width: 60px;
      height: 60px;
      background: rgba(225, 0, 0, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    
    .contact-icon i {
      font-size: 1.5rem;
      color: var(--primary);
    }
    
    .contact-details h3 {
      font-size: 1.2rem;
      margin-bottom: 8px;
      color: var(--secondary);
    }
    
    .contact-details p, 
    .contact-details a {
      color: #555;
      text-decoration: none;
      display: block;
      margin-bottom: 5px;
      transition: var(--transition);
    }
    
    .contact-details a:hover {
      color: var(--primary);
    }
    
    .contact-form {
      background: white;
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 40px;
    }
    
    .contact-form h2 {
      font-size: 2rem;
      color: var(--secondary);
      margin-bottom: 30px;
      position: relative;
    }
    
    .contact-form h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--primary-gradient);
      border-radius: 2px;
    }
    
    .form-group {
      margin-bottom: 25px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #555;
    }
    
    .form-control {
      width: 100%;
      padding: 14px 18px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      font-size: 1rem;
      transition: var(--transition);
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(225, 0, 0, 0.1);
    }
    
    textarea.form-control {
      min-height: 150px;
      resize: vertical;
    }
    
    .submit-btn {
      background: var(--primary-gradient);
      color: white;
      border: none;
      padding: 15px 30px;
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      width: 100%;
      display: block;
    }
    
    .submit-btn:hover {
      opacity: 0.9;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(225, 0, 0, 0.2);
    }
    
    /* Map Section */
    .map-section {
      margin-bottom: 60px;
    }
    
    .map-section h2 {
      text-align: center;
      font-size: 2rem;
      color: var(--secondary);
      margin-bottom: 30px;
    }
    
    .map-container {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: var(--shadow);
      height: 450px;
    }
    
    .map-container iframe {
      width: 100%;
      height: 100%;
      border: none;
    }
    
    /* Chat Options */
    .chat-section {
      margin-bottom: 60px;
    }
    
    .chat-section h2 {
      text-align: center;
      font-size: 2rem;
      color: var(--secondary);
      margin-bottom: 30px;
    }
    
    .chat-options {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 25px;
    }
    
    .chat-box {
      background: white;
      padding: 35px 30px;
      border-radius: 16px;
      box-shadow: var(--shadow);
      text-align: center;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      z-index: 1;
    }
    
    .chat-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: var(--primary-gradient);
      z-index: 2;
    }
    
    .chat-box:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    .chat-box i {
      font-size: 3rem;
      color: var(--primary);
      margin-bottom: 20px;
      transition: var(--transition);
    }
    
    .chat-box h3 {
      font-size: 1.4rem;
      color: var(--secondary);
      margin-bottom: 15px;
    }
    
    .chat-box p {
      color: #666;
      margin-bottom: 20px;
    }
    
    .chat-btn {
      display: inline-block;
      background: var(--primary);
      color: white;
      padding: 12px 25px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      transition: var(--transition);
    }
    
    .chat-btn:hover {
      background: #ff512f;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(225, 0, 0, 0.2);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      
      .nav-menu {
        position: fixed;
        top: 70px;
        left: -100%;
        flex-direction: column;
        background: white;
        width: 100%;
        height: calc(100vh - 70px);
        transition: var(--transition);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        padding: 40px 0;
        z-index: 999;
      }
      
      .nav-menu.active {
        left: 0;
      }
      
      .nav-menu li {
        margin: 15px 30px;
      }
      
      .nav-menu a {
        font-size: 1.2rem;
      }
      
      .hamburger {
        display: block;
      }
      
      .contact-container {
        grid-template-columns: 1fr;
      }
      
      .contact-info, 
      .contact-form {
        padding: 30px;
      }
      
      .map-container {
        height: 350px;
      }
    }
    
    @media (max-width: 480px) {
  
      
      .contact-method {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .contact-icon {
        margin-bottom: 15px;
      }
      
      .chat-options {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  
  <!-- Header -->
  <?php include 'header.php'; ?>

  <!-- Contact Section -->
  <section class="contact-section">
    <div class="contact-container">
      <div class="contact-info">
        <h2>Contact Information</h2>
        <div class="contact-methods">
          <div class="contact-method">
            <div class="contact-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="contact-details">
              <h3>Address</h3>
              <p>16/31, S&S Enclave, Kannapiran Colony, Valipalayam</p>
              <p>Tirupur – 641601</p>
            </div>
          </div>
          
          <div class="contact-method">
            <div class="contact-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="contact-details">
              <h3>Email</h3>
              <a href="mailto:info@sunface.in">info@sunface.in</a>
            </div>
          </div>
          
          <div class="contact-method">
            <div class="contact-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div class="contact-details">
              <h3>Phone</h3>
              <a href="tel:+919894748800">+91 98947 48800</a>
            </div>
          </div>
          
          <div class="contact-method">
            <div class="contact-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="contact-details">
              <h3>Working Hours</h3>
              <p>Monday - Saturday: 9:00 AM - 6:00 PM</p>
              <p>Sunday: Closed</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="contact-form">
        <h2>Send Us a Message</h2>
        <form method="POST" action="">
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" required>
          </div>
          
          <div class="form-group">
            <label for="mobile">Mobile Number *</label>
            <input type="tel" id="mobile" name="mobile" class="form-control" placeholder="10-digit Mobile Number" maxlength="10" required>
          </div>
          
          <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" class="form-control" placeholder="Your City" required>
          </div>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Your Email (optional)">
          </div>
          
          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" class="form-control" placeholder="Your message here..." required></textarea>
          </div>
          
          <button type="submit" class="submit-btn">Send Message</button>
        </form>
      </div>
    </div>
    
    <!-- Map Section -->
    <div class="map-section">
      <h2>Our Location</h2>
      <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3914.811996186839!2d77.3463215141772!3d11.115110692080257!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba90fd80f34fc11%3A0x81fc0015292bb820!2sSunface%20Software%20Solutions!5e0!3m2!1sen!2sin!4v1687320000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
    
    <!-- Chat Options -->
    <div class="chat-section">
      <h2>Connect With Us</h2>
      <div class="chat-options">
        <div class="chat-box">
          <i class="fab fa-whatsapp"></i>
          <h3>WhatsApp Chat</h3>
          <p>Chat with us instantly on WhatsApp</p>
          <a href="https://wa.me/919894748800" target="_blank" class="chat-btn">Chat Now</a>
        </div>
        
        <div class="chat-box">
          <i class="fas fa-comments"></i>
          <h3>Live Chat</h3>
          <p>Chat with our support team in real-time</p>
          <a href="#" class="chat-btn">Start Chat</a>
        </div>
        
        <div class="chat-box">
          <i class="fab fa-facebook-messenger"></i>
          <h3>Facebook Chat</h3>
          <p>Message us on Facebook Messenger</p>
          <a href="https://m.me/sunfacesoftwaresolutions" target="_blank" class="chat-btn">Message Us</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>  

  <script>
    // Hamburger Menu
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    hamburger.addEventListener('click', () => {
      navMenu.classList.toggle('active');
      hamburger.innerHTML = navMenu.classList.contains('active') ? 
        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });
    
    // Close menu when clicking on a link
    document.querySelectorAll('.nav-menu a').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        hamburger.innerHTML = '<i class="fas fa-bars"></i>';
      });
    });
    
    // Form validation
    const mobileInput = document.getElementById('mobile');
    if (mobileInput) {
      mobileInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
      });
    }
    
    // Show success/error messages
    <?php if ($success): ?>
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?= $success ?>',
        confirmButtonColor: '#e10000',
      });
    <?php elseif ($error): ?>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= $error ?>',
        confirmButtonColor: '#e10000',
      });
    <?php endif; ?>
  </script>
</body>
</html>