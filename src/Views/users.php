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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Povratak na naslovnicu
            </a>
        </div>

        <!-- Prikaz globalnih flash poruka o uspješnoj/nepuspješnoj promjeni uloge -->
        <?= \App\Core\Flash::display() ?>

        <!-- Tablica s korisnicima -->
        <div class="bg-white shadow-md rounded-lg overflow-x-auto border border-gray-200">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-600 w-16">ID</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Ime</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Email</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Trenutna uloga / Promjena</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <?php $isCurrentUser = ($user['id'] == ($_SESSION['user_id'] ?? null)); ?>
                        <tr class="transition <?= $isCurrentUser ? 'bg-blue-50/60 hover:bg-blue-50' : 'hover:bg-gray-50' ?>">
                            
                            <!-- ID -->
                            <td class="p-4 text-sm font-medium text-gray-500">
                                #<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            
                            <!-- Ime -->
                            <td class="p-4 text-sm font-semibold text-gray-900">
                                <div class="flex items-center gap-2">
                                    <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php if ($isCurrentUser): ?>
                                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">Vi</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                            <!-- Email -->
                            <td class="p-4 text-sm text-gray-600">
                                <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            
                            <!-- Akcija / Forma -->
                            <td class="p-4">
                                <form action="/users/update-role" method="POST" class="flex items-center gap-3-wrap sm:flex-nowrap gap-3">
                                    <!-- Osigurana CSRF zaštita i pročišćen ID -->
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    
                                    <!-- Select dropdown -->
                                    <select name="role" 
                                            <?= $isCurrentUser ? 'disabled' : '' ?>
                                            class="block w-44 rounded-md border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm border focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                                        <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Klijent</option>
                                        <option value="agent" <?= $user['role'] === 'agent' ? 'selected' : '' ?>>Agent</option>
                                        <option value="pm" <?= $user['role'] === 'pm' ? 'selected' : '' ?>>Project Manager</option>
                                    </select>
                                    
                                    <!-- Button -->
                                    <button type="submit" 
                                            <?= $isCurrentUser ? 'disabled' : '' ?>
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