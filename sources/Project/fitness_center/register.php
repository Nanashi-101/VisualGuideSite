<?php
require 'config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $msg = 'Invalid email.';
    elseif (strlen($pass) < 8) $msg = 'Password needs at least 8 characters.';
    else {
        // The very first account becomes the admin, everyone after is a member.
        $role = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() == 0 ? 'admin' : 'member';
        try {
            $pdo->prepare('INSERT INTO users (email,password_hash,role) VALUES (?,?,?)')
                ->execute([$email, password_hash($pass, PASSWORD_DEFAULT), $role]);
            go('login.php?registered=1');
        } catch (PDOException $e) {
            $msg = $e->getCode() == 23000 ? 'This email is already registered.' : 'Registration failed.';
        }
    }
}
page_head('Join');
auth_start('person-plus', 'Join the gym', 'Create your account in seconds.'); ?>
  <?= alert($msg) ?>
  <form method="post">
    <div class="form-floating mb-3"><input type="email" name="email" class="form-control" id="e" placeholder="Email" required><label for="e">Email</label></div>
    <div class="form-floating mb-3"><input type="password" name="password" class="form-control" id="p" placeholder="Password" minlength="8" required><label for="p">Password (min. 8 characters)</label></div>
    <button class="btn btn-primary w-100 py-2">Create account</button>
  </form>
  <p class="text-center mt-4 mb-0">Already a member? <a href="login.php">Signin</a></p>
<?php auth_end(); page_foot();
