<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - MedQueue</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#3B82F6', // Blue-500
            secondary: '#10B981', // Green-500
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gradient-to-br from-blue-100 via-white to-green-100 min-h-screen flex items-center justify-center px-4 font-sans">

  <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl border border-blue-100">
    <div class="text-center mb-6">
      <h2 class="text-3xl font-extrabold text-primary drop-shadow">Welcome Back!</h2>
      <p class="text-gray-500 text-sm mt-1">Login to continue to MedQueue</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-semibold text-center shadow">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="Backend/login.php" method="POST" class="space-y-5">
      <div>
        <label class="block mb-1 font-medium text-gray-700">Email</label>
        <div class="relative">
          <input type="email" name="email" placeholder="you@example.com" required class="w-full border border-gray-300 px-4 py-2 pl-10 rounded-lg focus:ring-2 focus:ring-blue-300">
          <span class="absolute left-3 top-2.5 text-gray-400">📧</span>
        </div>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Password</label>
        <div class="relative">
          <input type="password" name="password" placeholder="••••••••" required class="w-full border border-gray-300 px-4 py-2 pl-10 rounded-lg focus:ring-2 focus:ring-blue-300">
          <span class="absolute left-3 top-2.5 text-gray-400">🔒</span>
        </div>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Login As</label>
        <select name="role" required class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-300">
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-green-500 text-white font-semibold py-2 rounded-xl shadow-md hover:from-green-500 hover:to-blue-500 transition duration-300">
        Login
      </button>
    </form>

    <p class="mt-6 text-sm text-center text-gray-600">
      Don't have an account?
      <a href="register.php" class="text-primary font-medium hover:underline">Register</a>
    </p>
  </div>

</body>
</html>
