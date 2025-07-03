<?php
require_once 'db_connect.php'; // Use include if you prefer non-fatal error

// Get plan ID from URL
$plan_id = isset($_GET['plan_id']) ? intval($_GET['plan_id']) : 0;

// Validate plan ID
if ($plan_id <= 0) {
    die("Invalid plan ID.");
}

// Fetch plan details
$sql = "SELECT * FROM plans WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $plan_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Plan not found.");
}

$plan = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($plan['plan_name']); ?> - Package Details</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #e10000 0%, #ff512f 100%);
      min-height: 100vh;
    }

    .container {
      max-width: 480px;
      margin: 48px auto 0 auto;
      background: #fff;
      padding: 36px 28px 32px 28px;
      box-shadow: 0 8px 32px rgba(225,0,0,0.13);
      border-radius: 22px;
      position: relative;
      text-align: center;
      animation: fadeInUp 0.7s cubic-bezier(.23,1.01,.32,1) both;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(40px);}
      to { opacity: 1; transform: translateY(0);}
    }

    h1 {
      font-size: 2.1rem;
      margin-bottom: 18px;
      color: #e10000;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .plan-img {
      display: block;
      margin: 0 auto 18px auto;
      width: 110px;
      height: 110px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #e10000;
      background: #fff0f0;
      box-shadow: 0 2px 12px rgba(225,0,0,0.08);
      transition: box-shadow 0.2s, transform 0.2s;
    }

    .plan-img:hover, .plan-img:active {
      box-shadow: 0 6px 24px rgba(225,0,0,0.18);
      transform: scale(1.04);
    }

    .plan-info {
      margin-bottom: 18px;
      text-align: left;
    }

    .plan-info p {
      font-size: 1.08rem;
      margin: 12px 0 0 0;
      color: #222;
      line-height: 1.7;
    }

    .plan-info strong {
      color: #e10000;
      font-weight: 600;
      font-size: 1.05em;
    }

    .plan-info .plan-price {
      display: inline-block;
      background: #fff0f0;
      color: #e10000;
      font-size: 1.3rem;
      font-weight: 700;
      border-radius: 10px;
      padding: 7px 22px;
      margin: 0 0 12px 0;
      letter-spacing: 1px;
      box-shadow: 0 2px 8px rgba(225,0,0,0.06);
    }

    .subscribe-btn {
      display: inline-block;
      margin-top: 24px;
      padding: 14px 38px;
      background: linear-gradient(90deg, #e10000 0%, #ff512f 100%);
      color: #fff;
      text-decoration: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.1rem;
      letter-spacing: 1px;
      border: none;
      box-shadow: 0 4px 16px rgba(225,0,0,0.13);
      transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
      cursor: pointer;
      outline: none;
      position: relative;
      overflow: hidden;
    }

    .subscribe-btn:active,
    .subscribe-btn:focus,
    .subscribe-btn:hover {
      background: linear-gradient(90deg, #ff512f 0%, #e10000 100%);
      box-shadow: 0 8px 32px rgba(225,0,0,0.18);
      transform: scale(1.04);
    }

    .back-link {
      display: inline-block;
      margin-top: 22px;
      color: #e10000;
      text-decoration: none;
      font-size: 1rem;
      font-weight: 600;
      background: #fff0f0;
      border-radius: 8px;
      padding: 8px 18px;
      transition: background 0.2s, color 0.2s, transform 0.1s;
    }

    .back-link:hover,
    .back-link:focus {
      background: #e10000;
      color: #fff;
      transform: scale(1.04);
      text-decoration: none;
    }

    footer {
      background: #101d42;
      color: #fff;
      text-align: center;
      padding: 20px 0;
      font-size: 1rem;
      letter-spacing: 0.5px;
      box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
      margin-top: 60px;
      width: 100%;
    }

    @media (max-width: 600px) {
      .container {
        padding: 18px 6vw 18px 6vw;
        max-width: 98vw;
      }
      h1 {
        font-size: 1.3rem;
      }
      .plan-img {
        width: 80px;
        height: 80px;
      }
      .subscribe-btn {
        padding: 12px 18px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  
  

<div class="container">
  <img src="<?= htmlspecialchars($plan['image']) ?>" alt="Plan Image" class="plan-detail-img" />
  <h1><?php echo htmlspecialchars($plan['plan_name']); ?></h1>

  <div class="plan-info">
    <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($plan['price']); ?></p>
    <p><strong>Validity:</strong> <?php echo htmlspecialchars($plan['validity']); ?></p>
    <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($plan['description'])); ?></p>

    <?php if (!empty($plan['addons'])): ?>
      <p><strong>Add-ons:</strong><br><?php echo nl2br(htmlspecialchars($plan['addons'])); ?></p>
    <?php endif; ?>
  </div>

  <a href="subscribe.php?plan_id=<?php echo $plan['id']; ?>" class="subscribe-btn">Subscribe Now</a>
  <br>
  <a href="services.php" class="back-link">&larr; Back to Plans</a>
</div>

<footer>
  <p>&copy; 2025 Your Company. All Rights Reserved.</p>
</footer>

</body>
</html>
