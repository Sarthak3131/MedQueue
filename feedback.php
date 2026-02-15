<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $feedback = $_POST['feedback'];

    $sql = "INSERT INTO feedback (name, feedback) VALUES (?, ?)";
    $stmt = $conn->prepare(query: $sql);
    $stmt->bind_param("ss", $name, $feedback);

    if ($stmt->execute()) {
        $message = "Thanks for your feedback!";
    } else {
        $message = "Something went wrong!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - MedQueue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="images/d1.png">
    <style>
        .feedback-card {
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f3ff 100%);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .feedback-card:hover {
            transform: translateY(-5px);
        }
        .emoji {
            font-size: 2.5rem;
            margin-right: 0.5rem;
        }
        .rating-star {
            color: #ffd700;
            font-size: 1.2rem;
        }
        .submit-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-50 via-white to-blue-100 min-h-screen text-gray-800 font-sans">
    <!-- Navbar -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-4">
            <div class="flex items-center space-x-2">
                <img src="images/d1.png" class="w-8 h-8" alt="Logo" />
                <h1 class="text-xl font-bold">MedQueue</h1>
            </div>
            <nav class="flex items-center space-x-6 text-sm md:text-base">
                <a href="index.php" class="hover:underline">Home</a>
                <a href="doctor.php" class="hover:underline">Doctors</a>
                <a href="about.php" class="hover:underline">About</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="dashboard.php" class="hover:underline">Dashboard</a>
                    <a href="logout.php" class="hover:underline text-red-200">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="hover:underline">Login</a>
                    <a href="register.php" class="hover:underline">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 bg-gradient-to-r from-blue-50 to-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold text-blue-800 mb-4">Share Your Experience</h1>
            <p class="text-xl text-gray-600 mb-8">Your feedback helps us improve our services and provide better care</p>
            <div class="flex justify-center space-x-4 mb-8">
                <span class="rating-star">⭐</span>
                <span class="rating-star">⭐</span>
                <span class="rating-star">⭐</span>
                <span class="rating-star">⭐</span>
                <span class="rating-star">⭐</span>
            </div>
        </div>
    </section>

    <!-- Feedback Form -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="feedback-card p-8 rounded-xl">
                <h2 class="text-2xl font-bold text-blue-800 mb-6">Tell Us What You Think</h2>

                <?php if (!empty($message)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700">
                        <span class="emoji">✅</span>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">👤</span>
                        <input type="text" name="name" placeholder="Your name" required 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">📝</span>
                        <textarea name="feedback" placeholder="Share your thoughts and experiences..." required 
                                  rows="4" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" class="submit-btn px-8 py-3 text-white font-semibold rounded-lg hover:shadow-lg">
                            <span class="emoji">💬</span>
                            Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Why Share Feedback -->
    <section class="py-12 bg-gradient-to-r from-white to-blue-50">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-blue-800 mb-6 text-center">Why Share Your Feedback?</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="p-6 border rounded-lg text-center">
                    <span class="emoji text-4xl mb-4">✨</span>
                    <h3 class="font-semibold mb-2">Improve Services</h3>
                    <p class="text-gray-600">Help us enhance our medical services and patient care</p>
                </div>
                <div class="p-6 border rounded-lg text-center">
                    <span class="emoji text-4xl mb-4">📈</span>
                    <h3 class="font-semibold mb-2">Track Progress</h3>
                    <p class="text-gray-600">Your feedback helps us monitor and improve our performance</p>
                </div>
                <div class="p-6 border rounded-lg text-center">
                    <span class="emoji text-4xl mb-4">❤️</span>
                    <h3 class="font-semibold mb-2">Better Care</h3>
                    <p class="text-gray-600">Your input helps us provide better care for all patients</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
