<!-- Datoteka: src/Views/login.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava u sustav - Ticketing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans p-4">
    <div class="bg-white p-8 shadow-md rounded-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Ticketing Sustav</h1>
        
        <!-- Prikaz flash poruka (npr. greške kod prijave ili uspješna registracija) -->
        <?= \App\Core\Flash::display() ?>

        <form action="/login/submit" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" required placeholder="unos@domena.com"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Lozinka</label>
                <input type="password" id="password" name="password" required placeholder="Vaša lozinka"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition duration-300 shadow">
                Prijava
            </button>
        </form>

        <p class="mt-6 text-sm text-center text-gray-600">
            Nemate račun? <a href="/register" class="text-blue-600 hover:underline font-medium">Registrirajte se</a>
        </p>
    </div>
</body>
</html>