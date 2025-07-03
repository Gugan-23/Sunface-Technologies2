<?php
include 'db_connect.php';

$sql = "SELECT * FROM plans ORDER BY category, price";
$result = $conn->query($sql);

$plans_by_category = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $plans_by_category[$row['category']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Plans & Services</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/services.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <?php include 'header.php'; ?>

  <section class="plans-section">
    <h2>Plans & Services</h2>
    <?php foreach ($plans_by_category as $category => $plans): ?>
      <div class="category-block">
        <h3 class="category-title"><?= htmlspecialchars($category) ?></h3>
        <div class="plans-grid">
          <?php foreach ($plans as $plan): ?>
            <div class="plan-card touch-effect">
              <img src="<?= htmlspecialchars($plan['image']) ?>" alt="<?= htmlspecialchars($plan['plan_name']) ?>" class="plan-img"/>
              <div class="plan-info">
                <div class="plan-name"><?= htmlspecialchars($plan['plan_name']) ?></div>
                <div class="plan-validity">Validity: <?= htmlspecialchars($plan['validity']) ?></div>
                <div class="plan-price-container">
                  <span class="plan-price">₹<?= htmlspecialchars($plan['price']) ?></span>
                </div>
              </div>
              <div class="plan-actions">
                <a href="package_details.php?plan_id=<?= $plan['id'] ?>" class="pack-details-btn touch-effect">
  <span class="pack-icon">!</span> Pack details
</a>
                <button class="subscribe-btn touch-effect">Subscribe</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <?php include 'footer.php'; ?>

  <script>
    // Touch effect for plan cards
    document.querySelectorAll('.touch-effect').forEach(card => {
      card.addEventListener('touchstart', () => card.classList.add('active'));
      card.addEventListener('touchend', () => card.classList.remove('active'));
      card.addEventListener('mousedown', () => card.classList.add('active'));
      card.addEventListener('mouseup', () => card.classList.remove('active'));
      card.addEventListener('mouseleave', () => card.classList.remove('active'));
    });
  </script>
</body>
</html>