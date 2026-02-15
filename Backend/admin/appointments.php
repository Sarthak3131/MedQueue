<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Appointments - MedQueue</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
  <h1 class="text-2xl font-bold mb-4 text-blue-800">Appointments Page (Under Construction)</h1>
  <a href="dashboard.php" class="text-blue-600 underline">← Back to Admin Dashboard</a>
</body>
</html>
