<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us - Sunface Software Solutions</title>
  <link rel="stylesheet" href="css/about.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background: #fff;
    }

    .about-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 50px 20px;
    }

    h1, h2 {
      text-align: center;
      color: #e10000;
      font-weight: 700;
      letter-spacing: 1px;
      margin-bottom: 18px;
      text-shadow: 0 2px 8px rgba(225,0,0,0.06);
    }

    h1 {
      font-size: 2.3rem;
      margin-top: 0;
      margin-bottom: 36px;
    }

    .about-section {
      background: #fff;
      padding: 36px 32px;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(225,0,0,0.10);
      margin-bottom: 40px;
      transition: box-shadow 0.18s, transform 0.18s;
      position: relative;
      cursor: pointer;
    }

    .about-section:hover,
    .about-section:focus-within {
      box-shadow: 0 16px 48px rgba(225,0,0,0.18);
      transform: translateY(-4px) scale(1.025);
    }

    .about-section p {
      line-height: 1.8;
      color: #333;
      font-size: 1.08rem;
    }

    .about-section strong {
      color: #e10000;
      font-weight: 700;
      font-size: 1.08em;
    }

    .team {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
    }

    .team-member {
      background: #fff;
      padding: 28px 18px 22px 18px;
      border-radius: 16px;
      text-align: center;
      width: 260px;
      box-shadow: 0 4px 24px rgba(225,0,0,0.07);
      transition: box-shadow 0.18s, transform 0.18s;
      position: relative;
      cursor: pointer;
    }

    .team-member:hover,
    .team-member:focus-within {
      box-shadow: 0 8px 36px rgba(225,0,0,0.18);
      transform: translateY(-4px) scale(1.04);
    }

    .team-member img {
      border-radius: 50%;
      width: 120px;
      height: 120px;
      object-fit: cover;
      border: 3px solid #e10000;
      background: #fff0f0;
      box-shadow: 0 2px 12px rgba(225,0,0,0.08);
      margin-bottom: 12px;
      transition: box-shadow 0.2s, transform 0.2s;
    }

    .team-member:hover img,
    .team-member:focus-within img {
      box-shadow: 0 6px 24px rgba(225,0,0,0.18);
      transform: scale(1.04);
    }

    .team-member h4 {
      margin-top: 10px;
      color: #e10000;
      font-weight: 700;
      font-size: 1.15rem;
      letter-spacing: 0.5px;
    }

    .team-member p {
      color: #444;
      font-size: 1rem;
      margin: 8px 0 0 0;
    }

    .cta {
      text-align: center;
      margin-top: 50px;
    }

    .cta h2 {
      color: #101d42;
      margin-bottom: 18px;
    }

    .cta a {
      background: linear-gradient(90deg, #e10000 0%, #ff512f 100%);
      color: white;
      padding: 14px 32px;
      text-decoration: none;
      border-radius: 24px;
      font-weight: bold;
      font-size: 1.1rem;
      letter-spacing: 1px;
      transition: background 0.18s, box-shadow 0.18s, transform 0.12s;
      box-shadow: 0 4px 16px rgba(225,0,0,0.13);
      display: inline-block;
      border: none;
      outline: none;
    }

    .cta a:hover,
    .cta a:focus {
      background: linear-gradient(90deg, #ff512f 0%, #e10000 100%);
      color: #fff;
      transform: scale(1.04);
      box-shadow: 0 8px 32px rgba(225,0,0,0.18);
    }

    @media (max-width: 900px) {
      .about-container {
        padding: 30px 4vw;
      }
      .team {
        gap: 18px;
      }
    }

    @media (max-width: 600px) {
      .about-container {
        padding: 10px 0;
      }
      .about-section {
        padding: 18px 6vw;
      }
      .team {
        flex-direction: column;
        gap: 18px;
      }
      .team-member {
        width: 98vw;
        max-width: 340px;
        margin: 0 auto;
      }
    }
  </style>
</head>
<body>

<div class="about-container">
  <h1>About Sunface Software Solutions</h1>

  <div class="about-section">
    <h2>Who We Are</h2>
    <p>Sunface Software Solutions, based in Tirupur, Tamil Nadu, is a dynamic IT company specializing in modern web design, mobile app development, SEO, and digital marketing solutions. Founded with the vision to digitally empower businesses, we’ve been a trusted partner to startups, SMEs, and enterprises alike.</p>
  </div>

  <div class="about-section">
    <h2>Our Mission</h2>
    <p>We aim to deliver cutting-edge digital solutions that help businesses grow, scale, and succeed in an ever-evolving digital landscape. Our core mission is to provide high-quality, cost-effective IT services that align with our clients’ business goals.</p>
  </div>

  <div class="about-section">
    <h2>What We Do</h2>
    <p>
      <strong>✔ Website Design & Development</strong><br>
      <strong>✔ Android/iOS Mobile Applications</strong><br>
      <strong>✔ Digital Marketing & SEO Services</strong><br>
      <strong>✔ E-Commerce & CMS Platforms</strong><br>
      <strong>✔ Branding & UI/UX Design</strong>
    </p>
  </div>

  <div class="about-section">
    <h2>Meet Our Team</h2>
    <div class="team">
      <div class="team-member">
       
      </div>
      <div class="team-member">
        
      </div>
      <div class="team-member">
       
      </div>
    </div>
  </div>

  <div class="cta">
    <h2>Want to Work With Us?</h2>
    <a href="contact.php">Contact Us Today</a>
  </div>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

</body>
</html>
