<?php include('registration.php');?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>TELMARK AGENCIES |  REGISTRATION</title>
  <style>
  body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-size: cover;
    background: #ebeff2;
  }

  .wrapper {
    position: relative;
    width: 400px;
    height: 620px;
    background: #fff;
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    box-shadow: 0 0 30px rgba(235, 239, 242, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    transition: transform 0.5s ease, height 0.2s ease;
  }

  .wrapper .form-box {
    width: 100%;
    padding: 20px;
  }

  .form-box h2 {
    font-size: 2em;
    color: #000;
    text-align: center;
  }

  .input-box {
    position: relative;
    width: 100%;
    height: 40px;
    border-bottom: 2px solid #000;
    margin: 30px 0;
  }

  .input-box label {
    position: absolute;
    top: 50%;
    left: 5px;
    transform: translateY(-50%);
    font-size: 1em;
    color: #000;
    font-weight: 500;
    pointer-events: none;
    transition: 0.5s;
  }

  .input-box input:focus ~ label,
  .input-box input:valid ~ label {
    top: -5px;
  }

  .input-box input {
    width: 100%;
    height: 100%;
    background: transparent;
    border: none;
    outline: none;
    font-size: 1em;
    color: #000;
    font-weight: 600;
    padding: 0 35px 0 5px;
  }

  .input-box .icon {
    position: absolute;
    right: 8px;
    font-size: 1.2em;
    color: #000;
    line-height: 57px;
  }

  .remember-forgot {
    font-size: 0.9em;
    color: #000;
    font-weight: 500;
    margin: -15px 0 15px;
    display: flex;
    justify-content: space-between;
  }

  .remember-forgot label input {
    accent-color: #000;
    margin-right: 3px;
  }

  .remember-forgot a {
    color: #000;
    text-decoration: none;
  }

  .remember-forgot a:hover {
    text-decoration: underline;
  }

  .bth {
    width: 100%;
    height: 45px;
    background: #ff9933;
    border: none;
    outline: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1em;
    color: #ffffff;
    font-weight: 500;
  }

  .login-register {
    font-size: 0.9em;
    color: #ff9933;
    text-align: center;
    font-weight: 500;
    margin: 25px 0 10px;
  }

  .login-register p a {
    color: #000;
    text-decoration: none;
    font-weight: 600;
  }

  .login-register p a:hover {
    text-decoration: underline;
  }
</style>

</head>

<body>
    <div class="wrapper" style="background:#fff;">
        <div class="form-box register" id="register-form">
            <h2>Registration</h2>
            <form action="registration.php" method="POST">
                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="person"></ion-icon>
                    </span>
                    
                    <?php
                    // Assuming the $conn variable is defined before this point in your code
                    $referred_by = (isset($_GET['ref'])) ? sanitize($conn, $_GET['ref']) : null;
                    ?>
                    <!-- Adjusted line for sanitizing the invited_by value -->
                    <input type="text" name="invited_by" readonly value="<?php echo ($referred_by) ? sanitize($conn, $referred_by) : 'None'; ?>">
                    <label>Invited By</label>
                </div>
                <input type="hidden" name="referred_by" value="<?php echo $referred_by; ?>">

                <!-- Rest of the form remains unchanged -->
                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="person"></ion-icon>
                    </span>
                    <input type="text" name="your_name" required />
                    <label>Your Name</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="person"></ion-icon>
                    </span>
                    <input type="text" name="reg_username" required />
                    <label>Username</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="mail"></ion-icon>
                    </span>
                    <input type="email" name="reg_email" required />
                    <label>Email</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="person"></ion-icon>
                    </span>
                    <input type="text" name="phone_number" required />
                    <label>Phone Number</label>
                </div>

                <div class="input-box">
                    <span class="icon">
                        <ion-icon name="lock-closed"></ion-icon>
                    </span>
                    <input type="password" name="reg_password" required />
                    <label>Password</label>
                </div>

                <div class="remember-forgot">
                    <label>
                        <input type="checkbox" required /> I agree to the
                        <a href="./terms.php" target="_blank">terms & conditions</a>
                    </label>
                </div>

                <button type="submit" class="bth" name="register" value="Register">Register</button>

                <div class="login-register">
                    <p>
                        Already have an account? <a href="./index.php" class="login-link">Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>

    var referralCode = extractReferralCode();

    if (referralCode) {
      document.getElementById('referred_by').innerHTML = referralCode;
    }
    

</script>

</body>

</html>