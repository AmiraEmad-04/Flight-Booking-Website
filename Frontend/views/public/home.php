<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking System - Your Journey Starts Here</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        /* Top Bar */
        .top-bar {
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 0;
            position: absolute;
            width: 100%;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .top-bar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-info {
            color: white;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .top-links {
            display: flex;
            gap: 30px;
        }

        .top-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .top-links a:hover {
            color: #64b5f6;
        }

        /* Navigation */
        nav {
            position: absolute;
            top: 50px;
            width: 100%;
            z-index: 100;
            padding: 20px 0;
        }

        .nav-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            font-size: 28px;
            font-weight: 700;
            text-decoration: none;
        }

        .logo-icon {
            font-size: 36px;
        }

        .nav-links {
            display: flex;
            gap: 45px;
            list-style: none;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #4a90e2 100%);
            z-index: -1;
        }

        .hero-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('airplane-bg.jpg') center/cover no-repeat;
            opacity: 0.4;
            z-index: 1;
        }

        .hero-background::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
            z-index: 2;
        }

        .airplane-image {
            position: absolute;
            right: -100px;
            top: 50%;
            transform: translateY(-50%);
            width: 60%;
            max-width: 800px;
            opacity: 0.9;
            z-index: 1;
            animation: floatPlane 6s ease-in-out infinite;
        }

        @keyframes floatPlane {
            0%, 100% { transform: translateY(-50%) translateX(0); }
            50% { transform: translateY(-55%) translateX(-20px); }
        }

        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 50px;
            z-index: 10;
            color: white;
        }

        .hero-label {
            font-size: 16px;
            font-weight: 500;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
            opacity: 0.9;
            animation: fadeInDown 1s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 0.9;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 72px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 25px;
            max-width: 700px;
            animation: fadeInLeft 1s ease 0.3s both;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-description {
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 50px;
            max-width: 600px;
            font-weight: 300;
            opacity: 0.95;
            animation: fadeInLeft 1s ease 0.6s both;
        }

        /* Buttons */
        .hero-buttons {
            display: flex;
            gap: 25px;
            animation: fadeInUp 1s ease 0.9s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn {
            padding: 18px 45px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: #64b5f6;
            color: white;
        }

        .btn-primary:hover {
            background: #42a5f5;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(100, 181, 246, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: #1e3c72;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        }

        .btn-icon {
            font-size: 18px;
        }

        /* Features Section */
        .features {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 10;
        }

        .features-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 50px 50px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .feature-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .feature-description {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 300;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .airplane-image {
                width: 55%;
                right: -80px;
            }

            h1 {
                font-size: 56px;
            }
        }

        @media (max-width: 968px) {
            .nav-links {
                display: none;
            }

            .features-container {
                grid-template-columns: repeat(2, 1fr);
            }

            h1 {
                font-size: 48px;
            }

            .hero-description {
                font-size: 18px;
            }

            .airplane-image {
                width: 70%;
                right: -120px;
                opacity: 0.5;
            }
        }

        @media (max-width: 768px) {
            .top-bar-content,
            .nav-content,
            .hero-content,
            .features-container {
                padding: 0 25px;
            }

            h1 {
                font-size: 40px;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .features-container {
                grid-template-columns: 1fr;
                padding-bottom: 30px;
            }

            .airplane-image {
                display: none;
            }
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            animation: bounce 2s infinite;
        }

        .scroll-indicator span {
            display: block;
            width: 30px;
            height: 50px;
            border: 2px solid white;
            border-radius: 25px;
            position: relative;
        }

        .scroll-indicator span::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            animation: scrollDown 2s infinite;
        }

        @keyframes scrollDown {
            0% {
                top: 10px;
                opacity: 1;
            }
            100% {
                top: 30px;
                opacity: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="contact-info">
                📞 1800-000-999
            </div>
            <div class="top-links">
                <a href="#">Request a Quote</a>
                <a href="Backend/temp_login.php">Customer Login</a>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav>
        <div class="nav-content">
            <a href="#" class="logo">
                <span class="logo-icon">✈️</span>
                <span>FlyBooking</span>
            </a>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background">
            <!-- Background image will be here -->
             <img src="D:\XXAMP\htdocs\Flight-Booking-Website\Frontend\images\plane.jpeg" alt="Airplane" class="airplane-image">
        </div>

        <!-- You'll add your airplane image here via CSS or replace this comment -->
         

        <div class="hero-content">
            <div class="hero-label">AIR FREIGHT</div>
            <h1>Your Journey Starts Here</h1>
            <p class="hero-description">
                We provide the best flight booking services for our clients and always try to achieve our client's trust and satisfaction.
            </p>
            <div class="hero-buttons">
                <a href="Backend/login.php" class="btn btn-primary">
                    <span>Login Now</span>
                    <span class="btn-icon">→</span>
                </a>
                <a href="Backend/register.php" class="btn btn-secondary">
                    <span>Create Account</span>
                    <span class="btn-icon">✓</span>
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <span></span>
        </div>
    </section>

    <!-- Features Section -->
    <div class="features">
        <div class="features-container">
            <div class="feature-card">
                <div class="feature-icon">✈️</div>
                <div class="feature-title">Air Freight</div>
                <div class="feature-description">Fast and reliable air shipping</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌊</div>
                <div class="feature-title">Ocean Freight</div>
                <div class="feature-description">Cost-effective ocean transport</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <div class="feature-title">Inland Freight</div>
                <div class="feature-description">Door-to-door delivery service</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <div class="feature-title">Warehouse</div>
                <div class="feature-description">Secure storage solutions</div>
            </div>
        </div>
    </div>

</body>
</html>