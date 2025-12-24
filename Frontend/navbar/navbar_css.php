<!-- Navbar Styles -->
<style>
    header {
        padding: 10px;
        position: absolute;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 1000;
    }
    header::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        top: -150px;
        right: -100px;
    }

    header::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        bottom: -100px;
        left: -50px;
    }

    header .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
    }

    .logo {
        color: white;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: 1px;
        animation: fadeInDown 0.8s ease-out;
    }

    .logo span {
        color: #ffd700;
    }

    nav ul {
        display: flex;
        list-style: none;
    }

        nav ul li {
        margin-left: 30px;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        padding: 8px 16px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    nav ul li a:hover, nav ul li a.active {
        background-color: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }

    /* Message Icon Styles */
    .messages-nav-item {
        position: relative;
    }

    .messages-link {
        position: relative;
        display: flex;
        align-items: center;
        gap: 5px;
        color: white;
        text-decoration: none;
    }

    .message-icon {
        transition: transform 0.3s;
    }

    .messages-link:hover .message-icon {
        transform: scale(1.1);
    }

    .unread-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ff4444;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 15px;
        }

        nav ul {
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }
    }
</style>

<script>
    function handleLogout(event) {
        event.preventDefault();
        
        // Use absolute path from root
        const logoutUrl = window.location.origin + '/Flight-Booking-Website/Backend/services/auth.php?action=logout';
        
        fetch(logoutUrl)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = window.location.origin + '/Flight-Booking-Website/Frontend/pages/auth/login.php';
                } else {
                    alert('Logout failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during logout');
            });
    }
</script>