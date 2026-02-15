<?php
session_start();
require 'db.php';

$user = $_SESSION['user'] ?? null;

// Fetch doctor-wise queue data
$sql = "
SELECT 
    a.doctor_name,
    a.department,
    MAX(q.token_number) AS last_token,
    COUNT(q.id) AS total_queue
FROM appointments a
JOIN queue q ON a.id = q.appointment_id
GROUP BY a.doctor_name, a.department
ORDER BY a.department
";
$result = $conn->query($sql);
$queues = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Queue Monitor - MedQueue</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen text-gray-800 font-sans">

  <!-- Header -->
  <header class="bg-blue-600 text-white p-4 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img src="images/d1.png" class="w-8 h-8" alt="Logo" />
        <h1 class="text-xl font-bold">MedQueue</h1>
      </div>

      <nav class="flex items-center space-x-6 text-sm md:text-base relative">
        <a href="index.php" class="hover:underline">Home</a>
        <a href="doctor.php" class="hover:underline">Doctors</a>
        <a href="about.php" class="hover:underline">About</a>

        <?php if ($user): ?>
          <div class="relative group">
            <button class="hover:underline focus:outline-none">
              <?= htmlspecialchars($user['name'] ?? $user['email'] ?? 'Account') ?> ▾
            </button>
            <div class="absolute right-0 mt-2 bg-white text-black rounded shadow hidden group-hover:block z-50 w-40">
              <a href="<?= $user['role'] === 'admin' ? 'Backend/admin/dashboard.php' : 'dashboard.php' ?>" class="block px-4 py-2 hover:bg-gray-100">Dashboard</a>
              <a href="logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php" class="hover:underline">Login</a>
          <a href="register.php" class="hover:underline">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <section class="py-12 px-6">
    <h2 class="text-3xl font-bold text-center text-blue-800 mb-8 drop-shadow">📊 Current Token Queue Status</h2>

    <?php if (count($queues) > 0): ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
        <?php foreach ($queues as $q): ?>
          <div class="bg-white shadow-md p-6 rounded-2xl border border-blue-100 hover:shadow-xl transition transform hover:scale-105">
            <h3 class="text-xl font-semibold text-blue-800 mb-2"><?= htmlspecialchars($q['doctor_name']) ?></h3>
            <p class="text-gray-600 mb-1">🧪 Department: <strong><?= htmlspecialchars($q['department']) ?></strong></p>
            <p class="text-green-700 font-bold">✅ Current Token: <?= $q['last_token'] ?></p>
            <p class="text-gray-700">👥 Total Appointments: <?= $q['total_queue'] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-600">No appointments or queues available.</p>
    <?php endif; ?>
  </section>

  <!-- Footer -->
  <footer class="bg-white border-t mt-12 py-4 text-center text-sm text-gray-600">
    &copy; <?= date('Y') ?> MedQueue. All rights reserved.
  </footer>

</body>
</html>
