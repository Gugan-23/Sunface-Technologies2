<?php
// File: footer.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sunface Technologies – Footer</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #e10000;
      --primary-light: #ff3a3a;
      --primary-dark: #b00000;
      --primary-gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      --secondary: #1a1a1a;
      --text-light: #f8f8f8;
      --text-muted: #cccccc;
      --transition: all 0.3s ease;
    }
    
    /* Footer */
    .footer {
      background: var(--secondary);
      color: var(--text-light);
      padding: 70px 20px 30px;
      position: relative;
      border-top: 4px solid var(--primary);
    }
    
    .footer::before {
      content: '';
      position: absolute;
      top: -4px;
      left: 0;
      width: 100%;
      height: 4px;
      background: var(--primary-gradient);
    }
    
    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 50px;
    }
    
    .footer-column h3 {
      font-size: 1.4rem;
      margin-bottom: 25px;
      position: relative;
      padding-bottom: 12px;
      color: var(--text-light);
    }
    
    .footer-column h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--primary-gradient);
      border-radius: 2px;
    }
    
    .footer-column p {
      color: var(--text-muted);
      margin-bottom: 25px;
      line-height: 1.7;
    }
    
    .footer-links {
      list-style: none;
      padding-left: 0;
    }
    
    .footer-links li {
      margin-bottom: 15px;
      transition: var(--transition);
    }
    
    .footer-links li:hover {
      transform: translateX(5px);
    }
    
    .footer-links a {
      color: var(--text-muted);
      text-decoration: none;
      transition: var(--transition);
      display: flex;
      align-items: center;
      font-weight: 400;
    }
    
    .footer-links a:hover {
      color: var(--primary-light);
    }
    
    .footer-links a i {
      margin-right: 12px;
      color: var(--primary);
      font-size: 0.9rem;
      transition: var(--transition);
    }
    
    .footer-links a:hover i {
      color: var(--primary-light);
      transform: scale(1.2);
    }
    
    .footer-bottom {
      max-width: 1200px;
      margin: 50px auto 0;
      padding-top: 25px;
      border-top: 1px solid rgba(255,255,255,0.08);
      text-align: center;
      font-size: 0.95rem;
      color: var(--text-muted);
    }
    
    .footer-bottom p {
      margin: 0;
    }
    
    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 25px;
    }
    
    .social-links a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 42px;
      height: 42px;
      background: rgba(255,255,255,0.08);
      border-radius: 50%;
      color: var(--text-light);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }
    
    .social-links a::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: var(--primary-gradient);
      transform: scale(0);
      transition: var(--transition);
      border-radius: 50%;
      z-index: 0;
    }
    
    .social-links a:hover::before {
      transform: scale(1);
    }
    
    .social-links a i {
      position: relative;
      z-index: 1;
      transition: var(--transition);
    }
    
    .social-links a:hover i {
      color: white;
      transform: scale(1.2);
    }
    
    @media (max-width: 768px) {
      .footer {
        padding: 50px 20px 25px;
      }
      
      .footer-content {
        gap: 30px;
        grid-template-columns: 1fr;
      }
      
      .footer-column {
        margin-bottom: 30px;
      }
      
      .footer-column:last-child {
        margin-bottom: 0;
      }
    }
  </style>
</head>
<body>
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-column">
        <h3>About Sunface</h3>
        <p>Sunface Technologies is a leading web development and digital marketing agency based in Tirupur, dedicated to delivering innovative solutions to our clients.</p>
        <div class="social-links">
          <a href="https://www.facebook.com/sunfacein?mibextid=ZbWKwL" target="_blank"><i class="fab fa-facebook-f"></i></a>
          <a href="https://x.com/sunfacein" target="_blank"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/sunfacein?igsh=bnVlaDB3dG54emJk" target="_blank"><i class="fab fa-instagram"></i></a>
          <a href="https://www.linkedin.com/company/sunfacein/posts/?feedView=all" target="_blank"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      
      <div class="footer-column">
        <h3>Quick Links</h3>
        <ul class="footer-links">
          <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
          <li><a href="services.php"><i class="fas fa-chevron-right"></i> Services</a></li>
          <li><a href="achievement.php"><i class="fas fa-chevron-right"></i> Achievements</a></li>
          <li><a href="contact.php"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
        </ul>
      </div>
      
      <div class="footer-column">
        <h3>Contact Info</h3>
        <ul class="footer-links">
          <li><a href="https://maps.app.goo.gl/example" target="_blank"><i class="fas fa-map-marker-alt"></i> 16/31, S&S Enclave, Kannapiran Colony, Tirupur - 641601</a></li>
          <li><a href="tel:+919894748800"><i class="fas fa-phone"></i> +91 98947 48800</a></li>
          <li><a href="mailto:info@sunface.in"><i class="fas fa-envelope"></i> info@sunface.in</a></li>
          <li><a href="#"><i class="fas fa-clock"></i> Mon-Sat: 9:00 AM - 6:00 PM</a></li>
        </ul>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> Sunface Technologies. All Rights Reserved.</p>
    </div>
  </footer>
</body>
</html>