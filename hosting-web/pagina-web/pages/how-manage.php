<?php
$pageTitle = "How to Manage Your Server";
include_once '../includes/header.php';

// Require login
requireLogin();

// Get hostname from URL parameter
$hostname = isset($_GET['hostname']) ? htmlspecialchars($_GET['hostname']) : 'your-server-ip';
?>

<section class="how-manage-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-terminal"></i> How to Manage Your Server</h1>
            <p class="lead">Learn how to connect to and manage your unmanaged server using SSH.</p>
        </div>

        <div class="server-info-card">
            <div class="server-info-header">
                <h2>Your Server Information</h2>
            </div>
            <div class="server-info-content">
                <div class="server-info-item">
                    <span class="info-label"><i class="fas fa-server"></i> Hostname/IP:</span>
                    <span class="info-value" id="server-hostname"><?php echo $hostname; ?></span>
                </div>
                <div class="server-info-item">
                    <span class="info-label"><i class="fas fa-user"></i> Username:</span>
                    <span class="info-value" id="server-username">Your username selected in Server Creation</span>
                </div>
                <div class="server-info-item">
                    <span class="info-label"><i class="fas fa-key"></i> Default Password:</span>
                    <span class="info-value">Your password selected in Server Creation</span>
                </div>
                <div class="server-info-item">
                    <span class="info-label"><i class="fas fa-door-open"></i> SSH Port:</span>
                    <span class="info-value" id="server-port">22</span>
                    <button class="copy-btn" onclick="copyToClipboard('server-port')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="connection-tabs">
            <ul class="tab-nav">
                <li class="tab-item active" data-tab="windows">
                    <i class="fab fa-windows"></i> Windows
                </li>
                <li class="tab-item" data-tab="macos">
                    <i class="fab fa-apple"></i> macOS
                </li>
                <li class="tab-item" data-tab="linux">
                    <i class="fab fa-linux"></i> Linux
                </li>
                <li class="tab-item" data-tab="web">
                    <i class="fas fa-globe"></i> Web-based
                </li>
                <li class="tab-item" data-tab="mobile">
                    <i class="fas fa-mobile-alt"></i> Mobile
                </li>
            </ul>

            <div class="tab-content">
                <!-- Windows Tab -->
                <div class="tab-pane active" id="windows">
                    <h3>Connecting from Windows</h3>
                    
                    <div class="method-card">
                        <div class="method-header">
                            <h4><i class="fas fa-download"></i> Option 1: PuTTY</h4>
                        </div>
                        <div class="method-content">
                            <ol class="instruction-list">
                                <li>
                                    <strong>Download PuTTY:</strong> 
                                    <a href="https://www.putty.org/" target="_blank" class="external-link">
                                        Download from putty.org <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </li>
                                <li><strong>Launch PuTTY</strong> after installation.</li>
                                <li>
                                    <strong>Enter connection details:</strong>
                                    <ul>
                                        <li>Host Name: <code><?php echo $hostname; ?></code></li>
                                        <li>Port: <code>22</code></li>
                                        <li>Connection type: <code>SSH</code></li>
                                    </ul>
                                </li>
                                <li><strong>Click "Open"</strong> to start the connection.</li>
                                <li>
                                    <strong>Accept the security alert</strong> (appears on first connection).
                                </li>
                                <li>
                                    <strong>Enter your credentials:</strong>
                                    <ul>
                                        <li>Username: <code>Your username selected in Server Creation</code></li>
                                        <li>Password: <code>Your password selected in Server Creation</code></li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div class="method-card">
                        <div class="method-header">
                            <h4><i class="fas fa-terminal"></i> Option 2: Windows Terminal / PowerShell</h4>
                        </div>
                        <div class="method-content">
                            <p>Windows 10/11 users can use the built-in Windows Terminal or PowerShell:</p>
                            
                            <div class="code-block">
                                <pre><code>ssh [user_created]@<?php echo $hostname; ?></code></pre>
                                <button class="copy-btn" onclick="copyCommandToClipboard('ssh [user_created]@<?php echo $hostname; ?>')">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                            
                            <p>When prompted, enter your password (it won't be visible as you type).</p>
                        </div>
                    </div>
                </div>

                <!-- macOS Tab -->
                <div class="tab-pane" id="macos">
                    <h3>Connecting from macOS</h3>
                    
                    <div class="method-card">
                        <div class="method-header">
                            <h4><i class="fas fa-terminal"></i> Using Terminal</h4>
                        </div>
                        <div class="method-content">
                            <ol class="instruction-list">
                                <li><strong>Open Terminal</strong> (Applications → Utilities → Terminal).</li>
                                <li>
                                    <strong>Enter the SSH command:</strong>
                                    <div class="code-block">
                                        <pre><code>ssh [user_created]@<?php echo $hostname; ?></code></pre>
                                        <button class="copy-btn" onclick="copyCommandToClipboard('ssh [user_created]@<?php echo $hostname; ?>')">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </li>
                                <li><strong>Accept the security warning</strong> by typing "yes" (first connection only).</li>
                                <li><strong>Enter your password</strong> when prompted (it won't be visible as you type).</li>
                            </ol>
                            
                            <div class="tip-box">
                                <h5><i class="fas fa-lightbulb"></i> Pro Tip</h5>
                                <p>For easier connections, you can set up SSH keys to avoid entering your password each time:</p>
                                <div class="code-block">
                                    <pre><code># Generate SSH key
ssh-keygen -t rsa -b 4096

# Copy key to server
ssh-copy-id [user_created]@<?php echo $hostname; ?></code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linux Tab -->
                <div class="tab-pane" id="linux">
                    <h3>Connecting from Linux</h3>
                    
                    <div class="method-card">
                        <div class="method-header">
                            <h4><i class="fas fa-terminal"></i> Using Terminal</h4>
                        </div>
                        <div class="method-content">
                            <ol class="instruction-list">
                                <li><strong>Open your terminal</strong> application.</li>
                                <li>
                                    <strong>Enter the SSH command:</strong>
                                    <div class="code-block">
                                        <pre><code>ssh [user_created]@<?php echo $hostname; ?></code></pre>
                                        <button class="copy-btn" onclick="copyCommandToClipboard('ssh [user_created]@<?php echo $hostname; ?>')">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </li>
                                <li><strong>Accept the security warning</strong> by typing "yes" (first connection only).</li>
                                <li><strong>Enter your password</strong> when prompted (it won't be visible as you type).</li>
                            </ol>
                            
                            <div class="tip-box">
                                <h5><i class="fas fa-shield-alt"></i> Security Tip</h5>
                                <p>After your first login, it's recommended to:</p>
                                <ol>
                                    <li>Change your [user_created] password</li>
                                    <li>Create a new user with sudo privileges</li>
                                    <li>Set up SSH key authentication</li>
                                    <li>Disable password authentication</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Web-based Tab -->
                <div class="tab-pane" id="web">
                    <h3>Web-based SSH Clients</h3>
                    
                    <div class="method-card">
                        <div class="method-header">
                            <h4><i class="fas fa-globe"></i> Using Web-based SSH Clients</h4>
                        </div>
                        <div class="method-content">
                            <p>If you can't install software, you can use these web-based SSH clients:</p>
                            
                            <div class="web-clients">
                                <div class="web-client-card">
                                    <h5>Wetty</h5>
                                    <p>Web-based terminal that works in any browser.</p>
                                    <a href="https://github.com/butlerx/wetty" target="_blank" class="btn btn-outline">
                                        <i class="fab fa-github"></i> Learn More
                                    </a>
                                </div>
                                
                                <div class="web-client-card">
                                    <h5>Shell In A Box</h5>
                                    <p>Browser-based terminal emulator and SSH client.</p>
                                    <a href="https://github.com/shellinabox/shellinabox" target="_blank" class="btn btn-outline">
                                        <i class="fab fa-github"></i> Learn More
                                    </a>
                                </div>
                                
                                <div class="web-client-card">
                                    <h5>GateOne</h5>
                                    <p>HTML5-powered terminal emulator and SSH client.</p>
                                    <a href="https://github.com/liftoff/GateOne" target="_blank" class="btn btn-outline">
                                        <i class="fab fa-github"></i> Learn More
                                    </a>
                                </div>
                            </div>
                            
                            <div class="note-box">
                                <p><i class="fas fa-info-circle"></i> Note: Web-based clients may require additional setup on your server.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Tab -->
                <div class="tab-pane" id="mobile">
                    <h3>Connecting from Mobile Devices</h3>
                    
                    <div class="method-card">
                        <div class="method-header">
                            <h4><i class="fab fa-android"></i> Android</h4>
                        </div>
                        <div class="method-content">
                            <p>Recommended apps for Android:</p>
                            
                            <div class="app-list">
                                <div class="app-item">
                                    <h5>JuiceSSH</h5>
                                    <p>Feature-rich SSH client with a clean interface.</p>
                                    <a href="https://play.google.com/store/apps/details?id=com.sonelli.juicessh" target="_blank" class="btn btn-sm btn-outline">
                                        <i class="fab fa-google-play"></i> Google Play
                                    </a>
                                </div>
                                
                                <div class="app-item">
                                    <h5>Termux</h5>
                                    <p>Terminal emulator and Linux environment app.</p>
                                    <a href="https://play.google.com/store/apps/details?id=com.termux" target="_blank" class="btn btn-sm btn-outline">
                                        <i class="fab fa-google-play"></i> Google Play
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="method-card">
                        <div class="method-header">
                            <h4><i class="fab fa-apple"></i> iOS</h4>
                        </div>
                        <div class="method-content">
                            <p>Recommended apps for iOS:</p>
                            
                            <div class="app-list">
                                <div class="app-item">
                                    <h5>Termius</h5>
                                    <p>Cross-platform SSH client with a modern interface.</p>
                                    <a href="https://apps.apple.com/us/app/termius-ssh-client/id549039908" target="_blank" class="btn btn-sm btn-outline">
                                        <i class="fab fa-app-store"></i> App Store
                                    </a>
                                </div>
                                
                                <div class="app-item">
                                    <h5>Prompt</h5>
                                    <p>Powerful SSH client designed for iOS.</p>
                                    <a href="https://apps.apple.com/us/app/prompt-2/id917437289" target="_blank" class="btn btn-sm btn-outline">
                                        <i class="fab fa-app-store"></i> App Store
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="common-commands-section">
            <h3><i class="fas fa-code"></i> Essential Linux Commands</h3>
            <p>Once connected, here are some useful commands to manage your server:</p>
            
            <div class="commands-grid">
                <div class="command-card">
                    <div class="command-name">ls</div>
                    <div class="command-desc">List files and directories</div>
                    <div class="command-example">
                        <code>ls -la</code>
                        <span>List all files with details</span>
                    </div>
                </div>
                
                <div class="command-card">
                    <div class="command-name">cd</div>
                    <div class="command-desc">Change directory</div>
                    <div class="command-example">
                        <code>cd /var/www</code>
                        <span>Navigate to web directory</span>
                    </div>
                </div>
                
                <div class="command-card">
                    <div class="command-name">mkdir</div>
                    <div class="command-desc">Create a new directory</div>
                    <div class="command-example">
                        <code>mkdir my_folder</code>
                        <span>Create a folder named "my_folder"</span>
                    </div>
                </div>
                
                <div class="command-card">
                    <div class="command-name">rm</div>
                    <div class="command-desc">Remove files or directories</div>
                    <div class="command-example">
                        <code>rm -rf directory_name</code>
                        <span>Delete a directory and its contents</span>
                    </div>
                </div>
                
                <div class="command-card">
                    <div class="command-name">cp</div>
                    <div class="command-desc">Copy files or directories</div>
                    <div class="command-example">
                        <code>cp file.txt backup/</code>
                        <span>Copy file.txt to backup directory</span>
                    </div>
                </div>
                
                <div class="command-card">
                    <div class="command-name">mv</div>
                    <div class="command-desc">Move or rename files</div>
                    <div class="command-example">
                        <code>mv old.txt new.txt</code>
                        <span>Rename old.txt to new.txt</span>
                    </div>
                </div>
                
                <div class="command-card">
                    <div class="command-name">nano</div>
                    <div class="command-desc">Simple text editor</div>
                    <div class="command-example">
                        <code>nano config.php</code>
                        <span>Edit config.php file</span>
                    </div>
                </div>
                
                <div class="command-card">
                    <div class="command-name">systemctl</div>
                    <div class="command-desc">Control system services</div>
                    <div class="command-example">
                        <code>systemctl restart apache2</code>
                        <span>Restart Apache web server</span>
                    </div>
                </div>
                
                <div class="command-card">
                    <div class="command-name">passwd</div>
                    <div class="command-desc">Change password</div>
                    <div class="command-example">
                        <code>passwd</code>
                        <span>Change your current user's password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabItems = document.querySelectorAll('.tab-item');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all tabs
            tabItems.forEach(tab => tab.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding tab content
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Copy to clipboard functions
    window.copyToClipboard = function(elementId) {
        const element = document.getElementById(elementId);
        const text = element.textContent;
        
        navigator.clipboard.writeText(text).then(() => {
            // Show success feedback
            const button = event.currentTarget;
            const originalHTML = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.add('copied');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('copied');
            }, 2000);
        });
    };
    
    window.copyCommandToClipboard = function(command) {
        navigator.clipboard.writeText(command).then(() => {
            // Show success feedback
            const button = event.currentTarget;
            const originalHTML = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-check"></i> Copied!';
            button.classList.add('copied');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('copied');
            }, 2000);
        });
    };
});
</script>

