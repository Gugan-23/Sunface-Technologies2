<?php
include "db_connect.php";
$signupSuccess = isset($_GET['success']) && $_GET['success'] == 1;
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $conn->real_escape_string(htmlspecialchars(trim($_POST["name"])));
    $email    = $conn->real_escape_string(htmlspecialchars(trim($_POST["email"])));
    $mobile   = $conn->real_escape_string(htmlspecialchars(trim($_POST["mobile"])));
    $address  = $conn->real_escape_string(htmlspecialchars(trim($_POST["address"])));
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check if email or mobile already exists
    $checkUser = "SELECT * FROM signup WHERE email='$email' OR mobile='$mobile'";
    $result = $conn->query($checkUser);
    if ($result && $result->num_rows > 0) {
        $errorMessage = "User already exists. Please use an alternative email or phone number.";
    } else {
        $sql = "INSERT INTO signup (name, email, password, mobile, address)
                VALUES ('$name', '$email', '$password', '$mobile', '$address')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
    alert('✅ Registered Successfully! Redirecting to login...');
    window.location.href = 'login.php';
</script>";
exit;

            exit;
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Sunface</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background: linear-gradient(135deg, #e10000 0%, #ff512f 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    .form-container {
      background: #fff;
      border-radius: 22px;
      padding: 36px 28px 28px 28px;
      width: 100%;
      max-width: 370px;
      box-shadow: 0 8px 32px rgba(225,0,0,0.13);
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      animation: fadeInUp 0.7s cubic-bezier(.23,1.01,.32,1) both;
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(40px);}
      to { opacity: 1; transform: translateY(0);}
    }
    .form-container h2 {
      text-align: center;
      color: #e10000;
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 20px;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .form-container h2::before {
      content: "📝";
      font-size: 1.3em;
      margin-right: 6px;
      opacity: 0.85;
    }
    .form-container input,
    .form-container textarea {
      width: 100%;
      padding: 13px 16px 13px 42px;
      margin-bottom: 16px;
      border: none;
      border-radius: 14px;
      background-color: #fff0f0;
      font-size: 1rem;
      color: #222;
      outline: none;
      box-shadow: 0 2px 8px rgba(225,0,0,0.06);
      transition: box-shadow 0.18s, background 0.18s;
      position: relative;
    }
    .form-container input:focus,
    .form-container textarea:focus {
      background: #ffeaea;
      box-shadow: 0 4px 16px rgba(225,0,0,0.13);
    }
    .form-container .input-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.1em;
      color: #e10000;
      opacity: 0.85;
      pointer-events: none;
    }
    .input-wrapper {
      position: relative;
      width: 100%;
    }
    .form-container input::placeholder,
    .form-container textarea::placeholder {
      color: #e10000;
      font-size: 0.98em;
      opacity: 0.7;
    }
    .form-container button,
    .refresh-btn {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 30px;
      background: linear-gradient(90deg, #e10000 0%, #ff512f 100%);
      color: #fff;
      font-weight: bold;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background 0.18s, box-shadow 0.18s, transform 0.12s;
      box-shadow: 0 4px 16px rgba(225,0,0,0.13);
      margin-top: 8px;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .form-container button::before {
      content: "🔒";
      font-size: 1.1em;
      margin-right: 6px;
      opacity: 0.85;
    }
    .refresh-btn {
      background: linear-gradient(90deg, #1a7f37 0%, #43e97b 100%);
      color: #fff;
      font-weight: bold;
      font-size: 1.1rem;
      margin-top: 24px;
      border-radius: 30px;
      box-shadow: 0 4px 16px rgba(34,197,94,0.13);
      transition: background 0.18s, box-shadow 0.18s, transform 0.12s;
    }
    .refresh-btn:hover,
    .refresh-btn:focus {
      background: linear-gradient(90deg, #43e97b 0%, #1a7f37 100%);
      transform: scale(1.04);
      box-shadow: 0 8px 32px rgba(34,197,94,0.18);
    }
    .form-container button:hover,
    .form-container button:focus {
      background: linear-gradient(90deg, #ff512f 0%, #e10000 100%);
      color: #fff;
      transform: scale(1.04);
      box-shadow: 0 8px 32px rgba(225,0,0,0.18);
    }
    .message.error {
      background-color: #f8d7da;
      color: #e10000;
      padding: 12px;
      border-radius: 12px;
      margin-top: 10px;
      font-size: 1rem;
      text-align: center;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(225,0,0,0.06);
    }
    .success-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 36px 0 18px 0;
      min-height: 320px;
    }
    .success-tick {
      font-size: 3.5rem;
      color: #1a7f37;
      margin-bottom: 18px;
      animation: popIn 0.5s cubic-bezier(.23,1.01,.32,1);
    }
    @keyframes popIn {
      0% { transform: scale(0.5); opacity: 0; }
      80% { transform: scale(1.15); opacity: 1; }
      100% { transform: scale(1); }
    }
    .success-msg {
      color: #1a7f37;
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 10px;
      text-align: center;
      letter-spacing: 0.5px;
    }
    @media screen and (max-width: 480px) {
      body {
        padding: 10px;
        align-items: flex-start;
      }
      .form-container {
        padding: 20px 10px;
        border-radius: 16px;
        max-width: 98vw;
      }
      .form-container h2 {
        font-size: 1.3rem;
        margin-bottom: 18px;
      }
      .form-container input,
      .form-container textarea {
        font-size: 0.98rem;
        padding: 11px 12px 11px 38px;
      }
      .form-container button,
      .refresh-btn {
        font-size: 1rem;
        padding: 12px;
      }
      .message.error,
      .success-msg {
        font-size: 0.98rem;
      }
      .success-tick {
        font-size: 2.2rem;
      }
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Register</h2>
    <?php if ($signupSuccess): ?>
      <div class="success-box">
        <div class="success-tick">✅</div>
        <div class="success-msg">Registered Successfully!</div>
        <button class="refresh-btn" onclick="window.location.reload();">Refresh</button>
      </div>
    <?php else: ?>
      <?php if (!empty($errorMessage)): ?>
        <div class="message error">
          <strong>Error:</strong><br><?= htmlspecialchars($errorMessage) ?>
        </div>
      <?php endif; ?>
      <form method="POST" action="" onsubmit="return validateForm();">
        <div class="input-wrapper">
          <span class="input-icon">👤</span>
          <input type="text" name="name" id="name" placeholder="Full Name" required />
        </div>
        <div class="input-wrapper">
          <span class="input-icon">📧</span>
          <input type="email" name="email" id="email" placeholder="Email Address" required />
        </div>
        <div class="input-wrapper">
          <span class="input-icon">🔑</span>
          <input type="password" name="password" id="password" placeholder="Password (min 6 chars)" required />
        </div>
        <div class="input-wrapper">
          <span class="input-icon">📱</span>
          <input type="text" name="mobile" id="mobile" placeholder="Mobile Number" maxlength="10" required />
        </div>
        <div class="input-wrapper">
          <span class="input-icon">🏠</span>
          <textarea name="address" id="address" placeholder="Address" required></textarea>
        </div>
        <button type="submit">Register</button>
        <p>Have an account?<a href="login.php">Login</a>
      </form>
    <?php endif; ?>
  </div>
  <script>
    function validateForm() {
      const email = document.getElementById('email').value;
      const mobile = document.getElementById('mobile').value;
      const password = document.getElementById('password').value;

      const mobileRegex = /^[6-9]\d{9}$/;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailRegex.test(email)) {
        alert("Please enter a valid email address.");
        return false;
      }

      if (!mobileRegex.test(mobile)) {
        alert("Enter a valid 10-digit Indian mobile number.");
        return false;
      }

      if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return false;
      }

      return true;
    }
  </script>
</body>
</html>