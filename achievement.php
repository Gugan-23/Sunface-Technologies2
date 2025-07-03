<?php
session_start();
include "db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sunface Technologies – Achievements</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      background-color: #fafafa;
      color: #333;
      line-height: 1.6;
      position: relative;
      overflow-x: hidden;
    }
    
    
    /* Timeline Section */
    .timeline-section {
      padding: 80px 20px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .section-title {
      text-align: center;
      margin-bottom: 60px;
    }
    
    .section-title h2 {
      font-size: 2.5rem;
      color: var(--secondary);
      margin-bottom: 15px;
      position: relative;
      display: inline-block;
    }
    
    .section-title h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: var(--primary-gradient);
      border-radius: 2px;
    }
    
    .timeline-container {
      position: relative;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .timeline-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 4px;
      height: 100%;
      background: var(--primary-gradient);
      border-radius: 2px;
      z-index: 1;
    }
    
    .timeline-item {
      position: relative;
      width: 100%;
      margin-bottom: 60px;
      display: flex;
      justify-content: flex-end;
      padding-right: 30px;
    }
    
    .timeline-item:nth-child(even) {
      justify-content: flex-start;
      padding-right: 0;
      padding-left: 30px;
    }
    
    .achievement-card {
      background: white;
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 30px;
      width: calc(50% - 40px);
      position: relative;
      transition: var(--transition);
      z-index: 2;
    }
    
    .timeline-item:nth-child(even) .achievement-card {
      box-shadow: var(--shadow);
    }
    
    .achievement-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .achievement-card::before {
      content: '';
      position: absolute;
      top: 24px;
      right: -10px;
      width: 20px;
      height: 20px;
      background: var(--primary-gradient);
      border-radius: 50%;
      border: 4px solid white;
      z-index: 3;
    }
    
    .timeline-item:nth-child(even) .achievement-card::before {
      right: auto;
      left: -10px;
    }
    
    .year {
      display: inline-block;
      background: var(--primary-gradient);
      color: white;
      font-weight: 600;
      padding: 5px 15px;
      border-radius: 30px;
      margin-bottom: 15px;
      font-size: 0.9rem;
    }
    
    .achievement-card h3 {
      font-size: 1.4rem;
      margin-bottom: 15px;
      color: var(--secondary);
    }
    
    .achievement-card p {
      color: #555;
      margin-bottom: 0;
    }
    
    .icon-container {
      position: absolute;
      top: 25px;
      right: -60px;
      width: 50px;
      height: 50px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      z-index: 4;
    }
    
    .timeline-item:nth-child(even) .icon-container {
      right: auto;
      left: -60px;
    }
    
    .icon-container i {
      font-size: 1.5rem;
      color: var(--primary);
    }
    
    /* Responsive */
    @media (max-width: 900px) {
      .timeline-container::before {
        left: 30px;
      }
      
      .timeline-item,
      .timeline-item:nth-child(even) {
        justify-content: flex-start;
        padding-left: 70px;
        padding-right: 0;
      }
      
      .achievement-card {
        width: 100%;
      }
      
      .achievement-card::before {
        left: -10px;
        right: auto;
      }
      
      .icon-container {
        left: 5px;
        right: auto;
      }
    }
    
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
      
  
      
      .section-title h2 {
        font-size: 2rem;
      }
      
      .achievement-card {
        padding: 25px;
      }
    }
    
    @media (max-width: 480px) {
    
      .section-title h2 {
        font-size: 1.7rem;
      }
      
      .timeline-section {
        padding: 60px 15px;
      }
      
      .timeline-item,
      .timeline-item:nth-child(even) {
        padding-left: 60px;
      }
      
      .icon-container {
        width: 40px;
        height: 40px;
        top: 20px;
        left: 0;
      }
      
      .icon-container i {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>

  <?php include 'header.php'; ?>

  <!-- Timeline Section -->
  <section class="timeline-section">
    <div class="section-title">
      <h2>Our Achievements and Journey</h2>
      <p>Milestones that define our growth and success</p>
    </div>
    
    <div class="timeline-container">
      <!-- Achievement 1 -->
      <div class="timeline-item">
        <div class="achievement-card">
          <span class="year">2020</span>
          <h3>AWS Hosting Migration</h3>
          <p>Upgraded server infrastructure to AWS cloud, enhancing website uptime and performance.</p>
        </div>
        <div class="icon-container">
          <i class="fas fa-cloud"></i>
        </div>
      </div>
      
      <!-- Achievement 2 -->
      <div class="timeline-item">
        <div class="achievement-card">
          <span class="year">2021</span>
          <h3>Launch of Admission Mobile App</h3>
          <p>Developed and published an Android mobile app for educational admissions along with associated marketing campaigns.</p>
        </div>
        <div class="icon-container">
          <i class="fas fa-mobile-alt"></i>
        </div>
      </div>
      
      <!-- Achievement 3 -->
      <div class="timeline-item">
        <div class="achievement-card">
          <span class="year">2022</span>
          <h3>Physical Office & Meta Verification</h3>
          <p>Relocated to a central Tirupur office and achieved Meta business verification, reinforcing credibility.</p>
        </div>
        <div class="icon-container">
          <i class="fas fa-building"></i>
        </div>
      </div>
      
      <!-- Achievement 4 -->
      <div class="timeline-item">
        <div class="achievement-card">
          <span class="year">2023</span>
          <h3>Govt. College Training Partner</h3>
          <p>Collaborated with a Government College as a Knowledge Partner, offering internship & training programmes in web technologies.</p>
        </div>
        <div class="icon-container">
          <i class="fas fa-graduation-cap"></i>
        </div>
      </div>
      
      <!-- Achievement 5 -->
      <div class="timeline-item">
        <div class="achievement-card">
          <span class="year">2024</span>
          <h3>Google Ads Verified</h3>
          <p>Received Google Ads certification and became an official Gupshup Partner, enhancing marketing capabilities.</p>
        </div>
        <div class="icon-container">
          <i class="fab fa-google"></i>
        </div>
      </div>
      
      <!-- Achievement 6 -->
      <div class="timeline-item">
        <div class="achievement-card">
          <span class="year">Top 3</span>
          <h3>Top Web Designers in Tirupur</h3>
          <p>Ranked within the top 3 web design firms locally by ThreeBestRated.com.</p>
        </div>
        <div class="icon-container">
          <i class="fas fa-medal"></i>
        </div>
      </div>
      
      <!-- Achievement 7 -->
      <div class="timeline-item">
        <div class="achievement-card">
          <span class="year">Internship</span>
          <h3>Internship Training Program</h3>
          <p>Started Industry-ready Internship Training programmes in full-stack development and digital marketing for Tiruppur students.</p>
        </div>
        <div class="icon-container">
          <i class="fas fa-laptop-code"></i>
        </div>
      </div>
      
      <!-- Achievement 8 -->
      <div class="timeline-item">
        <div class="achievement-card">
          <span class="year">3+ Years</span>
          <h3>Years in Business</h3>
          <p>Built from a college project in 2013 to a registered GST/MSME business with over three years of experience in web and marketing.</p>
        </div>
        <div class="icon-container">
          <i class="fas fa-history"></i>
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
  </script>
</body>
</html>