<?php
namespace App\Models;

use PDO;

class Ticket {
    private PDO $db;

    // Konstruktor sada prima PDO konekciju putem Dependency Injection-a
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll($userId = null, $role = 'client') {
        $sql = "SELECT tickets.*, users.name as user_name 
                FROM tickets 
                JOIN users ON tickets.user_id = users.id";

        if ($role === 'pm' || $role === 'admin') {
            // PM i Admin vide apsolutno sve tickete
            $sql .= " ORDER BY tickets.created_at DESC";
            $stmt = $this->db->query($sql);
            
        } elseif ($role === 'agent') {
            // Agenti vide isključivo tickete koji su dodijeljeni njima
            $sql .= " WHERE tickets.assigned_to = ?";
            $sql .= " ORDER BY tickets.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            
        } else {
            // Obični korisnici (klijenti) vide samo tickete koje su sami kreirali
            $sql .= " WHERE tickets.user_id = ?";
            $sql .= " ORDER BY tickets.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Metoda za dodjeljivanje agenta
    public function assignAgent($ticketId, $agentId) {
        $sql = "UPDATE tickets SET assigned_to = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$agentId, $ticketId]);
    }

    // Metoda za dohvat svih agenata
    public function getAgents() {
        $sql = "SELECT id, name FROM users WHERE role = 'agent'";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($userId, $title, $description) {
        $sql = "INSERT INTO tickets (user_id, title, description) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $title, $description]);
    }

    // Dohvaća jedan ticket po ID-u
    public function findById($id) {
        $sql = "SELECT tickets.*, users.name as user_name 
                FROM tickets 
                JOIN users ON tickets.user_id = users.id 
                WHERE tickets.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Dohvaća sve odgovore za određeni ticket
    public function getReplies($ticketId) {
        $sql = "SELECT replies.*, users.name as user_name 
                FROM replies 
                JOIN users ON replies.user_id = users.id 
                WHERE replies.ticket_id = ? 
                ORDER BY replies.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Dodaje odgovor i mijenja status ticketa koristeći TRANSAKCIJU
    public function addReplyWithStatusUpdate($ticketId, $userId, $message, $newStatus) {
        try {
            $this->db->beginTransaction();

            // 1. Dodaj odgovor
            $sqlReply = "INSERT INTO replies (ticket_id, user_id, message) VALUES (?, ?, ?)";
            $stmtReply = $this->db->prepare($sqlReply);
            $stmtReply->execute([$ticketId, $userId, $message]);

            // 2. Ažuriraj status ticketa
            $sqlStatus = "UPDATE tickets SET status = ? WHERE id = ?";
            $stmtStatus = $this->db->prepare($sqlStatus);
            $stmtStatus->execute([$newStatus, $ticketId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            // U pravoj aplikaciji ovdje bismo logirali grešku (npr. error_log)
            return false;
        }
    }
}