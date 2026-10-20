<?php

$judul_halaman = $t["login"];
require "header.php";
?>
<section class="login panel"><p class="section-label">BookNest</p><h1><?php echo e($t["login"]); ?></h1><p class="muted"><?php echo e($t["login_intro"]); ?></p><?php if (!empty($error)) { ?><p class="notice" role="alert"><?php echo e($error); ?></p><?php } ?><form method="post"><label for="username"><?php echo e($t["username"]); ?></label><input id="username" name="username" value="<?php echo e($username); ?>" maxlength="50" autocomplete="username" required><label for="password"><?php echo e($t["password"]); ?></label><input id="password" name="password" type="password" maxlength="72" autocomplete="current-password" required><button class="primary"><?php echo e($t["login"]); ?></button></form></section>
<?php require "footer.php"; ?>