<?php
session_start();
require 'db.php';

$maxQueueSize = 5;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointmentId = $_POST["appointment_id"];

    // Get current count
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM queue WHERE appointment_id = ?");
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $currentCount = $result['count'];

    // Circular logic: if full, remove first
    if ($currentCount >= $maxQueueSize) {
        $conn->query("DELETE FROM queue WHERE appointment_id = $appointmentId ORDER BY id ASC LIMIT 1");
    }

    // Add new token
    $token = rand(100, 999);
    $stmt = $conn->prepare("INSERT INTO queue (appointment_id, token_number) VALUES (?, ?)");
    $stmt->bind_param("ii", $appointmentId, $token);
    $stmt->execute();

    echo "Token #$token added to the queue.";
}
?>
