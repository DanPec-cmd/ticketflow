<!-- Datoteka: src/Views/show.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= $ticket['id'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-3xl mx-auto">
        <a href="/" class="text-blue-600 hover:underline mb-4 inline-block">← Natrag na popis</a>
        
        <!-- Glavni Ticket -->
        <div class="bg-white p-6 shadow-md rounded-lg mb-6 border-l-4 <?= $ticket['status'] === 'closed' ? 'border-green-500' : 'border-blue-500' ?>">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($ticket['title']) ?></h1>
                <span class="px-3 py-1 text-xs font-semibold rounded-full uppercase
                    <?= $ticket['status'] === 'open' ? 'bg-yellow-100 text-yellow-800' : ($ticket['status'] === 'closed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') ?>">
                    <?= $ticket['status'] ?>
                </span>
            </div>
            <p class="text-sm text-gray-500 mb-4">Prijavio: <strong><?= htmlspecialchars($ticket['user_name']) ?></strong> dana <?= date('d.m.Y H:i', strtotime($ticket['created_at'])) ?></p>
            <div class="text-gray-700 bg-gray-50 p-4 rounded whitespace-pre-wrap"><?= htmlspecialchars($ticket['description']) ?></div>
        </div>

        <!-- Odgovori -->
        <h3 class="text-lg font-bold text-gray-800 mb-4">Komentari i Odgovori</h3>
        <div class="space-y-4 mb-8">
            <?php if (!empty($replies)): ?>
                <?php foreach ($replies as $reply): ?>
                    <div class="bg-white p-4 shadow-sm rounded-lg border border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($reply['user_name']) ?></span>
                            <span class="text-xs text-gray-500"><?= date('d.m.Y H:i', strtotime($reply['created_at'])) ?></span>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($reply['message']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500 text-sm">Još nema odgovora na ovaj ticket.</p>
            <?php endif; ?>
        </div>

        <!-- Forma za dodavanje odgovora -->
        <?php if ($ticket['status'] !== 'closed'): ?>
            <div class="bg-white p-6 shadow-md rounded-lg">
                <form action="/ticket/reply" method="POST">
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Vaš odgovor</label>
                        <textarea name="message" rows="4" required class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-blue-500"></textarea>
                    </div>

                    <div class="flex justify-between items-center">
                        <select name="status" class="border border-gray-300 rounded-lg p-2 focus:outline-none">
                            <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Ostavi Otvoreno</option>
                            <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>U radu</option>
                            <option value="closed">Označi kao Riješeno</option>
                        </select>
                        
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 font-medium transition">Pošalji Odgovor</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-gray-200 text-center p-4 rounded-lg text-gray-600 font-medium">Ovaj ticket je zatvoren. Ne možete dodavati nove odgovore.</div>
        <?php endif; ?>
    </div>
</body>
</html>