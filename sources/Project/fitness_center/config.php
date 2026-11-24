<?php
// Shared by every page: DB connection, secure session, helpers, page layout.
$pdo = new PDO('mysql:host=127.0.0.1;dbname=fitness_center;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]); // shortcut: root without password is XAMPP's default, use a dedicated DB user when deployed
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
session_start();
require_once __DIR__ . '/totp.php';

const ISSUER = 'Kraftwerk Fitness';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES); }
function go($url) { header("Location: $url"); exit; }
function me() { return $_SESSION['user'] ?? null; }
function require_login() { if (!me()) go('login.php'); }
function require_admin() { require_login(); if (me()['role'] !== 'admin') { http_response_code(403); exit('Admins only.'); } }

// Password and code are both right: finish the login.
function finish_login($u) {
    session_regenerate_id(true);
    $_SESSION = ['user' => ['id' => $u['id'], 'email' => $u['email'], 'role' => $u['role']]];
    go('dashboard.php');
}

function page_head($title) {
    $u = me();
    $links = $u
        ? '<li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>'
          . ($u['role'] === 'admin' ? '<li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>' : '')
          . '<li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-outline-light px-3" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>'
        : '<li class="nav-item"><a class="nav-link" href="login.php">Sigin</a></li>'
          . '<li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-primary px-4" href="register.php">Join now</a></li>';
    $v = filemtime(__DIR__ . '/style.css'); // new version number on every edit, so browsers never keep an old stylesheet
    echo '<!doctype html><html lang="en" data-bs-theme="dark"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>' . h($title) . ' – ' . ISSUER . '</title>'
       . '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="preconnect" href="https://images.unsplash.com">'
       . '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">'
       . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
       . '<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">'
       . '<link href="style.css?v=' . $v . '" rel="stylesheet"><script>document.documentElement.classList.add("js")</script></head><body>'
       . '<nav class="navbar navbar-expand-lg navbar-dark fixed-top site-nav"><div class="container">'
       . '<a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-lightning-charge-fill text-primary"></i> Kraftwerk</a>'
       . '<button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Open menu"><span class="navbar-toggler-icon"></span></button>'
       . '<div class="collapse navbar-collapse" id="nav"><ul class="navbar-nav ms-auto align-items-lg-center">'
       . '<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>'
       . '<li class="nav-item"><a class="nav-link" href="index.php#programs">Programs</a></li>'
       . '<li class="nav-item"><a class="nav-link" href="index.php#plans">Plans</a></li>'
       . '<li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>' . $links
       . '</ul></div></div></nav><main>';
}
function page_foot() {
    echo '</main><footer class="site-footer pt-5 pb-4"><div class="container"><div class="row g-4 pb-4 border-bottom border-secondary-subtle border-opacity-25">'
       . '<div class="col-lg-5"><div class="h4 text-white mb-3"><i class="bi bi-lightning-charge-fill text-primary"></i> Kraftwerk Fitness</div>'
       . '<p class="mb-3">Strength, conditioning and community in the heart of Munich. Open 24/7 for members.</p>'
       . '<a class="social me-2" href="https://instagram.com" aria-label="Instagram"><i class="bi bi-instagram"></i></a>'
       . '<a class="social me-2" href="https://facebook.com" aria-label="Facebook"><i class="bi bi-facebook"></i></a>'
       . '<a class="social" href="https://youtube.com" aria-label="YouTube"><i class="bi bi-youtube"></i></a></div>'
       . '<div class="col-6 col-lg-3"><div class="h6 text-white">Explore</div><ul class="list-unstyled mb-0">'
       . '<li class="mb-2"><a href="index.php#programs">Programs</a></li><li class="mb-2"><a href="index.php#plans">Memberships</a></li><li><a href="index.php#contact">Contact</a></li></ul></div>'
       . '<div class="col-6 col-lg-4"><div class="h6 text-white">Visit us</div><p class="mb-1"><i class="bi bi-geo-alt text-primary"></i> Musterstraße 12, 80331 München</p>'
       . '<p class="mb-0"><i class="bi bi-clock text-primary"></i> Staffed Mon–Fri 7:00–22:00</p></div></div>'
       . '<div class="d-flex flex-wrap justify-content-between gap-2 pt-3 small"><span>&copy; ' . date('Y') . ' Kraftwerk Fitness München · Built for education (Computer &amp; Media Lab)</span><span>Photos: <a href="https://unsplash.com" rel="noopener">Unsplash</a></span></div></div></footer>'
       . '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>'
       . '<script src="site.js?v=' . filemtime(__DIR__ . '/site.js') . '"></script></body></html>';
}

// Centered card used by login, register and the 2FA pages.
function auth_start($icon, $title, $sub, $wide = false) {
    echo '<div class="auth-wrap"><div class="container"><div class="row justify-content-center"><div class="col-md-' . ($wide ? '8 col-lg-6' : '6 col-lg-5') . '">'
       . '<div class="card auth-card p-4 p-md-5"><div class="text-center mb-4"><div class="display-4 text-primary"><i class="bi bi-' . $icon . '"></i></div>'
       . '<h2 class="fw-bold">' . $title . '</h2><p class="text-muted mb-0">' . $sub . '</p></div>';
}
function auth_end() { echo '</div></div></div></div></div>'; }
function alert($msg, $type = 'danger') { return $msg ? '<div class="alert alert-' . $type . ' py-2">' . h($msg) . '</div>' : ''; }

const MAX_FAILS = 5, LOCK_MINUTES = 15;

function is_locked($uid) {
    global $pdo;
    $s = $pdo->prepare('SELECT locked_until > NOW() FROM users WHERE id=?');
    $s->execute([$uid]);
    return (bool)$s->fetchColumn();
}
// Count a wrong password or code. The 5th one in a row locks the account for 15 minutes.
function register_fail($uid) {
    global $pdo;
    $pdo->prepare('UPDATE users SET failed_count = failed_count + 1,
                   locked_until = IF(failed_count >= ?, NOW() + INTERVAL ' . LOCK_MINUTES . ' MINUTE, locked_until),
                   failed_count = IF(failed_count >= ?, 0, failed_count) WHERE id=?')->execute([MAX_FAILS, MAX_FAILS, $uid]);
}
function bad($uid, $text) { return is_locked($uid) ? 'Too many attempts. Try again in ' . LOCK_MINUTES . ' minutes.' : $text; }

// Is $code the current 6-digit Authenticator code of this user? ($enabledOnly: false while setting up)
// A locked account never passes. A wrong code counts as a failure, a right one clears the counter.
function code_ok($uid, $code, $enabledOnly = true) {
    global $pdo;
    if (is_locked($uid)) return false;
    $s = $pdo->prepare('SELECT secret_base32 FROM user_2fa WHERE user_id=?' . ($enabledOnly ? ' AND enabled=1' : ''));
    $s->execute([$uid]);
    $secret = $s->fetchColumn();
    $ok = $secret && verify_totp($secret, $code);
    if ($ok) $pdo->prepare('UPDATE users SET failed_count=0 WHERE id=?')->execute([$uid]);
    else register_fail($uid);
    return $ok;
}
