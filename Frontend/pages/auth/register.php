<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SKYBOOK</title>
    <link rel="stylesheet" href="../../css/auth/global.css">
    <link rel="stylesheet" href="../../css/auth/forms.css">
    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/auth/register.js"></script>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <span class="sky">SKY</span><span class="book">BOOK</span>
            </div>
            <nav>
                <ul>
                    <li><a href="../../Home/home.html">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="register-btn">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Create Your Account</h1>
            <p>Join thousands of satisfied travelers</p>
        </div>
    </section>

    <section class="auth-section">
        <div class="auth-container">
            <h2>Register</h2>
            <p>Enter your basic information</p>

            <form id="registerForm">
                <div class="account-type">
                    <button type="button" class="type-btn active" data-type="passenger">Passenger</button>
                    <button type="button" class="type-btn" data-type="company">Company</button>
                </div>
                <input type="hidden" name="type" id="accountType" value="passenger">

                <label>Name</label>
                <input type="text" name="name" required placeholder="Enter your full name">

                <label>Email</label>
                <input type="email" name="email" required placeholder="Enter your email">

                <label>Password</label>
                <input type="password" name="password" required placeholder="Create a password">

                <label>Phone Number</label>
                <input type="tel" name="tel" required placeholder="Enter your phone number">

                <label>Account Balance ($)</label>
                <input type="number" name="account_balance" value="0.00" step="0.01" required placeholder="0.00">

                <button type="submit" class="primary-btn">Continue</button>
            </form>

            <p class="auth-link">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </section>
</body>
</html>