<?php
// Start session and include DB config
session_start();
include_once 'includes/config.php';

// Redirect to login if user is not logged in
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account - Diabetes Wellness Assistant</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/accessibility.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/accessibility_widget.php'; ?>
    <div class="container">
        <h1>Your Account</h1>
        <ul>
            <li><a href="patient_data.php">Patient Data</a></li>
            <li><a href="includes/logout.php">Log Out</a></li>
        </ul>
    </div>
    <script src="js/accessibility.js"></script>
</body>
</html>

