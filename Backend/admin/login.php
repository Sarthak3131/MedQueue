<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded for now, can use DB later
    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid login credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100 flex items-center justify-center h-screen">
  <form method="POST" class="bg-white p-6 rounded shadow w-96">
    <h2 class="text-2xl font-bold mb-4 text-blue-800">🔐 Admin Login</h2>
    <?php if (!empty($error)) echo "<p class='text-red-600'>$error</p>"; ?>
    <input type="text" name="username" placeholder="Username" class="w-full mb-4 p-2 border rounded" required>
    <input type="password" name="password" placeholder="Password" class="w-full mb-4 p-2 border rounded" required>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Login</button>
  </form>
</body>
</html>
