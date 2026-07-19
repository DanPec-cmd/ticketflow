<?php
namespace App\Controllers;

use App\Models\Ticket;
use App\Core\AuthGuard; // Prilagodi namespace tvom AuthGuardu

class TicketController {
    
    private Ticket $ticketModel;

    // Dependency Injection: Prima gotov Ticket model (u koji je već ubačen PDO)
    public function __construct(Ticket $ticketModel) {
        $this->ticketModel = $ticketModel;
        
        AuthGuard::requireLogin();
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
    }

    public function index() {
        $tickets = $this->ticketModel->getAll($_SESSION['user_id'], $_SESSION['user_role']);
        require_once __DIR__ . '/../Views/list.php';
    }

    public function create() {
        require_once __DIR__ . '/../Views/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $_SESSION['error'] = "Sigurnosna greška: Neispravan token. Pokušajte ponovno.";
                header('Location: /tickets/create');
                exit;
            }

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (strlen($title) < 5 || strlen($title) > 255) {
                $_SESSION['error'] = "Naslov mora biti između 5 i 255 znakova.";
                header('Location: /tickets/create');
                exit;
            }

            if (strlen($description) < 10) {
                $_SESSION['error'] = "Opis problema mora imati minimalno 10 znakova.";
                header('Location: /tickets/create');
                exit;
            }

            $this->ticketModel->create($_SESSION['user_id'], htmlspecialchars($title), htmlspecialchars($description));
            
            $_SESSION['message'] = "Ticket je uspješno kreiran.";
            header('Location: /');
            exit;
        }
    }

    public function show($id = null) {
        if (!$id) { 
            header('Location: /'); 
            exit; 
        }

        $ticket = $this->ticketModel->findById($id);

        if (!$ticket) {
            echo "Ticket ne postoji.";
            return;
        }

        if ($_SESSION['user_role'] === 'client' && $ticket['user_id'] != $_SESSION['user_id']) {
            die("Nemate dozvolu za pregled ovog ticketa.");
        }

        $replies = $this->ticketModel->getReplies($id);
        
        // KLJUČNO ZA DEPENDENCY INJECTION: Kontroler dohvaća agente i šalje ih u View
        $agents = [];
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pm') {
            $agents = $this->ticketModel->getAgents();
        }

        require_once __DIR__ . '/../Views/show.php';
    }

    public function addReply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId = $_POST['ticket_id'] ?? null;
            
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $_SESSION['error'] = "Sigurnosna greška: Neispravan token. Pokušajte ponovno.";
                header("Location: /ticket/" . $ticketId);
                exit;
            }

            $message = trim($_POST['message'] ?? '');
            $status = $_POST['status'] ?? 'open';
            
            if (strlen($message) < 2) {
                $_SESSION['error'] = "Odgovor ne može biti prazan (minimalno 2 znaka).";
                header("Location: /ticket/" . $ticketId);
                exit;
            }

            $this->ticketModel->addReplyWithStatusUpdate($ticketId, $_SESSION['user_id'], htmlspecialchars($message), $status);
            
            $_SESSION['message'] = "Odgovor je uspješno dodan.";
            header("Location: /ticket/" . $ticketId);
            exit;
        }
    }

    public function assign() {
        AuthGuard::requireRole('pm');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticket_id = $_POST['ticket_id'] ?? null;
            
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $_SESSION['error'] = "Greška: Neispravan sigurnosni token.";
                header("Location: /ticket/" . $ticket_id);
                exit;
            }

            $agent_id = $_POST['agent_id'] ?? null;
            if ($ticket_id) {
                $agent_id_db = ($agent_id === '') ? null : $agent_id;
                
                if ($this->ticketModel->assignAgent($ticket_id, $agent_id_db)) {
                    $_SESSION['message'] = "Agent je uspješno dodijeljen.";
                } else {
                    $_SESSION['error'] = "Greška prilikom dodjeljivanja agenta.";
                }
            }
            header("Location: /ticket/" . $ticket_id);
            exit;
        }
    }
}