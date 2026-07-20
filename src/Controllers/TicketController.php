<?php
// Datoteka: src/Controllers/TicketController.php
namespace App\Controllers;

use App\Models\Ticket; 
use App\Core\AuthGuard;
use App\Core\Validator;
use App\Core\Flash; // Dodano!
use Exception;

class TicketController {
    
    private Ticket $ticketModel;

    public function __construct(Ticket $ticketModel) {
        $this->ticketModel = $ticketModel;
        
        AuthGuard::requireLogin();
        // Generiranje CSRF tokena ako već ne postoji
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
    }

    // Pomoćna metoda za CSRF provjeru (kako ne bismo ponavljali kod)
    private function validateCsrf() {
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
            Flash::error("Sigurnosna provjera nije uspjela.");
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/');
            exit;
        }
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];

        $perPage = 10;
        $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;

        $offset = ($currentPage - 1) * $perPage;

        $totalTickets = $this->ticketModel->getTotalTicketsCount($userId, $userRole);
        $totalPages = ceil($totalTickets / $perPage);

        if ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
            $offset = ($currentPage - 1) * $perPage;
        }

        $tickets = $this->ticketModel->getPaginatedTickets($userId, $userRole, $perPage, $offset);

        require_once __DIR__ . '/../Views/list.php';
    }

    public function create() {
        require_once __DIR__ . '/../Views/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tickets/create');
            exit;
        }

        $this->validateCsrf();

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!Validator::string($title, 5, 255)) {
            Flash::error("Naslov mora biti između 5 i 255 znakova.");
            header('Location: /tickets/create');
            exit;
        }

        if (!Validator::string($description, 10)) {
            Flash::error("Opis problema mora imati minimalno 10 znakova.");
            header('Location: /tickets/create');
            exit;
        }

        $this->ticketModel->create($_SESSION['user_id'], htmlspecialchars($title), htmlspecialchars($description));
        
        Flash::success("Ticket je uspješno kreiran.");
        header('Location: /');
        exit;
    }

    public function show($id = null) {
        if (!$id) { 
            header('Location: /'); 
            exit; 
        }

        $ticket = $this->ticketModel->findById($id);

        if (!$ticket) {
            Flash::error("Ticket nije pronađen.");
            header('Location: /');
            exit;
        }

        if ($_SESSION['user_role'] === 'client' && $ticket['user_id'] != $_SESSION['user_id']) {
            Flash::error("Nemate dozvolu za pregled ovog ticketa.");
            header('Location: /');
            exit;
        }

        $replies = $this->ticketModel->getReplies($id);
        
        $agents = [];
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pm') {
            $agents = $this->ticketModel->getAgents();
        }

        require_once __DIR__ . '/../Views/show.php';
    }

    public function addReply() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $this->validateCsrf();

        $ticketId = $_POST['ticket_id'] ?? null;
        $message = trim($_POST['message'] ?? '');
        $status = $_POST['status'] ?? 'open';
        
        if (!Validator::string($message, 2)) {
            Flash::error("Odgovor ne može biti prazan.");
            header("Location: /ticket/" . $ticketId);
            exit;
        }

        $this->ticketModel->addReplyWithStatusUpdate($ticketId, $_SESSION['user_id'], htmlspecialchars($message), $status);
        
        Flash::success("Odgovor je uspješno dodan.");
        header("Location: /ticket/" . $ticketId);
        exit;
    }

    public function assign() {
        AuthGuard::requireRole('pm');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $this->validateCsrf();

        $ticket_id = $_POST['ticket_id'] ?? null;
        $agent_id = $_POST['agent_id'] ?? null;
        
        if ($ticket_id) {
            $agent_id_db = ($agent_id === '') ? null : $agent_id;
            
            if ($this->ticketModel->assignAgent($ticket_id, $agent_id_db)) {
                Flash::success("Agent je uspješno dodijeljen.");
            } else {
                Flash::error("Greška prilikom dodjeljivanja agenta.");
            }
        }
        
        header("Location: /ticket/" . $ticket_id);
        exit;
    }
}