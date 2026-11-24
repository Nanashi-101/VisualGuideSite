<?php
require 'config.php';
$pre = $_SESSION['pre'] ?? null;
if (!$pre) go('login.php');
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (code_ok($pre['id'], $_POST['code'] ?? '')) finish_login($pre);
    $msg = bad($pre['id'], 'Wrong code.');
}
page_head('Verify');
auth_start('phone', 'Authenticator code', 'Open Google Authenticator and enter the 6-digit code for ' . ISSUER . '.'); ?>
  <?= alert($msg) ?>
  <form method="post">
    <input name="code" class="form-control code-input mb-3" inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
    <button class="btn btn-primary w-100 py-2">Verify</button>
  </form>
  <p class="text-center mt-4 mb-0"><a href="login.php">Cancel</a></p>
<?php auth_end(); page_foot();
