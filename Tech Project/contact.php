<?php
// Include DB config and encryption utilities
require_once 'includes/config.php';
require_once 'includes/encryption.php';
require_once 'includes/encryption_config.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// If patient, get emergency contact info
if ($_SESSION['acctype'] == 'patient') {
    $patient_id = $_SESSION['patient_id'];
    $sql = "SELECT firstname, lastname, em_name, em_phone, em_address FROM patients WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient_data = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Medical Contacts</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="style.css">
  <style>

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.main{

  margin: 15px;
}

    h2 {
      margin-top: 40px;
    }

    .contact-card {
      border: 1px solid #ccc;
      padding: 15px;
      border-radius: 5px;
      margin: 20px 0px;
    }

    .contact-buttons button {
      margin-right: 10px;
      margin-top: 10px;
      padding: 8px 12px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }

    .contact-buttons button:hover {
      background-color: #a78300;
    }

.contact_navbar {
  overflow: hidden;
  background-color: #a78300;
  color: #000;
  position: fixed;
  top: 0;
  width: 100%;
  display: flex;
  justify-content: center;
}

.contact_navbar a {
  display: inline-block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

.contact_navbar a:hover {
  background: #000;
  color: #a78300;
}


.main {
  margin-top: 50px;
}



.contact-nav {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin: 20px 0;
  padding: 10px;
  background-color: #f8f9fa;
  border-radius: 5px;
}

.contact-nav a {
  color: white;
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 4px;
  background-color: #4CAF50;
  transition: background-color 0.3s;
}

.contact-nav a:hover {
  background-color: #45a049;
}

.contact_navbar {
  display: none;
}

.main {
  margin-top: 20px;
}

  </style>

</head>

<body>

<?php // Main navigation bar ?>
<?php include 'includes/navbar.php'; ?>

<?php // Show welcome if logged in ?>
<?php
if (isset($_SESSION['username'])) {
    echo "<h2>Welcome " . $_SESSION['username'] . "!</h2>";
} 
?>

    <h2>Medical Contacts</h2>

  <div class="contact-nav">
    <a href="#emergency">Emergency Services</a>
    <a href="#em_contact">Your Emergency Contact</a>
    <a href="#hospitals">Major Hospitals</a>
    <a href="#aid">Diabetes Aid</a>
  </div>
  


    <h2 id="emergency">Emergency Services</h2>

  <div class="contact-card">
    <p><strong>Ambulance/Fire Service</strong><br><br>
    Dial: 911</p>
    <div class="contact-buttons">
      <button onclick="call('+911')">Call</button>
      <button onclick="sms('+911')">Message</button>
    </div>
  </div>

  <div class="contact-card">
    <p><strong>Police</strong><br><br>
    Dial: 999</p>
    <div class="contact-buttons">
      <button onclick="call('+999')">Call</button>
      <button onclick="sms('+999')">Message</button>
    </div>
  </div>

  <h2 id="em_contact">Your Emergency Contact</h2>

  <div class="main">
    <?php if ($patient_data): ?>
    <div class="emergency-contact">
      <p><strong>Name:</strong> <?php echo htmlspecialchars($patient_data['em_name']); ?></p>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient_data['em_phone']); ?></p>
      <p><strong>Address:</strong> <?php echo htmlspecialchars($patient_data['em_address']); ?></p>
      <div class="contact-buttons">
        <button onclick="call('<?php echo htmlspecialchars($patient_data['em_phone']); ?>')">Call Emergency Contact</button>
        <button onclick="sms('<?php echo htmlspecialchars($patient_data['em_phone']); ?>')">Message Emergency Contact</button>
      </div>
    </div>
    <?php endif; ?>
  <h2 id="hospitals">Major Hospitals</h2>


  <div class="contact-card">
    <p><strong>Owen King Europena Union (OKEU) Hospital-Millennium Highway, Castries</strong><br><br>
    Phone: +1 (758) 458-6500 <br>
Email: information@mhmc.lc<br>
Website: <a target="_blank" href="https://millenniumheights.org/owen-king-eu-hospital/">Millenniumheights.org</a>

    </p>

    <div class="contact-buttons">
      <button onclick="call('+17584522421')">Call</button>
      <button onclick="sms('+17584522421')">Message</button>
      <a href = "mailto:information@mhmc.lc"><button>Email</button></a>
      <a target="_blank" href = "https://millenniumheights.org/owen-king-eu-hospital/"><button>Visit Website</button></a>
    </div>
  </div>


  <div class="contact-card">
    <p><strong>Victoria Hospital-Hospital Road, Castries</strong><br><br>
    Phone: +1 (758) 452-2421</p>
    <div class="contact-buttons">
      <button onclick="call('+17584522421')">Call</button>
      <button onclick="sms('+17584522421')">Message</button>
    </div>
  </div>

  <div class="contact-card">
    <p><strong>St. Jude Hospital-St. Jude Highway, Vieux Fort</strong><br><br>
    Phone: +1 (758) 459-6700 or +1 (758) 454-6041
    Email: sjh@stjudehospitalslu.org<br>
    Website: <a target="_blank" href="https://www.stjudehospitalslu.org/l/">Stjudehospitalslu.org</a>
    </p>
    <div class="contact-buttons">
      <button onclick="call('+17584596700')">Call</button>
      <button onclick="sms('+17584596700')">Message</button>
      <button onclick="call('+17584546041')">Call Alternative</button>
      <button onclick="sms('+17584546041')">Message Alternative</button>
      <a href = "mailto:sjh@stjudehospitalslu.org"><button>Email</button></a>
      <a target="_blank" href = "https://www.stjudehospitalslu.org/l/"><button>Visit Website</button></a>
    </div>
  </div>

  <h2 id="aid">Diabetes Aid</h2>

  <div class="contact-card">
    <strong>SL Diabetes & Hypertension Association</strong><br><br>
    Phone: +1 (758) 452-6933<br>
    Mobile: +1 (758) 286-2024
    <div class="contact-buttons">
      <button onclick="call('+17584526933')">Call Office</button>
      <button onclick="sms('+17584526933')">Message Office</button>
      <button onclick="call('+17582862024')">Call Mobile</button>
      <button onclick="sms('+17582862024')">Message Mobile</button>
    </div>
  </div>
</div>

<script>
  // Call and SMS helper functions
  function isMobileDevice() {
    return /android|iphone|ipad/i.test(navigator.userAgent);
  }

  function call(number) {
    if (isMobileDevice()) {
      window.location.href = `tel:${number}`;
    } else {
      alert(`Please call ${number} using your phone.`);
    }
  }

  function sms(number) {
    if (isMobileDevice()) {
      window.location.href = `sms:${number}?body=I need help urgently.`;
    } else {
      alert(`SMS is available only on mobile devices. Please text ${number}.`);
    }
  }
</script>

</body>
</html>
