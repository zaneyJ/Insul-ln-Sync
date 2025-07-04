<?php
// Include database config and start session
include_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insul-In-Sync - Diabetes Management Made Easy</title>
    <!-- Main stylesheets -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/accessibility.css">
    <style>
        /* Hero and feature section styles */
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
    <?php // Main navigation bar ?>
    <?php include 'includes/navbar.php'; ?>
    <?php // Accessibility widget ?>
    <?php include 'includes/accessibility_widget.php'; ?>
    
    <?php // Display success/error messages ?>
    <?php if(isset($_GET['message'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px; border: 1px solid #c3e6cb;">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 4px; border: 1px solid #f5c6cb;">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="container">
        <!-- Hero section with welcome and call-to-action -->
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
        <!-- Features grid -->
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
