<?php
require 'config.php';
$pre = $_SESSION['pre'] ?? null;
if (!$pre) go('login.php');

$s = $pdo->prepare('SELECT secret_base32, enabled FROM user_2fa WHERE user_id=?');
$s->execute([$pre['id']]);
$row = $s->fetch();
if ($row && $row['enabled']) go('verify_2fa.php');
if (!$row) {
    $row = ['secret_base32' => random_secret_base32()];
    $pdo->prepare('INSERT INTO user_2fa (user_id,secret_base32) VALUES (?,?)')->execute([$pre['id'], $row['secret_base32']]);
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (code_ok($pre['id'], $_POST['code'] ?? '', false)) {
        $pdo->prepare('UPDATE user_2fa SET enabled=1 WHERE user_id=?')->execute([$pre['id']]);
        finish_login($pre);
    }
    $msg = bad($pre['id'], 'Wrong code. Try the current one from your app.');
}
$uri = provisioning_uri($pre['email'], ISSUER, $row['secret_base32']);
page_head('Set up Authenticator');
auth_start('qr-code-scan', 'Set up Authenticator', 'Scan the QR code with Google Authenticator or Microsoft Authenticator.', true); ?>
  <div class="text-center">
    <div id="qr">Generating QR code…</div>
    <p class="mt-3 small text-muted">Or enter this key by hand:<br><code class="user-select-all"><?= h($row['secret_base32']) ?></code></p>
  </div>
  <?= alert($msg) ?>
  <form method="post">
    <label class="form-label fw-semibold">6-digit code from the app</label>
    <input name="code" class="form-control code-input mb-3" inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code" required>
    <button class="btn btn-primary w-100 py-2">Confirm and log in</button>
  </form>
<?php auth_end(); ?>
<script src="qr_fcm.js"></script>
<script>try{renderFCMQRCode(document.getElementById('qr'),<?= json_encode($uri) ?>)}catch(e){document.getElementById('qr').textContent='QR not available, use the key below.'}</script>
<?php page_foot();
