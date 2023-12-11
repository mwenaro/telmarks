<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>TELMARK AGENCIES | LOGIN </title>
  <link rel="stylesheet" href="login.css" />
  <!--Add TAILWING CDN  -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
  <div class="wrapper" style="background:#fff; width:70%; max-width:600px; height:max-height:600px; margin: 0 auto;">
    <div class="form-box login" id="login-form">
      <h2>Login</h2>
      <form action="connection.php" method="POST">
        <!-- Specify your PHP script's filename in the action attribute -->
        <div class="input-box">
          <span class="icon">
            <ion-icon name="mail"></ion-icon>
          </span>
          <input type="text" name="login_username" required />
          <!-- Add name attribute -->
          <label>Username</label>
        </div>
        <div class="input-box">
          <span class="icon">
            <ion-icon name="lock-closed"></ion-icon>
          </span>
          <input type="password" name="login_password" required />
          <!-- Add name attribute -->
          <label>Password</label>
        </div>
        <div class="remember-forgot">
          <label><input type="checkbox" required /> Remember me</label>
          <a href="./forgot_password.php">Forgot Password?</a>
        </div>
        <button type="submit" class="bth" name="login">Login</button>
        <!-- Add name attribute and value for the submit button -->
        <div class="login-register">
          <p>
            Don't have an account?
            <a href="./register.php" class="register-link">Register</a>
          </p>
        </div>
      </form>
    </div>

  </div>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
    // Function to extract and clean the referral code from the URL
    function extractReferralCode() {
        const urlParams = new URLSearchParams(window.location.search);
        const referralCode = urlParams.get('ref');

        if (referralCode) {
            return referralCode;
        }

        var currentUrl = window.location.href;

        if (currentUrl.includes("ref=")) {
            var parts = currentUrl.split("ref=");
            var cleanReferralCode = parts[1].split("#")[0];

            return cleanReferralCode;
        }

        return null;
    }

    var referralCode = extractReferralCode();

    if (referralCode) {
        document.getElementById('referred_by').innerHTML = referralCode;
    }
</script>

</body>

</html>