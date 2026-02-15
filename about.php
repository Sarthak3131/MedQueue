<?php
session_start();
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About - MedQueue</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="images/d1.png" />
</head>
<body class="bg-gradient-to-b from-blue-50 via-white to-blue-100 min-h-screen text-gray-800 font-sans">

  <!-- Navbar -->
  <header class="bg-blue-600 p-4 text-white shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img src="images/d1.png" class="w-8 h-8" alt="Logo" />
        <h1 class="text-xl font-bold">MedQueue</h1>
      </div>
      <nav class="space-x-4 text-sm md:text-base">
        <a href="index.php" class="hover:underline">Home</a>
        <a href="about.php" class="underline">About</a>
        <?php if ($user): ?>
          <a href="<?= $user['role'] === 'admin' ? 'Backend/admin/dashboard.php' : 'dashboard.php' ?>" class="hover:underline">Dashboard</a>
          <a href="logout.php" class="hover:underline">Logout</a>
        <?php else: ?>
          <a href="login.php" class="hover:underline">Login</a>
          <a href="register.php" class="hover:underline">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-5xl mx-auto px-6 py-12">
    <h2 class="text-3xl font-bold text-blue-900 mb-4">Why MedQueue?</h2>
    <p class="text-gray-700 text-lg mb-6">
      MedQueue is a hospital queue management system designed to minimize patient wait times, reduce congestion, and streamline the appointment process.
      Our goal is to simplify both patient visits and admin operations using technology-driven solutions.
    </p>

    <img src="images/what-is-a-queue-management-system-for-hospitals.jpg" alt="Hospital Queue Illustration"
         class="rounded-xl shadow-md mx-auto mb-10 w-full md:w-3/4" />

    <h3 class="text-2xl font-semibold text-blue-800 mt-10 mb-3">✨ Features</h3>
    <ul class="list-disc list-inside text-gray-700 text-base leading-relaxed">
      <li>Online appointment booking with digital token generation</li>
      <li>Role-based login system for users and admins</li>
      <li>Walk-in patient registration by admins</li>
      <li>Real-time queue monitoring</li>
      <li>Efficient patient flow management using circular queues</li>
    </ul>

    <h3 class="text-2xl font-semibold text-blue-800 mt-10 mb-3">🌟 Our Vision</h3>
    <p class="text-gray-700 text-lg leading-relaxed">
      To revolutionize the healthcare queue system through digital transformation and deliver a seamless experience to both patients and healthcare providers.
    </p>
  </main>

  <!-- Footer -->
  <footer class="bg-white border-t mt-8 py-4 text-center text-sm text-gray-600">
    &copy; <?= date('Y') ?> MedQueue. All rights reserved.
  </footer>

</body>
</html>
