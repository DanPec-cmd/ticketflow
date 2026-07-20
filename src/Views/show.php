<!-- Datoteka: src/Views/show.php -->
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= htmlspecialchars($ticket['id'], ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 md:p-8 font-sans">

    <div class="max-w-3xl mx-auto">
        
        <!-- Prikaz globalnih flash poruka (uspjeh, greške kod dodjele, odgovora, itd.) -->
        <?= \App\Core\Flash::display() ?>

        <a href="/" class="text-blue-600 hover:underline mb-6 inline-flex items-center font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Natrag na popis
        </a>
        
        <!-- PM Forma za dodjeljivanje agenta -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pm'): ?>
            <div class="bg-indigo-50 p-4 rounded-lg mb-6 border border-indigo-200 shadow-sm">
                <form action="/ticket/assign" method="POST" class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id'], ENT_QUOTES, 'UTF-8') ?>">
                    
                    <div class="flex-1">
                        <label for="agent_id" class="block text-sm font-bold text-gray-700 mb-1">Dodijeli ticket agentu:</label>
                        <select id="agent_id" name="agent_id" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">-- Odaberi agenta --</option>
                            <?php foreach ($agents ?? [] as $agent): ?>
                                <option value="<?= htmlspecialchars($agent['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $ticket['assigned_to'] == $agent['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($agent['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded text-sm font-semibold hover:bg-indigo-700 transition h-[42px] shadow">
                        Spremi dodjelu
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Glavni Ticket -->
        <div class="bg-white p-6 shadow-md rounded-lg mb-8 border-l-4 <?= $ticket['status'] === 'closed' ? 'border-green-500' : 'border-blue-500' ?>">
            <div class="flex justify-between items-start mb-4 gap-4">
                <h1 class="text-2xl font-bold text-gray-800 break-words"><?= htmlspecialchars($ticket['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                
                <?php 
                $statusClass = 'bg-blue-100 text-blue-800';
                if ($ticket['status'] === 'open') {
                    $statusClass = 'bg-yellow-100 text-yellow-800';
                } elseif ($ticket['status'] === 'closed') {
                    $statusClass = 'bg-green-100 text-green-800';
                }
                ?>
                <span class="px-3 py-1 text-xs font-bold rounded-full uppercase <?= $statusClass ?> shrink-0">
                    <?= htmlspecialchars($ticket['status'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            
            <p class="text-sm text-gray-500 mb-6 pb-4 border-b border-gray-100">
                Prijavio/la: <strong class="text-gray-700"><?= htmlspecialchars($ticket['user_name'], ENT_QUOTES, 'UTF-8') ?></strong> 
                dana <?= htmlspecialchars(date('d.m.Y \u H:i', strtotime($ticket['created_at'])), ENT_QUOTES, 'UTF-8') ?>
            </p>
            
            <div class="text-gray-800 bg-gray-50 p-5 rounded-lg border border-gray-100 whitespace-pre-wrap leading-relaxed">
                <?= htmlspecialchars($ticket['description'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <!-- Komentari i Odgovori -->
        <h3 class="text-xl font-bold text-gray-800 mb-4">Komentari i Odgovori</h3>
        <div class="space-y-4 mb-8">
            <?php if (!empty($replies)): ?>
                <?php foreach ($replies as $reply): ?>
                    <div class="bg-white p-5 shadow-sm rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-gray-800"><?= htmlspecialchars($reply['user_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                <?= htmlspecialchars(date('d.m.Y H:i', strtotime($reply['created_at'])), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap leading-relaxed"><?= htmlspecialchars($reply['message'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-gray-50 p-6 rounded-lg border border-dashed border-gray-300 text-center">
                    <p class="text-gray-500 font-medium">Još nema odgovora na ovaj ticket.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Forma za dodavanje odgovora -->
        <?php if ($ticket['status'] !== 'closed'): ?>
            <div class="bg-white p-6 shadow-md rounded-lg border border-gray-200">
                <form action="/ticket/reply" method="POST">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id'], ENT_QUOTES, 'UTF-8') ?>">
                    
                    <div class="mb-4">
                        <label for="message" class="block text-gray-800 font-bold mb-2">Vaš odgovor</label>
                        <textarea id="message" name="message" rows="4" required placeholder="Upišite svoj komentar ovdje..." class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="w-full sm:w-auto">
                            <label for="status" class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Ažuriraj status</label>
                            <select id="status" name="status" class="w-full sm:w-auto border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 font-medium">
                                <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Otvoreno</option>
                                <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>U radu</option>
                                <option value="closed">Označi kao Riješeno (Zatvori)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-8 py-3 rounded-lg shadow hover:bg-blue-700 font-bold transition">
                            Pošalji Odgovor
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-gray-200 text-center p-6 rounded-lg border border-gray-300">
                <span class="text-gray-600 font-bold flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    Ovaj ticket je zatvoren. Ne možete dodavati nove odgovore.
                </span>
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>