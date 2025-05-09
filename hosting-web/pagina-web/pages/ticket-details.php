<?php
$pageTitle = "Ticket Details";
include_once '../includes/header.php';

requireLogin();

$userId = $_SESSION['user_id'];
$ticketId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$created = isset($_GET['created']) ? true : false;

$ticket = getTicketDetails($ticketId, $userId);

if (!$ticket) {
    header("Location: dashboard.php#tickets");
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($message)) {
        $error = "Reply message is required.";
    } else {
        $replyId = replyToTicket($ticketId, $userId, $message);
        
        if ($replyId) {
            $success = "Reply sent successfully.";
            $ticket = getTicketDetails($ticketId, $userId);
        } else {
            $error = "Failed to send reply. Please try again.";
        }
    }
}
?>

<section class="ticket-details">
    <div class="container">
        <div class="page-header">
            <h1>Ticket #<?php echo $ticketId; ?></h1>
            <div class="ticket-status">
                <span class="status-badge <?php echo strtolower($ticket['status']); ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                </span>
            </div>
        </div>
        
        <?php if ($created): ?>
            <div class="alert alert-success">
                <p>Ticket created successfully. Our support team will respond as soon as possible.</p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="ticket-container">
            <div class="ticket-header">
                <div class="ticket-info">
                    <h2><?php echo $ticket['subject']; ?></h2>
                    <div class="ticket-meta">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo formatDateTime($ticket['created_at']); ?></span>
                        <span><i class="fas fa-flag"></i> Priority: <?php echo ucfirst($ticket['priority']); ?></span>
                    </div>
                </div>
                <div class="ticket-actions">
                    <a href="dashboard.php#tickets" class="btn btn-secondary">Back to Tickets</a>
                </div>
            </div>
            
            <div class="ticket-messages">
                <div class="message customer">
                    <div class="message-header">
                        <div class="message-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-name">
                                <h4><?php echo $_SESSION['user_name']; ?></h4>
                                <span>Customer</span>
                            </div>
                        </div>
                        <div class="message-date">
                            <?php echo formatDateTime($ticket['created_at']); ?>
                        </div>
                    </div>
                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($ticket['message'])); ?>
                    </div>
                </div>
                
                <?php if (!empty($ticket['replies'])): ?>
                    <?php foreach ($ticket['replies'] as $reply): ?>
                        <div class="message <?php echo $reply['is_admin'] ? 'admin' : 'customer'; ?>">
                            <div class="message-header">
                                <div class="message-author">
                                    <div class="author-avatar">
                                        <i class="fas <?php echo $reply['is_admin'] ? 'fa-headset' : 'fa-user'; ?>"></i>
                                    </div>
                                    <div class="author-name">
                                        <h4><?php echo $reply['is_admin'] ? 'Support Team' : $_SESSION['user_name']; ?></h4>
                                        <span><?php echo $reply['is_admin'] ? 'Support Agent' : 'Customer'; ?></span>
                                    </div>
                                </div>
                                <div class="message-date">
                                    <?php echo formatDateTime($reply['created_at']); ?>
                                </div>
                            </div>
                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($reply['message'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if ($ticket['status'] !== 'closed'): ?>
                <div class="reply-form">
                    <h3>Reply to Ticket</h3>
                    <form action="ticket-details.php?id=<?php echo $ticketId; ?>" method="post">
                        <div class="form-group">
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Reply</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="ticket-closed">
                    <p>This ticket is closed. If you need further assistance, please create a new ticket.</p>
                    <a href="create-ticket.php" class="btn btn-primary">Create New Ticket</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>

