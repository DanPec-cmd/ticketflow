<?php
// Datoteka: src/Controllers/TicketController.php
namespace App\Controllers;

use App\Models\Ticket; 
use App\Core\AuthGuard;
use App\Core\Validator;
use Exception;

class TicketController {
    
    private Ticket $ticketModel;

    // Dependency Injection: Prima gotov Ticket model (u koji je već ubačen PDO)
    public function __construct(Ticket $ticketModel) {
        $this->ticketModel = $ticketModel;
        
        AuthGuard::requireLogin();
        // Generiranje tokena ostavljamo ovdje kako bi ga Views (HTML forme) mogle ispisati
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];

        // 1. Postavke paginacije
        $perPage = 10; // Broj ticketa po stranici
        $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;

        $offset = ($currentPage - 1) * $perPage;

        // 2. Dohvati ukupan broj ticketa i izračunaj ukupan broj stranica
        $totalTickets = $this->ticketModel->getTotalTicketsCount($userId, $userRole);
        $totalPages = ceil($totalTickets / $perPage);

        // Zaštita: Ako korisnik ručno upiše previsok broj stranice u URL
        if ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
            $offset = ($currentPage - 1) * $perPage;
        }

        // 3. Dohvati samo tickete za trenutnu stranicu
        $tickets = $this->ticketModel->getPaginatedTickets($userId, $userRole, $perPage, $offset);

        // Proslijedi sve varijable u View
        require_once __DIR__ . '/../Views/list.php';
    }

    public function create() {
        require_once __DIR__ . '/../Views/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (!Validator::string($title, 5, 255)) {
                $_SESSION['error'] = "Naslov mora biti između 5 i 255 znakova.";
                header('Location: /tickets/create');
                exit;
            }

            if (!Validator::string($description, 10)) {
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
        
        $agents = [];
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pm') {
            $agents = $this->ticketModel->getAgents();
        }

        require_once __DIR__ . '/../Views/show.php';
    }

    public function addReply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId = $_POST['ticket_id'] ?? null;
            $message = trim($_POST['message'] ?? '');
            $status = $_POST['status'] ?? 'open';
            
        if (!Validator::string($message, 2)) {
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