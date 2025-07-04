<?php
require_once 'includes/config.php';

// Get current step from URL parameter
$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$progress_width = ($current_step / 3) * 100;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['patient_login'])) {
        // Step 1: Patient Login
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        
        // First get the user's hashed password
        $sql = "SELECT id, username, pwd FROM patients WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $patient = $result->fetch_assoc();
            // Verify the password
            if (password_verify($pwd, $patient['pwd'])) {
                $_SESSION['patient_id'] = $patient['id'];
                $_SESSION['patient_username'] = $patient['username'];
                header("Location: grant_access.php?step=2");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Username not found";
        }
    } elseif (isset($_POST['select_caregiver'])) {
        // Step 2: Select Caregiver
        $username = trim($_POST['caregiver_username']);

        // Validate input
        if (empty($username)) {
            $error = "Caregiver username is required";
        } else {
            // Check if caregiver exists by username only
            $stmt = $conn->prepare("SELECT id, firstname, lastname FROM care_givers WHERE username = ?");
            if (!$stmt) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $_SESSION['caregiver_id'] = $row['id'];
                    $_SESSION['caregiver_name'] = $row['firstname'] . ' ' . $row['lastname'];
                    header("Location: grant_access.php?step=3");
                    exit();
                } else {
                    $error = "Caregiver not found. Please check the username and try again.";
                }
                $stmt->close();
            }
        }
    } elseif (isset($_POST['setup_access'])) {
        // Step 3: Setup Access Permissions
        if (!isset($_SESSION['patient_id']) || !isset($_SESSION['caregiver_id'])) {
            $error = "Session data missing. Please start over.";
        } else {
            $patient_id = $_SESSION['patient_id'];
            $caregiver_id = $_SESSION['caregiver_id'];
            $access_glucose = isset($_POST['access_glucose']) ? 1 : 0;
            $access_pressure = isset($_POST['access_pressure']) ? 1 : 0;
            $access_cholesterol = isset($_POST['access_cholesterol']) ? 1 : 0;
            
            // Check if access already exists
            $check_sql = "SELECT * FROM grant_access WHERE caregiver_id = ? AND patient_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $caregiver_id, $patient_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $error = "Access already granted for this caregiver";
            } else {
                $sql = "INSERT INTO grant_access (caregiver_id, patient_id, access_glucose, access_pressure, access_cholesterol) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiiii", $caregiver_id, $patient_id, $access_glucose, $access_pressure, $access_cholesterol);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Access granted successfully";
                    header("Location: data_access.php");
                    exit();
                } else {
                    $error = "Error granting access: " . $conn->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grant Caregiver Access</title>
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
            max-width: 500px;
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
        .checkbox-group {
            margin: 10px 0;
        }
        .checkbox-group label {
            display: inline;
            margin-left: 5px;
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
    <h3>Grant Access to Caregiver</h3>
    <div class="progress">
        <div class="progress-bar" style="--progress-width: <?php echo $progress_width; ?>%"></div>
    </div>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($current_step === 1): ?>
        <!-- Step 1: Patient Login -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="pwd">Password</label>
                <input type="password" id="pwd" name="pwd" required>
            </div>
            <button type="submit" name="patient_login">Login</button>
        </form>
    <?php elseif ($current_step === 2): ?>
        <!-- Step 2: Select Caregiver -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="caregiver_username">Caregiver Username</label>
                <input type="text" id="caregiver_username" name="caregiver_username" required>
            </div>
            <button type="submit" name="select_caregiver">Next</button>
        </form>
    <?php elseif ($current_step === 3): ?>
        <!-- Step 3: Setup Access Permissions -->
        <form method="POST" action="">
            <div class="form-group">
                <label>Select Access Permissions</label>
                <div class="checkbox-group">
                    <input type="checkbox" name="access_glucose" id="access_glucose">
                    <label for="access_glucose">Glucose Access</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="access_pressure" id="access_pressure">
                    <label for="access_pressure">Pressure Access</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="access_cholesterol" id="access_cholesterol">
                    <label for="access_cholesterol">Cholesterol Access</label>
                </div>
            </div>
            <button type="submit" name="setup_access">Grant Access</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>