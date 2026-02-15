<?php
session_start();
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome to My Hospital</title>
  <link href="/Htmlfiles/src/output.css" rel="stylesheet">
</head>
<body class="bg-blue-50">

  <!-- Navbar -->
  <?php if ($user): ?>
<a href="dashboard.php">Dashboard</a>
<a href="logout.php">Logout</a>
<?php else: ?>
<a href="login.php">Login</a>
<a href="register.php">Register</a>
<?php endif; ?>

  <header class="bg-blue-200 shadow-md py-4 px-8 flex justify-between items-center">
    <div class="text-2xl font-bold text-blue-800 flex items-center gap-2">
      <svg class="w-6 h-6 text-blue-800" fill="currentColor" viewBox="0 0 20 20"><path d="M10 20a2 2 0 01-2-2v-6H6a2 2 0 01-2-2V8a2 2 0 012-2h2V2a2 2 0 114 0v4h2a2 2 0 012 2v2a2 2 0 01-2 2h-2v6a2 2 0 01-2 2z" /></svg>
      Medi
    </div>
    <nav class="space-x-6 text-blue-900 font-medium">
      <a href="#" class="hover:underline">Home</a>
      <a href="/Htmlfiles/doctor.html" class="hover:underline">Doctor</a>
      <a href="/Htmlfiles/about.html" class="hover:underline">About</a>
    </nav>
  </header>

  <!-- Hero Section -->
  <section class="relative bg-blue-100 text-center py-12 px-6 md:px-20 bg-[url('/Htmlfiles/images/background.png')] bg-cover ">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-10">
      
      <!-- Text -->
      <div class="md:w-1/2">
        <h1 class="text-4xl md:text-5xl font-bold text-blue-800 mb-4">Welcome to Our Hospital Queue System</h1>
        <p class="text-lg text-gray-700 mb-4">
          Fast. Digital. Patient-friendly. No more long lines — just smart healthcare.
        </p>

        <!-- 👇 Added Welcome Text -->
        <p class="text-lg font-semibold text-blue-900 mb-6" id="welcomeText">
          Hello, welcome to MedQueue! Please register or login to continue.
        </p>

        <div class="flex gap-4 justify-center md:justify-start">
          <a href="register.html" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">Register</a>
          <a href="login.html" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">Login</a>
        </div>
      </div>

      <!-- Image -->
      <div class="md:w-1/2">
        <img src="/Htmlfiles/images/doctors.jpg" alt="Doctors" class="w-full max-w-md mx-auto rounded-xl shadow-lg" />
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-12 text-center bg-white">
    <h2 class="text-3xl font-bold text-blue-800 mb-6">Key Features of our Hospital</h2>
    <p class="text-gray-600 max-w-2xl mx-auto mb-4">
      Efficient digital queue management, 24/7 doctor access, real-time updates, and easy appointment scheduling.
    </p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
      <div class="bg-gray-200 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-xl font-semibold text-blue-800">Digital Queue Management</h3>
        <p class="text-gray-600">Manage your appointments seamlessly with our digital system.</p>
      </div>
      <div class="bg-gray-200 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-xl font-semibold text-blue-800">24/7 Doctor Access</h3>
        <p class="text-gray-600">Consult with our doctors anytime, anywhere.</p>
      </div>
      <div class="bg-gray-200 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
        <h3 class="text-xl font-semibold text-blue-800">Real-Time Updates</h3>
        <p class="text-gray-600">Stay informed with real-time notifications about your appointments.</p>
      </div>
    </div>
  </section>

  <!-- 👇 Voice Greeting Script -->
  <script>
    window.onload = function () {
      const message = new SpeechSynthesisUtterance("Hello, welcome to MedQueue! Please do your Appointment.");
      message.lang = "en-US";
      message.rate = 1;
      speechSynthesis.speak(message);
    };
  </script>

</body>
</html>
