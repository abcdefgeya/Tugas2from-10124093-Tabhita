<!doctype html>
<html lang="<?php echo e($_SESSION["language"]); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo e($judul_halaman); ?> | BookNest</title>
<link rel="stylesheet" href="assets/style.css">
<script src="assets/app.js" defer></script>
</head>
<body class="booknest">
<header class="top">
<span class="brand">BookNest</span>
<nav aria-label="<?php echo e($t["settings"]); ?>">
<form method="post">
<input type="hidden" name="action" value="language">
<label class="sr" for="language"><?php echo e($t["language"]); ?></label>
<select id="language" name="language" onchange="this.form.submit()">
<option value="id" <?php if ($_SESSION["language"] == "id") { echo "selected"; } ?>>Bahasa Indonesia</option>
<option value="en" <?php if ($_SESSION["language"] == "en") { echo "selected"; } ?>>English</option>
</select>
<noscript><button type="submit"><?php echo e($t["apply"]); ?></button></noscript>
</form>
<?php if (isset($_SESSION["user"])) { ?>
<span class="user"><?php echo e($_SESSION["user"]["username"]); ?></span>
<form method="post">
<input type="hidden" name="action" value="logout">
<button><?php echo e($t["logout"]); ?></button>
</form>
<?php } ?>
</nav>
</header>
<main>