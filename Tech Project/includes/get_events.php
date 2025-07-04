<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'events' => []];

if (isset($_GET['date'])) {
    // Get events for a specific date
    $date = $_GET['date'];
    $sql = "SELECT e.*, l.color 
            FROM calendar_events e 
            LEFT JOIN event_labels l ON e.label_id = l.id 
            WHERE e.user_id = ? AND DATE(e.start_datetime) = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $date);
} else if (isset($_GET['year']) && isset($_GET['month'])) {
    // Get events for a specific month
    $year = $_GET['year'];
    $month = $_GET['month'];
    $sql = "SELECT e.*, l.color 
            FROM calendar_events e 
            LEFT JOIN event_labels l ON e.label_id = l.id 
            WHERE e.user_id = ? AND YEAR(e.start_datetime) = ? AND MONTH(e.start_datetime) = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $user_id, $year, $month);
} else {
    http_response_code(400);
    exit('Invalid request');
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

$response['success'] = true;
$response['events'] = $events;

header('Content-Type: application/json');
echo json_encode($response); 