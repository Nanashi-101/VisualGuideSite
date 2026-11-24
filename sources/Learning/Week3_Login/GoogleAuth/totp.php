<?php
// totp.php — Google Authenticator compatible TOTP without external libs

function base32_encode_no_padding($data) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $bits = '';
    $out = '';
    for ($i=0; $i<strlen($data); $i++) {
        $bits .= str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);
    }
    $chunks = str_split($bits, 5);
    foreach ($chunks as $c) {
        if (strlen($c) < 5) $c = str_pad($c, 5, '0');
        $out .= $alphabet[bindec($c)];
    }
    return $out;
}

function base32_decode($b32) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $b32 = strtoupper($b32);
    $bits = '';
    $out = '';
    for ($i=0; $i<strlen($b32); $i++) {
        $pos = strpos($alphabet, $b32[$i]);
        if ($pos === false) continue;
        $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
    }
    $bytes = str_split($bits, 8);
    foreach ($bytes as $byte) {
        if (strlen($byte) === 8) $out .= chr(bindec($byte));
    }
    return $out;
}

function random_secret_base32($length = 20) { // 20 bytes -> ~32 Base32 chars
    $bytes = random_bytes($length);
    return rtrim(base32_encode_no_padding($bytes), '=');
}

function hotp($secret, $counter) {
    $secret_bin = base32_decode($secret);
    $bin_counter = pack('N*', 0) . pack('N*', $counter); // 8 bytes big-endian
    $hash = hash_hmac('sha1', $bin_counter, $secret_bin, true);
    $offset = ord(substr($hash, -1)) & 0x0F;
    $truncated = (ord($hash[$offset]) & 0x7f) << 24
               | (ord($hash[$offset+1]) & 0xff) << 16
               | (ord($hash[$offset+2]) & 0xff) << 8
               | (ord($hash[$offset+3]) & 0xff);
    return $truncated % 1000000;
}

function totp($secret, $timeStep = 30, $t0 = 0) {
    $counter = floor((time() - $t0) / $timeStep);
    return hotp($secret, $counter);
}

function verify_totp($secret, $code, $window = 1) {
    $code = str_pad(preg_replace('/\D/', '', $code), 6, '0', STR_PAD_LEFT);
    $timeStep = 30;
    $counter = floor(time() / $timeStep);
    for ($i = -$window; $i <= $window; $i++) {
        if (sprintf('%06d', hotp($secret, $counter + $i)) === $code) return true;
    }
    return false;
}

function provisioning_uri($accountName, $issuer, $secret) {
    $label = rawurlencode($issuer . ':' . $accountName);
    $issuerQ = rawurlencode($issuer);
    return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuerQ}&algorithm=SHA1&digits=6&period=30";
}

// Provider 1 (recommended): goQR / qrserver
function qr_url_primary($data) {
    return "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($data);
}
// Provider 2 (fallback): Google Charts
function qr_url_fallback($data) {
    return "https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=" . urlencode($data);
}
