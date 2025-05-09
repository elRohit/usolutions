<?php
$pageTitle = "Dashboard";
include_once '../includes/header.php';

requireLogin();

$userId = $_SESSION['user_id'];
$user = getUserProfile($userId);

$servers = getUserServers($userId);

$invoices = getUserInvoices($userId);

$tickets = getUserTickets($userId);
?>

<section class="dashboard">
   <div class="dashboard-container">
       <div class="dashboard-welcome">
           <h1>Welcome, <?php echo $user['first_name']; ?>!</h1>
           <p>Manage your servers from your personal dashboard.</p>
           <div class="dashboard-date"></div>
       </div>
       
       <div class="dashboard-layout">
           <div class="dashboard-menu">
               <div class="user-profile">
                   <div class="user-profile-info">
                       <div class="user-avatar">
                           <i class="fas fa-user"></i>
                       </div>
                       <div class="user-details">
                           <h3><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h3>
                           <p><?php echo $user['email']; ?></p>
                       </div>
                   </div>
               </div>
               
               <ul class="menu-list">
                   <li class="menu-item">
                       <a href="#servers" class="menu-link">
                           <i class="fas fa-server"></i>
                           <span>My Servers</span>
                       </a>
                   </li>
                   <li class="menu-item">
                       <a href="profile.php" class="menu-link" onclick="window.location.href='profile.php'; return false;">
                           <i class="fas fa-user-cog"></i>
                           <span>Account Settings</span>
                       </a>
                   </li>
               </ul>
           </div>
           
           <div class="dashboard-main">
               <div class="dashboard-section" id="overview">
                   <div class="stats-grid">
                       <div class="stat-card">
                           <div class="stat-header">
                               <h3 class="stat-title">Active Servers</h3>
                               <div class="stat-icon blue">
                                   <i class="fas fa-server"></i>
                               </div>
                           </div>
                           <div class="stat-number"><?php echo count(array_filter($servers, function($s) { return $s['server_status'] === 'active'; })); ?></div>
                           <div class="stat-trend up">
                               <i class="fas fa-arrow-up"></i> Active and running
                           </div>
                       </div>
                       
                       <div class="stat-card">
                           <div class="stat-header">
                               <h3 class="stat-title">Account Age</h3>
                               <div class="stat-icon red">
                                   <i class="fas fa-calendar-alt"></i>
                               </div>
                           </div>
                           <div class="stat-number"><?php echo floor((time() - strtotime($user['created_at'])) / (60 * 60 * 24)); ?></div>
                           <div class="stat-trend up">
                               <i class="fas fa-arrow-up"></i> Days with us
                           </div>
                       </div>
                   </div>
               </div>
               
               <div class="dashboard-section" id="servers">
                   <div class="content-panel">
                       <div class="panel-header">
                           <h2 class="panel-title">My Servers</h2>
                           <div class="panel-actions">
                               <button class="panel-btn" data-action="refresh" title="Refresh">
                                   <i class="fas fa-sync-alt"></i>
                               </button>
                               <a href="servers.php" class="btn server-btn server-btn-primary">
                                   <i class="fas fa-plus"></i> Order New Server
                               </a>
                           </div>
                       </div>
                       
                       <div class="panel-content">
                           <?php if (empty($servers)): ?>
                               <div class="empty-state">
                                   <div class="empty-icon">
                                       <i class="fas fa-server"></i>
                                   </div>
                                   <h3 class="empty-title">No Servers Yet</h3>
                                   <p class="empty-desc">You haven't purchased any servers yet. Explore our server options and get started!</p>
                                   <a href="servers.php" class="empty-btn">
                                       <i class="fas fa-plus"></i> Browse Servers
                                   </a>
                               </div>
                           <?php else: ?>
                               <div class="servers-grid">
                                   <?php foreach ($servers as $server): ?>
                                       <div class="server-card">
                                           <div class="server-header">
                                               <h3 class="server-name"><?php echo $server['server_name']; ?></h3>
                                               <div class="server-status <?php echo strtolower($server['server_status']); ?>">
                                                   <i class="fas fa-circle"></i>
                                                   <?php echo ucfirst($server['server_status']); ?>
                                               </div>
                                           </div>
                                           
                                           <div class="server-details">
                                               <div class="server-detail">
                                                   <span class="detail-label">Type:</span>
                                                   <span class="detail-value"><?php echo ucfirst($server['service_type']); ?></span>
                                               </div>
                                               <div class="server-detail">
                                                   <span class="detail-label">Plan:</span>
                                                   <span class="detail-value"><?php echo $server['plan_name']; ?></span>
                                               </div>
                                               <div class="server-detail">
                                                   <span class="detail-label">IP:</span>
                                                   <span class="detail-value"><?php echo $server['hostname'] ?? 'Not assigned yet'; ?></span>
                                               </div>
                                               <?php if ($server['service_type'] === 'managed'): ?>
                                                    <?php if (stripos($server['server_name'], 'wordpress') === 0):?>
                                                        <div class="server-detail">
                                                            <span class="detail-label">First User:</span>
                                                            <span class="detail-value">admin</span>
                                                        </div>
                                                        <div class="server-detail">
                                                            <span class="detail-label">First Password:</span>
                                                            <span class="detail-value">
                                                                <button class="server-btn server-btn-primary password-copy-btn" data-password="P@ssw0rd">
                                                                    <i class="fas fa-key"></i> Copy Password
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div class="server-detail responsibility-note">
                                                            Take responsability if you change password.
                                                        </div>
                                                    <?php elseif (stripos($server['server_name'], 'prestashop') === 0): ?>
                                                        <div class="server-detail">
                                                            <span class="detail-label">First User:</span>
                                                            <span class="detail-value">prestashop@usolutions.cat</span>
                                                        </div>
                                                        <div class="server-detail">
                                                            <span class="detail-label">First Password:</span>
                                                            <span class="detail-value">
                                                                <button class="server-btn server-btn-primary password-copy-btn" data-password="q%mF$Dg=e[3}]WJ">
                                                                    <i class="fas fa-key"></i> Copy Password
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div class="server-detail responsibility-note">
                                                            Take responsability if you change password.
                                                        </div>
                                                    <?php endif; ?>
                                                <?php elseif ($server['service_type'] === 'unmanaged'): ?>
                                                    <div class="server-detail">
                                                        <span class="detail-label">User:</span>
                                                        <span class="detail-value">Personalized</span>
                                                    </div>
                                                    <div class="server-detail">
                                                        <span class="detail-label">Password:</span>
                                                        <span class="detail-value">Personalized</span>
                                                    </div>
                                                <?php endif; ?>
                                               <div class="server-detail">
                                                   <span class="detail-label">Expires:</span>
                                                   <span class="detail-value"><?php echo formatDate($server['expiry_date']); ?></span>
                                               </div>
                                           </div>
                                           
                                           <div class="server-actions">
                                               <?php if ($server['server_status'] === 'active' && !empty($server['hostname'])): ?>
                                                   
                                                   <?php if ($server['service_type'] === 'managed'):

                                                       if (stripos($server['server_name'], 'wordpress') === 0):?>
                                                           <a href="http://<?php echo $server['hostname']; ?>" target="_blank" class="btn server-btn server-btn-primary">
                                                               <i class="fas fa-globe"></i> Visit Site
                                                           </a>
                                                           <a href="http://<?php echo $server['hostname']; ?>/wordpress/wp-admin" target="_blank" class="btn server-btn server-btn-secondary">
                                                               <i class="fas fa-cogs"></i> Admin Panel
                                                           </a> <?php
                                                       elseif (stripos($server['server_name'], 'prestashop') === 0): ?>
                                                           <a href="http://<?php echo $server['hostname']; ?>" target="_blank" class="btn server-btn server-btn-primary">
                                                               <i class="fas fa-globe"></i> Visit Site
                                                           </a>
                                                           <?php
                                                               $server_ip = $server['hostname'];
                                                               $ver_admin_page = "sshpass -p 'P@ssw0rd' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null root@$server_ip \" ls -d /var/www/html/prestashop/admin*/ \"";
                                                               $output = shell_exec($ver_admin_page . "2>&1");
                                                               $output = trim($output);
                                                               if (preg_match('/\/admin[^\s\/]*/', $output, $matches)) {
                                                                   $output = $matches[0];
                                                               } 
                                                           ?>
                                                           <a href="http://<?php echo $server['hostname'] ."/prestashop". $output; ?>" target="_blank" class="btn server-btn server-btn-secondary">
                                                               <i class="fas fa-cogs"></i> Admin Panel
                                                           </a>
                                                           <?php endif;
                                                   elseif($server['service_type'] === 'unmanaged'): ?>
                                                       
                                                       <a href="how-manage.php?hostname=<?php echo $server['hostname']; ?>" class="btn server-btn server-btn-primary">
                                                           <i class="fas fa-terminal"></i> How to Manage?
                                                       </a>
                                                   <?php endif; ?>
                                               <?php endif; ?>
                                           </div>
                                       </div>
                                   <?php endforeach; ?>
                               </div>
                           <?php endif; ?>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
</section>

<style>
    .responsibility-note {
        text-align: right;
        font-size: 12px;
        color: #666;
        font-style: italic;
        padding-right: 10px;
    }
    
    .copy-success {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        font-weight: 500;
        animation: fadeInOut 2s ease;
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(-20px); }
        15% { opacity: 1; transform: translateY(0); }
        85% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-20px); }
    }
    
    .password-copy-btn {
        padding: 6px 12px;
        font-size: 13px;
    }
</style>

<div class="copy-success" id="copySuccess">Password copied to clipboard!</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButtons = document.querySelectorAll('.password-copy-btn');
        const copySuccess = document.getElementById('copySuccess');
        
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const password = this.getAttribute('data-password');
                
                const textarea = document.createElement('textarea');
                textarea.value = password;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'absolute';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                
                textarea.select();
                document.execCommand('copy');
                
                document.body.removeChild(textarea);
                
                copySuccess.style.display = 'block';
                
                setTimeout(() => {
                    copySuccess.style.display = 'none';
                }, 2000);
            });
        });
    });
</script>
<?php include_once '../includes/footer.php'; ?>
