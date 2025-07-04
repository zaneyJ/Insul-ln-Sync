<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibility Widget</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/accessibility.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/accessibility_widget.php'; ?>
    <div class="container">
        <h1>Welcome to Insul-In-Sync</h1>
        <p>
            <strong>Insul-In-Sync</strong> is a user-friendly platform designed to help individuals—especially older adults and people with disabilities—manage their diabetes more effectively. With features like blood glucose logging, personalized insights, caregiver dashboards, and emergency contact tools, Insul-In-Sync empowers users to take control of their health and live more independently.
        </p>

        <h2>Using the Accessibility Widget</h2>
        <p>
            To ensure an inclusive experience for all, we've included an <strong>Accessibility Widget</strong> located at the top-right corner of the screen (marked with the ♿ symbol).
        </p>
        <ul>
            <li><strong>A+ / A- Buttons:</strong> Increase or decrease the text size to improve readability.</li>
            <li><strong>High Contrast:</strong> Switch to a high-contrast color scheme for better visibility.</li>
            <li><strong>Text-to-Speech:</strong> Enable spoken feedback by clicking on images or selecting text—great for users with visual impairments or reading difficulties.</li>
        </ul>
        <p>
            Simply click the "Accessibility" button to reveal these options and customize your browsing experience to suit your needs.
        </p>

        <h2>Image Gallery</h2>
        <div class="image-gallery">
            <img src="Pictures/pic1.png" alt="Individual using a blood pressure monitor on their arm" onerror="handleImageError(this)">
            <img src="Pictures/pic2.png" alt="Syringe inserted into an insulin vial" onerror="handleImageError(this)">
            <img src="Pictures/pic3.png" alt="Healthy breakfast of fruits, oats and orange juice" onerror="handleImageError(this)">
            <img src="Pictures/pic4.png" alt="Someone using a blood extractor for a glucose test machine" onerror="handleImageError(this)">
            <img src="Pictures/pic5.png" alt="Someone running down a leafy autumn pathway" onerror="handleImageError(this)">
        </div>
    </div>
    <script src="js/accessibility.js"></script>
</body>
</html>