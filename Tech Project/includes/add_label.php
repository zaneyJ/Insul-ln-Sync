<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false];

if (isset($data['name']) && isset($data['color'])) {
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $data['name']);
    $color = mysqli_real_escape_string($conn, $data['color']);

    $sql = "INSERT INTO event_labels (user_id, name, color) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $name, $color);

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['id'] = mysqli_insert_id($conn);
    }
}

header('Content-Type: application/json');
echo json_encode($response); 