<style>
/* How to Manage Page Styles */
.how-manage-page {
    padding: 2rem 0;
    background-color: var(--body-bg);
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.page-header h1 {
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.page-header .lead {
    font-size: 1.2rem;
    color: var(--secondary-color);
}

/* Server Info Card */
.server-info-card {
    background-color: #fff;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
    overflow: hidden;
}

.server-info-header {
    background-color: var(--primary-color);
    color: #fff;
    padding: 1rem 1.5rem;
}

.server-info-header h2 {
    margin: 0;
    font-size: 1.5rem;
    color: #fff;
}

.server-info-content {
    padding: 1.5rem;
}

.server-info-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.server-info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-label {
    font-weight: var(--font-weight-medium);
    width: 180px;
    color: var(--dark-color);
}

.info-label i {
    margin-right: 0.5rem;
    color: var(--primary-color);
}

.info-value {
    flex: 1;
    font-family: monospace;
    background-color: var(--light-color);
    padding: 0.25rem 0.5rem;
    border-radius: var(--border-radius);
}

/* Connection Tabs */
.connection-tabs {
    background-color: #fff;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
    overflow: hidden;
}

.tab-nav {
    display: flex;
    background-color: var(--light-color);
    border-bottom: 1px solid var(--border-color);
}

.tab-item {
    padding: 1rem 1.5rem;
    cursor: pointer;
    font-weight: var(--font-weight-medium);
    transition: var(--transition);
    border-bottom: 3px solid transparent;
}

.tab-item:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.tab-item.active {
    border-bottom-color: var(--primary-color);
    color: var(--primary-color);
}

.tab-item i {
    margin-right: 0.5rem;
}

.tab-content {
    padding: 1.5rem;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.tab-pane h3 {
    margin-top: 0;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.5rem;
}

/* Method Cards */
.method-card {
    background-color: var(--light-color);
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.method-header {
    background-color: var(--primary-light);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.method-header h4 {
    margin: 0;
    color: var(--primary-color);
}

.method-header h4 i {
    margin-right: 0.5rem;
}

.method-content {
    padding: 1.5rem;
}

/* Instruction List */
.instruction-list {
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}

.instruction-list li {
    margin-bottom: 0.75rem;
}

.instruction-list li:last-child {
    margin-bottom: 0;
}

.instruction-list li strong {
    color: var(--dark-color);
}

.instruction-list ul {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

/* Code Block */
.code-block {
    background-color: var(--dark-color);
    color: #fff;
    border-radius: var(--border-radius);
    padding: 1rem;
    margin: 1rem 0;
    position: relative;
    overflow: hidden;
}

.code-block pre {
    margin: 0;
    white-space: pre-wrap;
    font-family: monospace;
}

.code-block code {
    color: #fff;
}

.copy-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: none;
    border-radius: var(--border-radius);
    padding: 0.25rem 0.5rem;
    font-size: var(--font-size-sm);
    cursor: pointer;
    transition: var(--transition);
}

.copy-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.copy-btn.copied {
    background-color: var(--success-color);
}

/* Screenshot */
.screenshot {
    margin: 1.5rem 0;
    text-align: center;
}

.screenshot img {
    max-width: 100%;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.screenshot .caption {
    margin-top: 0.5rem;
    font-size: var(--font-size-sm);
    color: var(--secondary-color);
}

/* Tip Box */
.tip-box, .note-box {
    background-color: var(--primary-light);
    border-left: 4px solid var(--primary-color);
    padding: 1rem;
    margin: 1.5rem 0;
    border-radius: 0 var(--border-radius) var(--border-radius) 0;
}

.tip-box h5, .note-box h5 {
    margin-top: 0;
    color: var(--primary-color);
}

.note-box {
    background-color: rgba(var(--info-color), 0.1);
    border-left-color: var(--info-color);
}

.note-box p {
    margin: 0;
}

/* Web Clients */
.web-clients {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.web-client-card {
    background-color: #fff;
    border-radius: var(--border-radius);
    padding: 1rem;
    box-shadow: var(--shadow-sm);
}

.web-client-card h5 {
    margin-top: 0;
    color: var(--primary-color);
}

.web-client-card p {
    margin-bottom: 1rem;
}

/* App List */
.app-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.app-item {
    background-color: #fff;
    border-radius: var(--border-radius);
    padding: 1rem;
    box-shadow: var(--shadow-sm);
}

.app-item h5 {
    margin-top: 0;
    color: var(--primary-color);
}

.app-item p {
    margin-bottom: 1rem;
}

/* Commands Section */
.common-commands-section {
    background-color: #fff;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.common-commands-section h3 {
    margin-top: 0;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.commands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

.command-card {
    background-color: var(--light-color);
    border-radius: var(--border-radius);
    padding: 1rem;
    border-left: 4px solid var(--primary-color);
}

.command-name {
    font-family: monospace;
    font-weight: var(--font-weight-bold);
    font-size: 1.2rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.command-desc {
    color: var(--secondary-color);
    margin-bottom: 0.75rem;
}

.command-example {
    background-color: var(--dark-color);
    color: #fff;
    border-radius: var(--border-radius);
    padding: 0.5rem;
}

.command-example code {
    display: block;
    margin-bottom: 0.25rem;
}

.command-example span {
    font-size: var(--font-size-sm);
    color: rgba(255, 255, 255, 0.7);
}

/* Next Steps Section */
.next-steps-section {
    background-color: #fff;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.next-steps-section h3 {
    margin-top: 0;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.step-card {
    display: flex;
    background-color: var(--light-color);
    border-radius: var(--border-radius);
    overflow: hidden;
}

.step-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    background-color: var(--primary-color);
    color: #fff;
    font-size: 1.5rem;
}

.step-content {
    flex: 1;
    padding: 1rem;
}

.step-content h4 {
    margin-top: 0;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.step-content p {
    margin-bottom: 1rem;
    font-size: var(--font-size-sm);
}

/* Help Section */
.help-section {
    background-color: #fff;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    padding: 1.5rem;
    text-align: center;
}

.help-section h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.help-section p {
    margin-bottom: 1.5rem;
}

.help-section .btn {
    margin: 0 0.5rem;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .tab-nav {
        flex-wrap: wrap;
    }
    
    .tab-item {
        flex: 1 0 33.333%;
        text-align: center;
        padding: 0.75rem;
    }
    
    .tab-item i {
        margin-right: 0;
        display: block;
        margin-bottom: 0.25rem;
        font-size: 1.25rem;
    }
    
    .commands-grid, .steps-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .tab-item {
        flex: 1 0 50%;
    }
    
    .server-info-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .info-label {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        width: 100%;
    }
    
    .copy-btn {
        position: static;
        margin-top: 0.5rem;
        width: 100%;
    }
    
    .help-section .btn {
        display: block;
        margin: 0.5rem 0;
    }
}
</style>

<?php include_once '../includes/footer.php'; ?>
