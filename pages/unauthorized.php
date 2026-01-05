<?php
require_once __DIR__ . '/../config/auth.php';
requireLogin();
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12 text-center py-5">
            <div class="card border-danger">
                <div class="card-body py-5">
                    <i class="bi bi-shield-exclamation" style="font-size: 5rem; color: #dc3545;"></i>
                    <h2 class="mt-4 text-danger">Access Denied</h2>
                    <p class="lead">You don't have permission to access this page.</p>
                    <p class="text-muted">Your current role: <strong><?php echo ucfirst(getCurrentRole()); ?></strong></p>
                    <a href="index.php" class="btn btn-primary mt-3">
                        <i class="bi bi-house"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

