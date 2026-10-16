<?php
declare(strict_types=1);
$config = require __DIR__ . '/config.php';
session_name($config['session_name']);
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
session_start();
$_SESSION['language'] ??= 'id';
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
require __DIR__ . '/translations.php';
function t(string $key): string {
    global $translations;
    return $translations[$_SESSION['language']][$key] ?? $key;
}
function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function post(string $key): string { return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : ''; }
function csrf(): void { echo '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">'; }
function redirect(string $path): never { header('Location: ' . $path, true, 303); exit; }
function db(): PDO {
    global $config;
    static $pdo;
    if (!$pdo) {
        $d = $config['database'];
        $pdo = new PDO("mysql:host={$d['host']};port={$d['port']};dbname={$d['name']};charset=utf8mb4", $d['user'], $d['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);
    }
    return $pdo;
}
function validate(array $input): array {
    global $config;
    $values = []; $errors = [];
    foreach ($config['fields'] as $field => [$type, $limit]) {
        $value = isset($input[$field]) && is_string($input[$field]) ? trim($input[$field]) : '';
        if ($value === '') { $errors[$field] = t('required'); }
        elseif ($type === 'number') {
            $min = $field === 'year' ? 1 : 0;
            if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $limit]]) === false) { $errors[$field] = t('integer') . " ($min - $limit)."; }
        } elseif (mb_strlen($value, 'UTF-8') > $limit) { $errors[$field] = t('too_long') . " $limit."; }
        $values[$field] = $value;
    }
    return [$values, $errors];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], post('csrf'))) {
        http_response_code(403); exit(e(t('expired')));
    }
    switch (post('action')) {
        case 'language':
            if (in_array(post('language'), ['id', 'en'], true)) { $_SESSION['language'] = post('language'); }
            redirect(isset($_SESSION['user']) ? 'index.php' : 'login.php');
        case 'logout':
            $_SESSION = [];
            setcookie(session_name(), '', ['expires' => time() - 42000, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
            session_destroy(); redirect('login.php');
    }
}
function header_page(string $title): void {
    global $config;
    ?><!doctype html><html lang="<?= e($_SESSION['language']) ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($title) ?> | <?= e($config['name']) ?></title><link rel="stylesheet" href="assets/style.css"><script src="assets/app.js" defer></script></head><body class="<?= e($config['theme']) ?>"><header class="top"><span class="brand"><?= e($config['name']) ?></span><nav aria-label="<?= e(t('settings')) ?>"><form method="post"><?php csrf(); ?><input type="hidden" name="action" value="language"><label class="sr" for="language"><?= e(t('language')) ?></label><select id="language" name="language" onchange="this.form.submit()"><option value="id" <?= $_SESSION['language'] === 'id' ? 'selected' : '' ?>>Bahasa Indonesia</option><option value="en" <?= $_SESSION['language'] === 'en' ? 'selected' : '' ?>>English</option></select><noscript><button type="submit"><?= e(t('apply')) ?></button></noscript></form><?php if (isset($_SESSION['user'])): ?><span class="user"><?= e($_SESSION['user']['username']) ?></span><form method="post"><?php csrf(); ?><input type="hidden" name="action" value="logout"><button><?= e(t('logout')) ?></button></form><?php endif; ?></nav></header><main><?php
}
function footer_page(): void { ?><p class="loading" role="status" hidden><?= e(t('loading')) ?></p></main><footer><?= e(t('footer')) ?></footer></body></html><?php }
