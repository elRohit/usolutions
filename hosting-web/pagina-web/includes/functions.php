<?php
session_start();
require_once 'config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../pages/login.php");
        exit;
    }
}

function login($email, $password) {
    global $conn;
    
    $email = $conn->real_escape_string($email);
    $query = "SELECT id, password, first_name, last_name FROM users WHERE email = '$email' AND status = 1";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            return true;
        }
    }
    
    return false;
}

function logout() {
    session_unset();
    session_destroy();
}

function register($firstName, $lastName, $email, $password) {
    global $conn;
    
    $firstName = $conn->real_escape_string($firstName);
    $lastName = $conn->real_escape_string($lastName);
    $email = $conn->real_escape_string($email);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (first_name, last_name, email, password, created_at) 
              VALUES ('$firstName', '$lastName', '$email', '$hashedPassword', NOW())";
    
    if ($conn->query($query)) {
        return $conn->insert_id;
    }
    
    return false;
}

function getUserServers($userId) {
    global $conn;
    
    $userId = (int)$userId;
    $query = "SELECT * FROM server_view WHERE user_id = $userId ORDER BY purchase_date DESC";
    $result = $conn->query($query);
    
    $servers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $servers[] = $row;
        }
    }
    
    return $servers;
}

function getServerDetails($serverId, $userId) {
    global $conn;
    
    $serverId = (int)$serverId;
    $userId = (int)$userId;
    $query = "SELECT * FROM server_view WHERE server_id = $serverId AND user_id = $userId";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

function getServerBackups($serverId, $userId) {
    global $conn;
    
    $serverId = (int)$serverId;
    $userId = (int)$userId;
    $query = "SELECT sb.* FROM server_backups sb
              JOIN user_servers us ON sb.server_id = us.id
              WHERE sb.server_id = $serverId AND us.user_id = $userId
              ORDER BY sb.created_at DESC";
    $result = $conn->query($query);
    
    $backups = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $backups[] = $row;
        }
    }
    
    return $backups;
}

function getUserInvoices($userId) {
    global $conn;
    
    $userId = (int)$userId;
    $query = "SELECT * FROM invoices WHERE user_id = $userId ORDER BY due_date DESC";
    $result = $conn->query($query);
    
    $invoices = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $invoices[] = $row;
        }
    }
    
    return $invoices;
}

function getInvoiceDetails($invoiceId, $userId) {
    global $conn;
    
    $invoiceId = (int)$invoiceId;
    $userId = (int)$userId;
    $query = "SELECT i.*, ii.* FROM invoices i
              LEFT JOIN invoice_items ii ON i.id = ii.invoice_id
              WHERE i.id = $invoiceId AND i.user_id = $userId";
    $result = $conn->query($query);
    
    $invoice = null;
    $items = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($invoice === null) {
                $invoice = [
                    'id' => $row['id'],
                    'invoice_number' => $row['invoice_number'],
                    'user_id' => $row['user_id'],
                    'amount' => $row['amount'],
                    'status' => $row['status'],
                    'due_date' => $row['due_date'],
                    'paid_date' => $row['paid_date'],
                    'created_at' => $row['created_at']
                ];
            }
            
            if ($row['invoice_id']) {
                $items[] = [
                    'id' => $row['id'],
                    'description' => $row['description'],
                    'amount' => $row['amount'],
                    'server_id' => $row['server_id']
                ];
            }
        }
        
        if ($invoice) {
            $invoice['items'] = $items;
            return $invoice;
        }
    }
    
    return null;
}

function getUserTickets($userId) {
    global $conn;
    
    $userId = (int)$userId;
    $query = "SELECT * FROM support_tickets WHERE user_id = $userId ORDER BY created_at DESC";
    $result = $conn->query($query);
    
    $tickets = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
    }
    
    return $tickets;
}

