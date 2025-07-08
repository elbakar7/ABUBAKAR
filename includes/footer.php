    </main>
    
    <!-- Footer -->
    <footer class="bg-light text-center py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-start">
                    <p class="mb-0">
                        <i class="fas fa-mosque me-2 text-primary"></i>
                        <strong>Madrassah Management System</strong>
                    </p>
                    <small class="text-muted">Empowering Islamic Education</small>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">
                        <small class="text-muted">
                            &copy; <?php echo date('Y'); ?> All rights reserved.
                        </small>
                    </p>
                    <small class="text-muted">
                        Built with <i class="fas fa-heart text-danger"></i> for Islamic Education
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery for AJAX functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JavaScript -->
    <script src="/assets/js/main.js"></script>
    
    <?php if (isset($additionalScripts) && is_array($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($pageScripts)): ?>
        <script>
        <?php echo $pageScripts; ?>
        </script>
    <?php endif; ?>
</body>
</html>