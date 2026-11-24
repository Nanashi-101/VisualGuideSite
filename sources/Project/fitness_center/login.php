<?php
require 'config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = $pdo->prepare('SELECT id,email,role,password_hash FROM users WHERE email=?');
    $s->execute([trim($_POST['email'] ?? '')]);
    $u = $s->fetch();
    if ($u && is_locked($u['id'])) $msg = bad($u['id'], '');
    elseif ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION = ['pre' => ['id' => $u['id'], 'email' => $u['email'], 'role' => $u['role']]]; // password OK, code still missing
        $s = $pdo->prepare('SELECT enabled FROM user_2fa WHERE user_id=?');
        $s->execute([$u['id']]);
        go($s->fetchColumn() ? 'verify_2fa.php' : 'setup_2fa.php'); // no 2FA yet: set it up now, it is mandatory
    }
    else { if ($u) register_fail($u['id']); $msg = $u ? bad($u['id'], 'Wrong email or password.') : 'Wrong email or password.'; }
}
page_head('Login');
auth_start('shield-lock', 'Welcome back', 'Log in to your member area.'); ?>
  <?php if (isset($_GET['registered'])): ?><div class="alert alert-success py-2">Account created. Log in to set up your Authenticator.</div><?php endif; ?>
  <?= alert($msg) ?>
  <form method="post">
    <div class="form-floating mb-3"><input type="email" name="email" class="form-control" id="e" placeholder="Email" required><label for="e">Email</label></div>
    <div class="form-floating mb-3"><input type="password" name="password" class="form-control" id="p" placeholder="Password" required><label for="p">Password</label></div>
    <button class="btn btn-primary w-100 py-2">Signin</button>
  </form>
  <p class="text-center mt-4 mb-0">New here? <a href="register.php">Join now</a></p>
<?php auth_end(); page_foot();
