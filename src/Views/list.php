<!-- Datoteka: src/Views/list.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticketing Sustav - Popis Ticketa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 md:p-8 font-sans">
    <div class="max-w-5xl mx-auto">
        
        <!-- PM Upravljanje -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pm'): ?>
            <div class="mb-4">
                <a href="/users" class="text-indigo-600 hover:underline font-medium text-sm flex items-center gap-1">
                    ⚙️ Upravljanje korisnicima (PM)
                </a>
            </div>
        <?php endif; ?>

        <!-- Prikaz globalnih flash poruka (npr. Uspješno kreiran ticket) -->
        <?= \App\Core\Flash::display() ?>

        <!-- Header s informacijama o korisniku -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Svi Ticketi</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Prijavljeni ste kao: <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Korisnik', ENT_QUOTES, 'UTF-8') ?></strong> 
                    (<a href="/logout" class="text-red-500 hover:underline">Odjava</a>)
                </p>
            </div>
            <a href="/tickets/create" class="bg-blue-600 text-white px-5 py-2 rounded shadow hover:bg-blue-700 transition font-medium">
                + Novi Ticket
            </a>
        </div>

        <!-- Tablica s ticketima -->
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
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
                                <td class="p-4">
                                    <a href="/ticket/<?= urlencode($ticket['id']) ?>" class="text-blue-600 hover:underline font-medium">
                                        #<?= htmlspecialchars($ticket['id'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </td>
                                <td class="p-4 font-medium text-gray-900">
                                    <?= htmlspecialchars($ticket['title'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="p-4 text-gray-700">
                                    <?= htmlspecialchars($ticket['user_name'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="p-4">
                                    <?php 
                                    $statusClasses = [
                                        'open' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'closed' => 'bg-green-100 text-green-800'
                                    ];
                                    $class = $statusClasses[$ticket['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $class ?>">
                                        <?= htmlspecialchars(strtoupper($ticket['status']), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="p-4 text-gray-500 text-sm">
                                    <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Nema otvorenih ticketa.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginacija -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6 bg-gray-50 mt-4 rounded-lg shadow-sm">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Prikazano <span class="font-medium"><?= $offset + 1 ?></span> do 
                            <span class="font-medium"><?= min($offset + $perPage, $totalTickets) ?></span> od 
                            <span class="font-medium"><?= $totalTickets ?></span> ticketa
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            
                            <!-- Gumb 'Prethodna' -->
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?>" class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    &laquo; Nazad
                                </a>
                            <?php else: ?>
                                <span class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                    &laquo; Nazad
                                </span>
                            <?php endif; ?>

                            <!-- Brojevi stranica -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 border <?= $i === $currentPage ? 'border-blue-500 bg-blue-50 text-blue-600 z-10' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' ?> text-sm font-medium">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Gumb 'Sljedeća' -->
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>" class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    Naprijed &raquo;
                                </a>
                            <?php else: ?>
                                <span class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                    Naprijed &raquo;
                                </span>
                            <?php endif; ?>

                        </nav>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>