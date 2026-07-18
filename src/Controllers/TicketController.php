<?php
// Datoteka: src/Controllers/TicketController.php

class TicketController {
    
    // Trebam $tickets u view-u (GET /)
    public function index() {
        $ticketModel = new Ticket();
        $tickets = $ticketModel->getAll(); 
        
        require_once '../src/Views/list.php';
    }

    // Prikaz forme za novi ticket (GET /tickets/create)
    public function create() {
        require_once '../src/Views/create.php';
    }

    // Spremanje novog ticketa u bazu (POST /tickets/store)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Osnovna sanitizacija unosa da nekome ne bi nešto palo na pamet
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            
            // ID 1 je zaharkodiran jer sam ga ručno unio u bazu
            $userId = 1; 

            if (!empty($title) && !empty($description)) {
                $ticketModel = new Ticket();
                $ticketModel->create($userId, $title, $description);
            }
            
            // Post/Redirect/Get  Nakon POST zahtjeva, preusmjeri na GET
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

        $replies = $ticketModel->getReplies($id);
        
        require_once '../src/Views/show.php';
    }

    // Spremanje odgovora
    public function addReply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketId = $_POST['ticket_id'] ?? null;
            $message = htmlspecialchars(trim($_POST['message'] ?? ''));
            $status = $_POST['status'] ?? 'open';
            $userId = 1; // Opet koristimo našeg testnog korisnika

            if ($ticketId && !empty($message)) {
                $ticketModel = new Ticket();
                $ticketModel->addReplyWithStatusUpdate($ticketId, $userId, $message, $status);
            }
            
            header("Location: /ticket?id=" . $ticketId);
            exit;
        }
    }
}