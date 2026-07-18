<!-- Datoteka: src/Views/register.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center">Registracija</h1>
        
        <!-- Prikaz greške -->
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 shadow-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/register/submit" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <input type="text" name="name" placeholder="Ime i prezime" required class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:border-blue-500">
            <input type="email" name="email" placeholder="Email" required class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:border-blue-500">
            <input type="password" name="password" placeholder="Lozinka" required class="w-full p-3 border border-gray-300 rounded mb-6 focus:outline-none focus:border-blue-500">
            
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded transition">Registriraj se</button>
        </form>
        <p class="mt-6 text-sm text-center text-gray-600">Već imate račun? <a href="/login" class="text-blue-600 hover:underline font-medium">Prijavite se</a></p>
    </div>
</body>
</html>