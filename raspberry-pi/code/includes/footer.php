<?php
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php'):
?>
        </main>
    </div>

    <footer class="app-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Attendance Control System. All rights reserved.</p>
        </div>
    </footer>
<?php endif; ?>

    <!-- Common Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles-engine"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles"></script>
    <script src="js/common.js"></script>
    
    <!-- Page specific JS -->
    <?php if (isset($page_js)): ?>
    <script src="<?php echo $page_js; ?>"></script>
    <?php endif; ?>
</body>
</html>

