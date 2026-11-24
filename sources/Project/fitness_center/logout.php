<?php
// Logging out also needs the Authenticator code, as the assignment asks.
require 'config.php';
require_login();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (code_ok(me()['id'], $_POST['code'] ?? '')) {
        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        go('login.php');
    }
    $msg = bad(me()['id'], 'Wrong code. You are still logged in.');
}
page_head('Logout');
auth_start('box-arrow-right', 'Confirm logout', 'Enter the current code from Google Authenticator to log out.'); ?>
  <?= alert($msg) ?>
  <form method="post">
    <input name="code" class="form-control code-input mb-3" inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
    <button class="btn btn-primary w-100 py-2">Log out</button>
  </form>
  <p class="text-center mt-4 mb-0"><a href="dashboard.php">Stay logged in</a></p>
<?php auth_end(); page_foot();
