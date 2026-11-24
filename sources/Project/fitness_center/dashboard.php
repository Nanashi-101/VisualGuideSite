<?php
require 'config.php';
require_login();
$admin = me()['role'] === 'admin';
page_head('Dashboard'); ?>
<header class="page-head"><div class="container">
    <span class="badge bg-primary mb-2"><?= $admin ? 'Admin' : 'Member' ?></span>
    <h1 class="fw-bold text-break">Hello, <?= h(me()['email']) ?></h1>
    <p class="text-white-75 mb-0">You are logged in with two-factor authentication.</p>
  </div></header>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-md-6" data-reveal="up"><div class="feature p-4 h-100"><div class="icon mb-3"><i class="bi bi-shield-check"></i></div><h5 class="fw-bold">Account secured</h5><p class="text-muted mb-0">Logging in and out needs a code from your Authenticator app.</p></div></div>
    <?php if ($admin): ?>
    <div class="col-md-6" data-reveal="up" style="--d:.1s"><a href="admin.php" class="text-decoration-none text-reset"><div class="feature p-4 h-100"><div class="icon mb-3"><i class="bi bi-speedometer2"></i></div><h5 class="fw-bold">Admin panel</h5><p class="text-muted mb-0">See all users, manage members, plans and messages.</p></div></a></div>
    <?php endif; ?>
  </div>
</div>
<?php page_foot();
