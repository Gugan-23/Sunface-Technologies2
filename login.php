<?php
session_start();
require_once 'db_connect.php';

$login_error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM signup WHERE email = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

   if ($user) {
    if ($password === $user['password'] || password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_mobile'] = $user['mobile'];
        $_SESSION['user_address'] = $user['address'];
        $_SESSION['login_method'] = $user['login_method'];

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Welcome back, ' . $user['name'] . '!'
        ];

        // Send message to parent window to close modal and redirect
        echo '<script>
            if (window.parent !== window) {
                window.parent.postMessage({action: "loginSuccess", redirectUrl: "index.php"}, "*");
            } else {
                window.location.href = "index.php";
            }
        </script>';
        exit();
    } else {
        $login_error = "❌ Invalid email or password.";
    }
}
}

// Handle Google login callback
if (isset($_GET['google_login'])) {
    $name = $_GET['name'];
    $email = $_GET['email'];
    $login_method = 'google';
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM signup WHERE email = ?");
    if (!$stmt) {
        die("Database error (SELECT): " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Add default values for required fields
        $defaultPassword = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        $defaultMobile = '0000000000';
        
        $insert_stmt = $conn->prepare(
            "INSERT INTO signup (name, email, password, mobile, login_method, created) 
            VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $insert_stmt->bind_param("sssss", $name, $email, $defaultPassword, $defaultMobile, $login_method);
        $insert_stmt->execute();
        $user_id = $conn->insert_id;
        $insert_stmt->close();
    } else {
        // User exists, get their data
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
    }
    $stmt->close();
    
    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    $_SESSION['login_method'] = $login_method;
    
   echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Redirecting</title>
        <script>
            if (window.opener) {
                window.opener.postMessage({action: "loginSuccess", redirectUrl: "index.php"}, "*");
                window.close();
            } else if (window.parent !== window) {
                window.parent.postMessage({action: "loginSuccess", redirectUrl: "index.php"}, "*");
            } else {
                window.location.href = "index.php";
            }
        </script>
    </head>
    <body>
        Redirecting...
    </body>
    </html>';
    exit();
}

// Handle Mobile OTP login callback
if (isset($_GET['otp_login'])) {
    $mobile = $_GET['mobile'];
    $login_method = 'otp';

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM signup WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Create new user with mobile number
        $defaultPassword = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        $defaultName = 'User';
        $defaultEmail = null; // <-- FIXED: use null instead of empty string
        $insert_stmt = $conn->prepare(
            "INSERT INTO signup (name, email, password, mobile, login_method, created) 
            VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $insert_stmt->bind_param("sssss", $defaultName, $defaultEmail, $defaultPassword, $mobile, $login_method);
        $insert_stmt->execute();
        $user_id = $conn->insert_id;
        $insert_stmt->close();

        // Set default user data
        $user = [
            'email' => '',
            'name' => 'User',
            'address' => ''
        ];
    } else {
        // User exists, get their data
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
    }
    $stmt->close();

    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_mobile'] = $mobile;
    $_SESSION['login_method'] = $login_method;

    // Set default values for other session variables
    $_SESSION['user_email'] = $user['email'] ?? '';
    $_SESSION['user_name'] = $user['name'] ?? 'User';
    $_SESSION['user_address'] = $user['address'] ?? '';

    // Set flash message
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Welcome' . (isset($user['name']) ? ', ' . $user['name'] : '') . '!'
    ];


    // Redirect like Google login (close modal or popup and redirect parent)
echo '<!DOCTYPE html>
<html>
<head>
    <title>Redirecting</title>
    <script>
        if (window.opener) {
            window.opener.postMessage({action: "loginSuccess", redirectUrl: "index.php"}, "*");
            window.close();
        } else if (window.parent !== window) {
            window.parent.postMessage({action: "loginSuccess", redirectUrl: "index.php"}, "*");
        } else {
            window.location.href = "index.php";
        }
    </script>
</head>
<body>
    Redirecting...
</body>
</html>';
exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Sunface</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Add Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
    <style>
        :root {
            --primary-color: #4285F4;
            --secondary-color: #34A853;
            --accent-color: #EA4335;
            --light-gray: #f5f5f5;
            --medium-gray: #e0e0e0;
            --dark-gray: #757575;
            --text-color: #202124;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            transform: translateY(0);
            transition: var(--transition);
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3367D6 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header h2 {
            font-weight: 600;
            font-size: 28px;
            margin-bottom: 5px;
            position: relative;
            z-index: 2;
        }

        .login-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            z-index: 1;
        }

        .login-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
            font-size: 14px;
        }

        .input-field {
            position: relative;
        }

        .input-field i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dark-gray);
            font-size: 18px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid var(--medium-gray);
            border-radius: 8px;
            font-size: 15px;
            transition: var(--transition);
            background-color: #fafafa;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: white;
            box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.2);
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            background-color: #3367D6;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn i {
            margin-right: 10px;
            font-size: 18px;
        }

        .error-message {
            background: #FFEBEE;
            color: #C62828;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            border-left: 4px solid #C62828;
        }

        .error-message i {
            color: #C62828;
        }

        /* Google Button Styles */
        .google-btn {
            background: white;
            color: #5F6368;
            border: 1px solid #DADCE0;
            box-shadow: var(--shadow-sm);
            margin: 15px 0;
            position: relative;
            overflow: hidden;
        }

        .google-btn:hover {
            background: #F8F9FA;
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            color: var(--text-color);
        }

        .google-btn:active {
            transform: translateY(0);
            background: #F1F3F4;
        }

        .google-btn::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: var(--secondary-color);
        }

        .google-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }

        /* Mobile OTP Button */
        .mobile-btn {
            background: #F1F3F4;
            color: var(--text-color);
            margin: 10px 0;
        }

        .mobile-btn:hover {
            background: #E8EAED;
            transform: translateY(-2px);
        }

        .mobile-btn:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: var(--dark-gray);
            font-size: 14px;
            font-weight: 500;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid var(--medium-gray);
        }

        .divider::before {
            margin-right: 15px;
        }

        .divider::after {
            margin-left: 15px;
        }

        /* Footer Links */
        .footer-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .footer-links a:hover {
            text-decoration: underline;
            color: #3367D6;
        }

        .footer-links p {
            margin-bottom: 10px;
        }

        /* OTP Container */
        #otp-container {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: var(--light-gray);
            border-radius: 8px;
            animation: fadeIn 0.3s ease-out;
        }

        #otp-section {
            display: none;
            margin-top: 15px;
            animation: fadeIn 0.3s ease-out;
        }

        #recaptcha-container {
            margin: 15px 0;
        }

        #message {
            margin-top: 10px;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 4px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transform: translateY(20px);
            transition: all 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-content h3 {
            color: var(--secondary-color);
            margin-bottom: 20px;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content h3 i {
            margin-right: 10px;
            font-size: 28px;
        }

        .user-info {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }

        .user-info p {
            margin-bottom: 10px;
            display: flex;
        }

        .user-info p strong {
            min-width: 120px;
            display: inline-block;
            color: var(--dark-gray);
        }

        .close-btn {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            width: 100%;
            margin-top: 10px;
        }

        .close-btn:hover {
            background: #2D9144;
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .close-btn:active {
            transform: translateY(0);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Responsive Adjustments */
        @media (max-width: 480px) {
            .login-container {
                border-radius: 12px;
            }
            
            .login-header {
                padding: 25px;
            }
            
            .login-body {
                padding: 25px;
            }
            
            input[type="email"],
            input[type="password"],
            input[type="text"] {
                padding: 12px 12px 12px 40px;
            }
        }
        
    </style>
</head>


</body>
</html>

<body>

    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-user-circle"></i> Welcome Back</h2>
            <p>Sign in to continue to Sunface</p>
        </div>
        

        <div class="login-body">
            <?php if (!empty($login_error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $login_error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="email" placeholder="Enter your email" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Enter your password" required />
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="divider">or continue with</div>

            <button class="btn google-btn" onclick="googleLogin()">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="google-icon" alt="Google logo">
                Continue with Google
            </button>

<button id="signInButton" class="btn mobile-btn" onclick="showOtpContainer()">
  <i class="fas fa-mobile-alt"></i> Sign in with Mobile
</button>



            <div id="otp-container">
                <h3><i class="fas fa-sms"></i> Login with Mobile OTP</h3>
                <div class="input-field">
                    <i class="fas fa-phone"></i>
                    <input type="text" id="mobile" placeholder="+91XXXXXXXXXX" />
                </div>
                <button type="button" class="btn btn-primary" onclick="sendOTP()">
                    <i class="fas fa-paper-plane"></i> Send OTP
                </button>

                <div id="recaptcha-container"></div>

                <div id="otp-section">
                    <div class="input-field">
                        <i class="fas fa-key"></i>
                        <input type="text" id="otp" placeholder="Enter OTP" />
                    </div>
                    <button type="button" class="btn btn-primary" onclick="verifyOTP()">
                        <i class="fas fa-check-circle"></i> Verify OTP
                    </button>
                </div>

                <p id="message"></p>
            </div>

            <div class="footer-links">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                <p><a href="#">Forgot password?</a></p>
            </div>
        </div>
    </div>

    <!-- Success Modal Dialog -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-check-circle"></i> Login Successful!</h3>
            <div class="user-info" id="userInfo">
            </div>
            <button class="close-btn" onclick="closeModal()">Continue</button>
        </div>
    </div>

    <script>
        // Initialize Firebase
         // Initialize Firebase
        const firebaseConfig = {
            apiKey: "AIzaSyBX65M6zmsESQREF4HH7NHvSf20wvzJzgI",
            authDomain: "mobileotp-42e20.firebaseapp.com",
            projectId: "mobileotp-42e20",
            storageBucket: "mobileotp-42e20.appspot.com",
            messagingSenderId: "524800223982",
            appId: "1:524800223982:web:c8413883342212084261ff",
            measurementId: "G-QBYDNE78RE"
        };

        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();

        // Google Login Function
        function googleLogin() {
            const provider = new firebase.auth.GoogleAuthProvider();
            auth.signInWithPopup(provider)
                .then((result) => {
                    const user = result.user;
                    window.location.href = `login.php?google_login=1&name=${encodeURIComponent(user.displayName)}&email=${encodeURIComponent(user.email)}`;
                })
                .catch((error) => {
                    console.error("Google Sign-In Error:", error);
                    alert("Google Sign-In failed: " + error.message);
                });
        }

        // Mobile OTP Variables
       let confirmationResult;

  window.onload = () => {
    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier("recaptcha-container", {
      size: "normal",
      callback: () => console.log("reCAPTCHA solved"),
      "expired-callback": () => alert("reCAPTCHA expired. Reload the page.")
    });
    recaptchaVerifier.render();
  };

  function sendOTP() {
    const phone = document.getElementById("mobile").value.trim();
    if (!phone.startsWith("+")) {
      alert("Please include country code (e.g. +91)");
      return;
    }

    firebase.auth().signInWithPhoneNumber(phone, window.recaptchaVerifier)
      .then((result) => {
        confirmationResult = result;
        document.getElementById("otp-section").style.display = "block";
        document.getElementById("message").innerText = "✅ OTP sent to " + phone;
        document.getElementById("message").style.color = "#4CAF50";
      })
      .catch((error) => {
        document.getElementById("message").innerText = "❌ Error: " + error.message;
        document.getElementById("message").style.color = "#f44336";
      });
  }

 function verifyOTP() {
  const code = document.getElementById("otp").value.trim();
  if (!code) {
    alert("Please enter the OTP");
    return;
  }

  confirmationResult.confirm(code)
    .then((result) => {
      const user = result.user;
      const phone = user.phoneNumber;

      // ✅ Redirect to PHP backend with ?otp_login=1&mobile=+91xxxx
      window.location.href = `login.php?otp_login=1&mobile=${encodeURIComponent(phone)}`;
    })
    .catch((error) => {
      document.getElementById("message").innerText = "❌ Invalid OTP. Try again.";
      document.getElementById("message").style.color = "#f44336";
    });
}


  function showOtpContainer() {
  // Hide the sign-in button (if needed)
  const signInBtn = document.getElementById('signInButton');
  if (signInBtn) signInBtn.style.display = 'none';

  // Show the OTP container
  const otpContainer = document.getElementById('otp-container');
  if (otpContainer) otpContainer.style.display = 'block';

  // Hide OTP section initially
  const otpSection = document.getElementById('otp-section');
  if (otpSection) otpSection.style.display = 'none';

  // Clear messages and input fields (optional)
  document.getElementById('mobile').value = '';
  document.getElementById('otp').value = '';
  document.getElementById('message').innerText = '';
}

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        

    </script>
</body>
</html>
