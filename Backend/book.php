<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $doctor_name = $_POST['doctor_name'];
    $department = $_POST['department'];
    $appointment_date = $_POST['appointment_date'];

    // Insert into appointments table
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_name, department, appointment_date, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $user['id'], $doctor_name, $department, $appointment_date);
    $stmt->execute();

    // Get inserted appointment_id
    $appointment_id = $conn->insert_id;

    // Get current queue count for that department on that date
    $token_query = $conn->prepare("
        SELECT COUNT(*) AS total FROM appointments 
        WHERE department = ? AND appointment_date = ?
    ");
    $token_query->bind_param("ss", $department, $appointment_date);
    $token_query->execute();
    $token_result = $token_query->get_result()->fetch_assoc();
    $token_number = $token_result['total'] + 1;
    $token_query->close();

    // Step 3: Insert into queue table
    $queue_stmt = $conn->prepare("INSERT INTO queue (appointment_id, token_number, created_at) VALUES (?, ?, NOW())");
    $queue_stmt->bind_param("ii", $appointment_id, $token_number);
    $queue_stmt->execute();
    $queue_stmt->close();

    // ✅ Redirect back with success
    $_SESSION['success'] = "Appointment booked! Your token number is: $token_number. Payment of ₹$fee is pending.";
    header("Location: ../dashboard.php");
    exit;
} else {
    header("Location: ../doctor.php");
    exit;
}
