<!-- Datoteka: src/Views/list.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticketing Sustav</title>
    <!-- Tailwind pozvan preko cdn-a -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Svi Ticketi</h1>
            <a href="/tickets/create" class="bg-blue-600 text-white px-5 py-2 rounded shadow hover:bg-blue-700 transition">
                + Novi Ticket
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-600">ID</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Naslov</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Klijent</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Status</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Datum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 text-gray-500">#<?= $ticket['id'] ?></td>
                                <!-- htmlspecialchars jer nitko ne voli XSS (Cross-Site Scripting) -->
                                <td class="p-4 font-medium text-gray-900"><?= htmlspecialchars($ticket['title']) ?></td>
                                <td class="p-4 text-gray-700"><?= htmlspecialchars($ticket['user_name']) ?></td>
                                <td><a href="/ticket?id=<?= $ticket['id'] ?>" class="text-blue-600 hover:underline">#<?= $ticket['id'] ?></a></td>
                                <td class="p-4">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                        <?= $ticket['status'] === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= strtoupper($ticket['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-gray-500 text-sm">
                                    <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Nema otvorenih ticketa. Možemo na pauzu! :D</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>