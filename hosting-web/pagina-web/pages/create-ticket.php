<?php
$pageTitle = "Create Support Ticket";
include_once '../includes/header.php';

requireLogin();

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $priority = isset($_POST['priority']) ? trim($_POST['priority']) : 'medium';
    
    if (empty($subject) || empty($message)) {
        $error = "Subject and message are required.";
    } else {
        $ticketId = createTicket($userId, $subject, $message, $priority);
        
        if ($ticketId) {
            header("Location: ticket-details.php?id=" . $ticketId . "&created=1");
            exit;
        } else {
            $error = "Failed to create ticket. Please try again.";
        }
    }
}
?>

<section class="create-ticket">
    <div class="container">
        <div class="page-header">
            <h1>Create Support Ticket</h1>
            <p>Submit a new support ticket to our team</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="ticket-form">
            <form action="create-ticket.php" method="post">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="10" required></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="dashboard.php#tickets" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>

