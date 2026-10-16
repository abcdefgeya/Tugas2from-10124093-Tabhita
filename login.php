<?php
require __DIR__ . '/app.php';
if (isset($_SESSION['user'])) { redirect('index.php'); }
$error = ''; $username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = post('username'); $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    if ($username === '' || $password === '') { $error = t('required'); }
    elseif (mb_strlen($username) > 50 || strlen($password) > 72) { $error = t('login_limits'); }
    else {
        try {
            $query = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
            $query->execute([$username]); $user = $query->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true); $_SESSION['csrf'] = bin2hex(random_bytes(32));
                $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username']];
                redirect('index.php');
            } else { $error = t('invalid_login'); }
        } catch (PDOException $exception) { error_log($exception->getMessage()); $error = t('db_error'); }
    }
}
header_page(t('login'));
?><section class="login panel"><p class="section-label"><?= e($config['name']) ?></p><h1><?= e(t('login')) ?></h1><p class="muted"><?= e(t('login_intro')) ?></p><?php if ($error): ?><p class="notice" role="alert"><?= e($error) ?></p><?php endif; ?><form method="post"><?php csrf(); ?><label for="username"><?= e(t('username')) ?></label><input id="username" name="username" value="<?= e($username) ?>" maxlength="50" autocomplete="username" required><label for="password"><?= e(t('password')) ?></label><input id="password" name="password" type="password" maxlength="72" autocomplete="current-password" required><button class="primary"><?= e(t('login')) ?></button></form></section><?php footer_page();
