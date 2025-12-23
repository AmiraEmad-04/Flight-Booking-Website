<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKYBOOK</title>
    <?php include '../../navbar/navbar_css.php'; ?> <!-- Fixed path -->
    <link rel="stylesheet" href="../../css/Home/style.css">
    <script src="../../js/jquery.min.js"></script>
    <script src="../../js/jquery-ui.min.js"></script>
</head>
<body>
    <?php include '../../navbar/navbar.php'; ?> <!-- Fixed path -->
    
    <!-- Intro Section -->
    <section class="intro">
        <div class="container">
            <div class="intro-content">
                <h3 class="fade-in">AIR TRAVEL BOOKING</h3>
                <h1 class="slide-in">Your Journey Starts Here</h1>
                <p class="fade-in-delay">We provide always our best services for our clients and always try to achieve our client's trust and satisfaction.</p>
                <a href="#features" class="btn learn-more-btn">Learn More <i>→</i></a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="services-grid">
            <div class="service-card passenger-card">
                <div class="service-icon">
                    <img src="../../icons/user.svg" alt="Passenger">
                </div>
                <h3>PASSENGER</h3>
                <p>Search & book flights</p>
                <a href="#" class="read-more-link">Read More <img src="../../icons/arrow-right.svg" alt="→"></a>
            </div>
            <div class="service-card company-card">
                <div class="service-icon">
                    <img src="../../icons/building.svg" alt="Company">
                </div>
                <h3>COMPANY</h3>
                <p>Manage your flights</p>
                <a href="#" class="read-more-link">Read More <img src="../../icons/arrow-right.svg" alt="→"></a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="../../icons/globe-americas.svg" alt="Global Network">
                </div>
                <h4>Global Network</h4>
                <p>Access to flights worldwide with our extensive airline partnerships.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="../../icons/shield-alt.svg" alt="Secure Booking">
                </div>
                <h4>Secure Booking</h4>
                <p>Your data is protected with industry-leading security measures.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="../../icons/headset.svg" alt="24/7 Support">
                </div>
                <h4>24/7 Support</h4>
                <p>Our team is always available to help you with your travel needs.</p>
            </div>
        </div>
    </section>
</body>
</html>