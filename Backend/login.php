<?php
session_start();
require_once '../db.php';

// Debug line: remove in production
file_put_contents('../request_debug.txt', "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "❌ Invalid request (method was: " . $_SERVER["REQUEST_METHOD"] . ")";
    header("Location: ../login.php");
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];
$role = $_POST['role'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
$stmt->bind_param("ss", $email, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        if ($role === 'admin') {
            header("Location: ../Backend/admin/dashboard.php");
        } else {
            header("Location: ../dashboard.php");
        }
        exit;
    } else {
        $_SESSION['error'] = "❌ Incorrect password.";
    }
} else {
    $_SESSION['error'] = "❌ User not found or role mismatch.";
}

header("Location: ../login.php");
exit;
