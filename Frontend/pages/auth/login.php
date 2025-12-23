<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SKYBOOK</title>
    <?php include '../../navbar/navbar_css.php'; ?>
    <link rel="stylesheet" href="../../css/auth/global.css">
    <link rel="stylesheet" href="../../css/auth/forms.css">
    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/auth/login.js"></script>
</head>
<body>
    <?php include '../../navbar/navbar.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <h1>Welcome Back</h1>
            <p>Login to access your account</p>
        </div>
    </section>

    <section class="auth-section">
        <div class="auth-container">
            <h2>Login to Your Account</h2>
            <p>Enter your credentials below</p>

            <form id="loginForm">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Enter your email">

                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">

                <button type="submit" class="primary-btn">Login</button>
            </form>

            <p class="auth-link">Don't have an account? <a href="register.php">Sign Up</a></p>
        </div>
    </section>
</body>
</html>