<?php
require_once 'includes/config.php';
require_once 'includes/encryption.php';
require_once 'includes/encryption_config.php';

// Get current step from URL parameter
$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$progress_width = ($current_step / 2) * 100;

// Handle caregiver login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['caregiver_login'])) {
    $username = $_POST['username'];
    $pwd = $_POST['pwd'];
    
    // First get the caregiver's hashed password
    $sql = "SELECT id, username, pwd, firstname, lastname FROM care_givers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $caregiver = $result->fetch_assoc();
        // Verify the password
        if (password_verify($pwd, $caregiver['pwd'])) {
            $_SESSION['caregiver_id'] = $caregiver['id'];
            $_SESSION['caregiver_name'] = $caregiver['firstname'] . ' ' . $caregiver['lastname'];
            header("Location: care_giver_access.php?step=2");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Username not found";
    }
}

// Check if caregiver is logged in
if (!isset($_SESSION['caregiver_id']) || !isset($_SESSION['caregiver_name'])) {
    // If not logged in and trying to access step 2, redirect to login
    if ($current_step === 2) {
        header("Location: care_giver_access.php?step=1");
        exit();
    }
}

// Get patient data if caregiver is logged in
$patients = [];
if (isset($_SESSION['caregiver_id'])) {
    // Modified query to only get patients where caregiver has at least one type of access
    $sql = "SELECT p.*, ga.access_glucose, ga.access_pressure, ga.access_cholesterol 
            FROM patients p 
            JOIN grant_access ga ON p.id = ga.patient_id 
            WHERE ga.caregiver_id = ? 
            AND (ga.access_glucose = 1 OR ga.access_pressure = 1 OR ga.access_cholesterol = 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['caregiver_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Decrypt patient name
        $row['firstname'] = decryptData($row['firstname']);
        $row['lastname'] = decryptData($row['lastname']);
        $patients[] = $row;
    }
}

// Get specific patient data if requested
$patient_data = null;
if (isset($_GET['patient_id']) && isset($_SESSION['caregiver_id'])) {
    $patient_id = $_GET['patient_id'];
    
    // Verify caregiver has access to this patient
    $check_sql = "SELECT * FROM grant_access WHERE caregiver_id = ? AND patient_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $_SESSION['caregiver_id'], $patient_id);
    $check_stmt->execute();
    $access = $check_stmt->get_result()->fetch_assoc();
    
    if ($access) {
        // Get patient info
        $patient_sql = "SELECT * FROM patients WHERE id = ?";
        $patient_stmt = $conn->prepare($patient_sql);
        $patient_stmt->bind_param("i", $patient_id);
        $patient_stmt->execute();
        $patient_data = $patient_stmt->get_result()->fetch_assoc();
        
        // Get glucose data if access granted
        if ($access['access_glucose']) {
            $glucose_sql = "SELECT * FROM patient_glucose WHERE patient_id = ? ORDER BY time DESC LIMIT 10";
            $glucose_stmt = $conn->prepare($glucose_sql);
            $glucose_stmt->bind_param("i", $patient_id);
            $glucose_stmt->execute();
            $glucose_data = $glucose_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get pressure data if access granted
        if ($access['access_pressure']) {
            $pressure_sql = "SELECT * FROM patient_pressure WHERE patient_id = ? ORDER BY time DESC LIMIT 10";
            $pressure_stmt = $conn->prepare($pressure_sql);
            $pressure_stmt->bind_param("i", $patient_id);
            $pressure_stmt->execute();
            $pressure_data = $pressure_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get cholesterol data if access granted
        if ($access['access_cholesterol']) {
            $cholesterol_sql = "SELECT * FROM patient_cholesterol WHERE patient_id = ? ORDER BY time DESC LIMIT 10";
            $cholesterol_stmt = $conn->prepare($cholesterol_sql);
            $cholesterol_stmt->bind_param("i", $patient_id);
            $cholesterol_stmt->execute();
            $cholesterol_data = $cholesterol_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Access Portal</title>
    <style>
        body {
            background: #f7f9fa;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #4CAF50;
            padding: 16px 0;
            text-align: center;
            margin-bottom: 0;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 18px;
            font-weight: 500;
            font-size: 1.1em;
            transition: color 0.2s;
        }
        .navbar a:hover {
            color: #e0e0e0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px;
        }
        h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 18px;
        }
        .progress {
            height: 16px;
            background: #e0e0e0;
            border-radius: 8px;
            margin: 24px 0;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: #4CAF50;
            width: var(--progress-width);
            transition: width 0.4s;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #444;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }
        button:hover {
            background: #388e3c;
        }
        .error {
            color: #b71c1c;
            background: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
        .patient-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        @media (min-width: 700px) {
            .patient-list {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .patient-card {
            border: 1px solid #ddd;
            padding: 18px 15px;
            border-radius: 8px;
            background: #fafbfc;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .patient-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 5px 15px rgba(0,0,0,0.10);
        }
        .patient-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #388e3c;
        }
        .patient-card ul {
            padding-left: 18px;
            margin-bottom: 10px;
        }
        .patient-card button {
            width: auto;
            padding: 8px 18px;
            font-size: 1em;
            margin-top: 8px;
        }
        .no-patients {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="reminders.php">Reminders</a>
    <a href="about.php">About Us</a>
    <a href="education.php">Education</a>
    <a href="contact.php">Contact Medical Services</a>
    <a href="tools.php">Tools</a>
    <a href="account.php">Account</a>
    <a href="logout.php">Log Out</a>
</div>
<?php
if (isset($_SESSION['username'])) {
    echo "<h2>Welcome " . htmlspecialchars($_SESSION['username']) . "!</h2>";
}
?>
<div class="container">
    <h2>Caregiver Access Portal</h2>
    <div class="progress">
        <div class="progress-bar" style="--progress-width: <?php echo $progress_width; ?>%"></div>
    </div>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($current_step === 1): ?>
        <!-- Step 1: Caregiver Login -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="pwd">Password</label>
                <input type="password" id="pwd" name="pwd" required>
            </div>
            <button type="submit" name="caregiver_login">Login</button>
        </form>
    <?php elseif ($current_step === 2 && isset($_SESSION['caregiver_name'])): ?>
        <!-- Step 2: Patient List -->
        <h3>Welcome, <?php echo htmlspecialchars($_SESSION['caregiver_name']); ?></h3>
        <?php if (empty($patients)): ?>
            <div class="no-patients">
                <h3>No Patients Available</h3>
                <p>You don't have access to any patient data at this time.</p>
            </div>
        <?php else: ?>
            <div class="patient-list">
                <?php foreach ($patients as $patient): ?>
                    <div class="patient-card">
                        <h3><?php echo htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?></h3>
                        <p>Access to:</p>
                        <ul>
                            <?php if ($patient['access_glucose']): ?>
                                <li>Glucose Readings</li>
                            <?php endif; ?>
                            <?php if ($patient['access_pressure']): ?>
                                <li>Blood Pressure Readings</li>
                            <?php endif; ?>
                            <?php if ($patient['access_cholesterol']): ?>
                                <li>Cholesterol Readings</li>
                            <?php endif; ?>
                        </ul>
                        <button onclick="window.location.href='patient_data.php?patient_id=<?php echo $patient['id']; ?>'">
                            View Patient Data
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html> 