<?php
session_start();
$user = $_SESSION['user'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF']);

function isActive($page) {
  global $currentPage;
  return $currentPage === $page ? 'underline' : 'hover:underline';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to MedQueue</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="images/d1.png" />
</head>
<body class="bg-gradient-to-b from-blue-50 via-white to-blue-100 min-h-screen text-gray-800 font-sans">

  <!-- Navbar -->
  <header class="bg-blue-600 text-white p-4 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img src="images/d1.png" class="w-8 h-8" alt="Logo" />
        <h1 class="text-xl font-bold">MedQueue</h1>
      </div>

      <nav class="flex items-center space-x-6 text-sm md:text-base relative">
        <a href="index.php" class="<?= isActive('index.php') ?>">Home</a>
        <a href="doctor.php" class="<?= isActive('doctor.php') ?>">Doctors</a>
        <a href="about.php" class="<?= isActive('about.php') ?>">About</a>
        <a href="feedback.php" class="<?= isActive('feedback.php') ?>">Feedback</a>

        <?php if ($user): ?>
          <div class="relative group">
            <button class="hover:underline focus:outline-none">
              <?= htmlspecialchars($user['name'] ?? $user['email'] ?? 'User') ?> ▾
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

  <!-- Hero -->
  <main>
    <section class="text-center py-20 px-6 bg-gradient-to-r from-blue-100 to-white">
      <div class="max-w-5xl mx-auto">
        <h2 class="text-4xl md:text-5xl font-extrabold text-blue-800 mb-4 drop-shadow">Digital Queue System for Hospitals</h2>
        <p class="text-gray-700 text-lg md:text-xl mb-6 leading-relaxed">
          MedQueue is your one-stop solution for avoiding long waiting lines. Book appointments, manage walk-ins, and get your token instantly.
        </p>
        <a href="<?= $user ? ($user['role'] === 'admin' ? 'Backend/admin/dashboard.php' : 'dashboard.php') : 'login.php' ?>"
           class="inline-block bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-700 hover:to-blue-500 text-white border border-white font-semibold px-6 py-3 rounded-lg shadow-md hover:shadow-xl transition duration-300">
           <?= $user ? 'Go to Dashboard' : 'Get Started' ?>
        </a>
      </div>
    </section>

    <!-- Features -->
    <section class="bg-white py-16 px-6">
      <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8 text-center">
        <div class="bg-blue-50 p-6 border border-blue-100 rounded-2xl shadow hover:shadow-lg hover:scale-105 transition">
          <h3 class="text-xl font-bold text-blue-800 mb-2">🚀 Fast Token Booking</h3>
          <p class="text-gray-700">Book your token online in seconds and avoid queues.</p>
        </div>
        <div class="bg-blue-50 p-6 border border-blue-100 rounded-2xl shadow hover:shadow-lg hover:scale-105 transition">
          <h3 class="text-xl font-bold text-blue-800 mb-2">📋 Admin Walk-ins</h3>
          <p class="text-gray-700">Admins can register walk-in patients and manage queues in real-time.</p>
        </div>
        <div class="bg-blue-50 p-6 border border-blue-100 rounded-2xl shadow hover:shadow-lg hover:scale-105 transition">
          <h3 class="text-xl font-bold text-blue-800 mb-2">🔒 Secure Login</h3>
          <p class="text-gray-700">Role-based login system for users and hospital admins.</p>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-white border-t mt-8 py-4 text-center text-sm text-gray-600">
    &copy; <?= date(format: 'Y') ?> MedQueue. All rights reserved.
  </footer>

<div id="chatbot-container" class="fixed bottom-24 right-6 z-50">

  <div id="chat-window" class="hidden flex flex-col bg-white w-80 h-[460px] rounded-2xl shadow-2xl border border-blue-200 overflow-hidden animate-slide-up">

    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 font-semibold text-sm flex justify-between items-center">
      <span>🤖 MedBot - Your AI Assistant</span>
      <button onclick="document.getElementById('chat-window').classList.add('hidden')" class="hover:text-red-200">✖</button>
    </div>

    <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-blue-50 text-sm scroll-smooth">
      <div class="text-center text-gray-500 italic">How can I help you today?</div>
    </div>

    <form id="chat-form" class="flex items-center border-t border-gray-300 bg-white">
      <input type="text" id="user-input" placeholder="Ask a question..." class="flex-1 p-3 text-sm outline-none" />
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">Send</button>
    </form>
  </div>

  <button id="chat-toggle" class="bg-gradient-to-br from-blue-600 to-blue-800 hover:from-blue-800 hover:to-blue-600 text-white rounded-full p-4 shadow-xl transition-transform duration-300 hover:scale-110">
    💬
  </button>
</div>

<style>
  @keyframes slide-up {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  .animate-slide-up {
    animation: slide-up 0.3s ease-out;
  }
</style>

<script>
  const chatToggle = document.getElementById('chat-toggle');
  const chatWindow = document.getElementById('chat-window');
  const chatForm = document.getElementById('chat-form');
  const userInput = document.getElementById('user-input');
  const chatMessages = document.getElementById('chat-messages');

  chatToggle.addEventListener('click', () => {
    chatWindow.classList.toggle('hidden');
    setTimeout(() => {
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 100);
  });

  chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const userMsg = userInput.value.trim();
    if (!userMsg) return;

    // Add user's message
    chatMessages.innerHTML += `
      <div class="text-right">
        <div class="bg-blue-500 text-white inline-block px-3 py-2 rounded-xl shadow">${userMsg}</div>
      </div>
    `;
    userInput.value = '';
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Add loading spinner
    const loader = document.createElement('div');
    loader.className = "flex items-center space-x-2 text-left text-gray-500 animate-pulse";
    loader.innerHTML = `
      <div class="flex items-center space-x-2">
        <div class="w-4 h-4 border-2 border-t-blue-500 border-blue-200 rounded-full animate-spin"></div>
        <span>Typing...</span>
      </div>
    `;
    chatMessages.appendChild(loader);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Send request
    try {
      const response = await fetch('gemini_chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userMsg })
      });
      const data = await response.json();
      loader.remove();

      chatMessages.innerHTML += `
        <div class="text-left">
          <div class="bg-gray-200 text-black inline-block px-3 py-2 rounded-xl shadow">${data.reply}</div>
        </div>
      `;
      chatMessages.scrollTop = chatMessages.scrollHeight;

    } catch (err) {
      loader.remove();
      chatMessages.innerHTML += `
        <div class="text-left text-red-600 italic">Something went wrong. Try again later.</div>
      `;
    }
  });
</script>

</body>
</html>
