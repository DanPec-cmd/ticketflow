<!-- Datoteka: src/Views/create.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novi Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-2xl mx-auto bg-white p-8 shadow-md rounded-lg mt-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Prijavi novi problem</h1>

        <!-- Prikaz flash poruka (uspjeh ili greška) -->
        <?= \App\Core\Flash::display() ?>

        <form action="/tickets/store" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-5">
                <label class="block text-gray-700 font-semibold mb-2" for="title">Naslov problema</label>
                <input type="text" id="title" name="title" required placeholder="Kratki opis (npr. Ne radi mi email)" 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2" for="description">Detaljan opis</label>
                <textarea id="description" name="description" rows="5" required placeholder="Opišite korake do greške..." 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-between items-center">
                <a href="/" class="text-gray-500 hover:text-gray-800 font-medium transition">← Odustani</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 font-medium transition">
                    Spremi Ticket
                </button>
            </div>
        </form>
    </div>
</body>
</html>