function getTicketDetails($ticketId, $userId) {
    global $conn;
    
    $ticketId = (int)$ticketId;
    $userId = (int)$userId;
    $query = "SELECT t.*, r.* FROM support_tickets t
              LEFT JOIN ticket_replies r ON t.id = r.ticket_id
              WHERE t.id = $ticketId AND t.user_id = $userId
              ORDER BY r.created_at ASC";
    $result = $conn->query($query);
    
    $ticket = null;
    $replies = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($ticket === null) {
                $ticket = [
                    'id' => $row['id'],
                    'subject' => $row['subject'],
                    'message' => $row['message'],
                    'status' => $row['status'],
                    'priority' => $row['priority'],
                    'created_at' => $row['created_at']
                ];
            }
            
            if ($row['ticket_id']) {
                $replies[] = [
                    'id' => $row['id'],
                    'message' => $row['message'],
                    'is_admin' => $row['is_admin'],
                    'created_at' => $row['created_at']
                ];
            }
        }
        
        if ($ticket) {
            $ticket['replies'] = $replies;
            return $ticket;
        }
    }
    
    return null;
}

function createTicket($userId, $subject, $message, $priority) {
    global $conn;
    
    $userId = (int)$userId;
    $subject = $conn->real_escape_string($subject);
    $message = $conn->real_escape_string($message);
    $priority = $conn->real_escape_string($priority);
    
    $query = "INSERT INTO support_tickets (user_id, subject, message, priority, status, created_at) 
              VALUES ($userId, '$subject', '$message', '$priority', 'open', NOW())";
    
    if ($conn->query($query)) {
        return $conn->insert_id;
    }
    
    return false;
}

function replyToTicket($ticketId, $userId, $message, $isAdmin = 0) {
    global $conn;
    
    $ticketId = (int)$ticketId;
    $userId = (int)$userId;
    $message = $conn->real_escape_string($message);
    $isAdmin = (int)$isAdmin;
    
    $checkQuery = "SELECT id FROM support_tickets WHERE id = $ticketId AND user_id = $userId";
    $checkResult = $conn->query($checkQuery);
    
    if ($checkResult->num_rows === 0) {
        return false;
    }
    
    $query = "INSERT INTO ticket_replies (ticket_id, message, is_admin, created_at) 
              VALUES ($ticketId, '$message', $isAdmin, NOW())";
    
    if ($conn->query($query)) {
        $updateQuery = "UPDATE support_tickets SET status = 'customer_reply' WHERE id = $ticketId";
        if ($isAdmin) {
            $updateQuery = "UPDATE support_tickets SET status = 'answered' WHERE id = $ticketId";
        }
        $conn->query($updateQuery);
        
        return $conn->insert_id;
    }
    
    return false;
}

function getUserProfile($userId) {
    global $conn;
    
    $userId = (int)$userId;
    $query = "SELECT id, first_name, last_name, email, phone, created_at 
              FROM users WHERE id = $userId";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

function updateUserProfile($userId, $data) {
    global $conn;
    
    $userId = (int)$userId;
    $fields = [];
    
    $allowedFields = ['first_name', 'last_name', 'phone', 'address', 'city', 'state', 'zip_code', 'country'];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $value = $conn->real_escape_string($data[$field]);
            $fields[] = "$field = '$value'";
        }
    }
    
    if (empty($fields)) {
        return false;
    }
    
    $fieldsStr = implode(', ', $fields);
    $query = "UPDATE users SET $fieldsStr, updated_at = NOW() WHERE id = $userId";
    
    return $conn->query($query);
}

function updateUserPassword($userId, $currentPassword, $newPassword) {
    global $conn;
    
    $userId = (int)$userId;
    
    $query = "SELECT password FROM users WHERE id = $userId";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($currentPassword, $user['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password = '$hashedPassword', updated_at = NOW() WHERE id = $userId";
            return $conn->query($updateQuery);
        }
    }
    
    return false;
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function formatDateTime($dateTime) {
    return date('F j, Y, g:i a', strtotime($dateTime));
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

