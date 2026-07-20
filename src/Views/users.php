<!-- Datoteka: src/Views/users.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravljanje korisnicima</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 md:p-8 font-sans">
    <div class="max-w-5xl mx-auto">
        
        <!-- Header sekcija -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Upravljanje korisnicima</h1>
                <p class="text-sm text-gray-500 mt-1">Pregled svih registriranih korisnika i ažuriranje njihovih pristupnih uloga.</p>
            </div>
            <a href="/" class="text-blue-600 hover:underline font-medium text-sm flex items-center gap-1">
                ← Povratak na naslovnicu
            </a>
        </div>

        <!-- Obavijesti o statusu akcije (Flash poruke) -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex justify-between items-center">
                <span class="text-sm font-medium">✓ <?= $_SESSION['message']; unset($_SESSION['message']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex justify-between items-center">
                <span class="text-sm font-medium">⚠ <?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Tablica s korisnicima -->
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-600 w-16">ID</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Ime</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Email</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Upravljanje ulogom</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <!-- ID -->
                            <td class="p-4 text-sm font-medium text-gray-500">
                                #<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <!-- Ime -->
                            <td class="p-4 text-sm font-semibold text-gray-900">
                                <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <!-- Email -->
                            <td class="p-4 text-sm text-gray-600">
                                <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <!-- Akcija / Forma -->
                            <td class="p-4">
                                <form action="/users/update-role" method="POST" class="flex items-center gap-3">
                                    <!-- CSRF zaštita -->
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    
                                    <!-- Select dropdown -->
                                    <select name="role" class="block w-44 rounded-md border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm border focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Klijent</option>
                                        <option value="agent" <?= $user['role'] === 'agent' ? 'selected' : '' ?>>Agent</option>
                                        <option value="pm" <?= $user['role'] === 'pm' ? 'selected' : '' ?>>Project Manager</option>
                                    </select>
                                    
                                    <!-- Button -->
                                    <button type="submit" 
                                            <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>
                                            class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm font-medium shadow hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed disabled:shadow-none">
                                        Ažuriraj
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>