<?php
// Datoteka: src/Controllers/TicketController.php

class TicketController {
    
    // Konstruktor osigurava da su samo prijavljeni korisnici unutar aplikacije
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    // PM dodjeljuje ticket agentu
   // Obrada dodjeljivanja ticketa agentu
    public function assign() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Sigurnosna provjera: Samo PM može dodjeljivati tickete
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pm') {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticket_id = $_POST['ticket_id'] ?? null;
            $agent_id = $_POST['agent_id'] ?? null;

            if ($ticket_id) {
                $ticketModel = new Ticket();
                
                // Ako je PM odabrao "-- Odaberi agenta --" (prazan value), postavljamo na null
                $agent_id_db = ($agent_id === '') ? null : $agent_id;
                
                if ($ticketModel->assignAgent($ticket_id, $agent_id_db)) {
                    // Flash poruka o uspjehu (ispisat će se u show.php)
                    $_SESSION['message'] = "Agent je uspješno dodijeljen.";
                } else {
                    $_SESSION['message'] = "Greška prilikom dodjeljivanja agenta.";
                }
            }

            // Vraćamo korisnika nazad na detalje ticketa
            header("Location: /ticket/show?id=" . $ticket_id);
            exit;
        }
    }
    
    // Prikaz svih ticketa
    public function index() {
        $ticketModel = new Ticket();
        
        // Dohvaćamo tickete ovisno o prijavljenom korisniku
        $tickets = $ticketModel->getAll($_SESSION['user_id'], $_SESSION['user_role']);
        
        require_once '../src/Views/list.php';
    }

    // Prikaz forme za novi ticket
    public function create() {
        require_once '../src/Views/create.php';
    }

    // Spremanje novog ticketa
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            
            // Uzimamo ID iz sesije, ne hardkodiramo više
            $userId = $_SESSION['user_id']; 

            if (!empty($title) && !empty($description)) {
                $ticketModel = new Ticket();
                $ticketModel->create($userId, $title, $description);
            }
            
            header('Location: /');
            exit;
        }
    }

    // Prikaz pojedinačnog ticketa
    public function show() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: /');
            exit;
        }

        $ticketModel = new Ticket();
        $ticket = $ticketModel->findById($id);

        if (!$ticket) {
            echo "Ticket ne postoji.";
            return;
        }

        // Provjera: Klijent smije vidjeti samo svoje tickete. Agenti i PM-ovi vide sve.
        if ($_SESSION['user_role'] === 'client' && $ticket['user_id'] != $_SESSION['user_id']) {
            die("Nemate dozvolu za pregled ovog ticketa.");
        }

        $replies = $ticketModel->getReplies($id);
        
        require_once '../src/Views/show.php';
    }

    // Spremanje odgovora na ticket
    public function addReply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId = $_POST['ticket_id'] ?? null;
            $message = htmlspecialchars(trim($_POST['message'] ?? ''));
            $status = $_POST['status'] ?? 'open';
            $userId = $_SESSION['user_id']; 

            if ($ticketId && !empty($message)) {
                $ticketModel = new Ticket();
                $ticketModel->addReplyWithStatusUpdate($ticketId, $userId, $message, $status);
            }
            
            header("Location: /ticket?id=" . $ticketId);
            exit;
        }
    }
}