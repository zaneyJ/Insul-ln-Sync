<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav id="navbar">
    <div class="logo">
        <a href="index1.php">Insul-In-Sync</a>
    </div>
    <ul>
        <li><a href="reminders.php">Reminders</a></li>
        <li><a href="about_us.php">About Us</a></li>
        <li><a href="education.php">Education</a></li>
        <li><a href="contact.php">Contact Medical Services</a></li>

        <?php
        if (isset($_SESSION['username'])) {
            echo <<<HTML
            <li class="dropdown">
                <a href="#">Tools</a>
                <div class="dropdown-content">
                    <a href="pressure.php">Blood Pressure Tracker</a>
                    <a href="glucose.php">Blood Glucose Tracker</a>
                    <a href="cholesterol.php">Blood Cholesterol Tracker</a>
                    <a href="diet_tracker.php">Diet Tracker</a>
                    <a href="notepad.php">Notepad</a>
                    <a href="exercise.php">Exercise Planner</a>
                </div>
            </li> 

            <li class="dropdown">
                <a href="#">Account</a>
                <div class="dropdown-content">
            HTML;
            
            if ($_SESSION['acctype'] == 'patient') {
                echo '<a href="data_access.php">Data Access</a>';
            }

            if ($_SESSION['acctype'] == 'care_giver') {
                echo '<a href="care_giver_access.php">Patient Data</a>';
            }

            echo <<<HTML
                    <a href="edit_access.php">Edit Data Access</a>
                    <a href="patient_data.php">Patient Data</a>
                </div>
            </li>
            <li><a href="includes/logout.php">Log Out</a></li>
            HTML;
        }
        ?>

        <?php
        if (!isset($_SESSION['username'])) {
            echo <<<HTML
            <li><a href="login.php">Log In</a></li>
            <li><a href="signup.php">Sign Up</a></li>
            HTML;
        }
        ?>
    </ul>
</nav> 