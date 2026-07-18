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

    // Prikaz svih ticketa
    public function index() {
        $ticketModel = new Ticket();
        $tickets = $ticketModel->getAll();
        
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