<?php
// Basic PHP test
echo "PHP is working!<br>";

// Test database connection
try {
    require_once 'includes/config.php';
    echo "Database connection successful!<br>";
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Test session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Session started successfully!<br>";

// Basic HTML structure
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insul-In-Sync - Diabetes Management Made Easy</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/accessibility.css">
    <style>
        .hero {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(rgba(76, 175, 80, 0.1), rgba(76, 175, 80, 0.2));
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .hero h1 {
            color: #4CAF50;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .hero p {
            color: #333;
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .cta-button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .cta-button:hover {
            background-color: #45a049;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .feature-card h3 {
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: #666;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="reminders.php">Reminders</a></li>
            <li><a href="education.php">Education</a></li>
            <li><a href="contact.php">Emergency Contacts</a></li>
            <li class="dropdown">
                <a href="#">Account</a>
                <div class="dropdown-content">
                    <a href="data_access.php">Data Access</a>
                    <a href="includes/logout.php">Log Out</a>
                </div>
            </li>
            <li><a href="about_us.php">About Us</a></li>
            <?php if(isset($_SESSION['username'])): ?>
                <li><a href="includes/logout.php">Log Out</a></li>
            <?php else: ?>
                <li><a href="login.php">Log in / Sign up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php include 'includes/accessibility_widget.php'; ?>

    <div class="container">
        <div class="hero">
            <img src="Pictures/pic6.png" alt="Diabetes blue circle with red drop" style="max-width: 500px; width: 90vw; height: 220px; object-fit: contain; display: block; margin: 0 auto 30px auto; box-shadow: 0 4px 24px rgba(0,0,0,0.10); border-radius: 12px; background: #fff;" />
            <h1>Welcome to Insul-In-Sync</h1>
            <p>Your comprehensive diabetes management companion</p>
            <div class="cta-buttons">
                <?php if(!isset($_SESSION['username'])): ?>
                    <a href="login.php" class="cta-button">Get Started</a>
                    <a href="signup.php" class="cta-button">Create Account</a>
                <?php else: ?>
                    <a href="reminders.php" class="cta-button">Go to Dashboard</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <h3>Smart Reminders</h3>
                <p>Never miss a medication or appointment with our intelligent reminder system</p>
            </div>
            <div class="feature-card">
                <h3>Educational Resources</h3>
                <p>Access comprehensive diabetes education materials and guides</p>
            </div>
            <div class="feature-card">
                <h3>Medical Contacts</h3>
                <p>Quick access to healthcare professionals and emergency services</p>
            </div>
            <div class="feature-card">
                <h3>Calendar Management</h3>
                <p>Organize your medical appointments and daily health routines</p>
            </div>
        </div>
    </div>

    <script src="js/accessibility.js"></script>
</body>
</html>
