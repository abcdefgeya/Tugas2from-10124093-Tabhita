<?php
require __DIR__ . '/app.php';
if (!isset($_SESSION['user'])) { redirect('login.php'); }
$table = $config['table']; // Table and column names come only from the local config.
$fields = array_keys($config['fields']);
$view = isset($_GET['view']) && is_string($_GET['view']) ? $_GET['view'] : 'list';
if (!in_array($view, ['list', 'new', 'edit', 'delete'], true)) { $view = 'list'; }
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$values = array_fill_keys($fields, ''); $errors = []; $error = ''; $rows = []; $record = null;
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
try {
    $pdo = db();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_var(post('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        switch (post('action')) {
            case 'save':
                $view = post('id') === '' ? 'new' : 'edit';
                [$values, $errors] = validate($_POST);
                if ($view === 'edit' && !$id) { $error = t('missing'); }
                if (!$errors && !$error) {
                    if ($view === 'edit') {
                        $query = $pdo->prepare("SELECT id FROM $table WHERE id = ?"); $query->execute([$id]);
                        if (!$query->fetch()) { $error = t('missing'); }
                    }
                    if (!$error) {
                        if ($view === 'new') {
                            $columns = implode(', ', $fields); $placeholders = implode(', ', array_fill(0, count($fields), '?'));
                            $query = $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
                            $query->execute(array_values($values));
                        } else {
                            $columns = implode(', ', array_map(fn($field) => "$field = ?", $fields));
                            $query = $pdo->prepare("UPDATE $table SET $columns WHERE id = ?");
                            $query->execute([...array_values($values), $id]);
                        }
                        $_SESSION['flash'] = t('saved'); redirect('index.php');
                    }
                }
                break;
            case 'delete':
                if (!$id) { $error = t('missing'); }
                else {
                    $query = $pdo->prepare("DELETE FROM $table WHERE id = ?"); $query->execute([$id]);
                    $_SESSION['flash'] = $query->rowCount() ? t('deleted') : t('missing'); redirect('index.php');
                }
                break;
            default: $error = t('bad_action');
        }
    }
    if (in_array($view, ['edit', 'delete'], true) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        $query = $pdo->prepare("SELECT * FROM $table WHERE id = ?"); $query->execute([$id ?: 0]); $record = $query->fetch();
        if (!$record) { $error = t('missing'); $view = 'list'; }
        else { $values = $record; }
    }
    if ($view === 'list') { $rows = $pdo->query("SELECT * FROM $table ORDER BY id DESC")->fetchAll(); }
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    if ($exception->getCode() === '23000') { $errors[$fields[0]] = t('duplicate'); }
    else { $error = t('db_error'); }
}
$label = fn($field) => t($table === 'books' && $field === 'code' ? 'book_code' : $field);
header_page(t('list_' . $table));
?><div class="page-heading"><div><p class="section-label"><?= e($config['name']) ?></p><h1><?= e(t('list_' . $table)) ?></h1><p class="muted"><?= e(t('intro_' . $table)) ?></p></div><?php if ($view === 'list'): ?><a class="button primary" href="index.php?view=new"><?= e(t('add')) ?></a><?php else: ?><a class="button" href="index.php"><?= e(t('back')) ?></a><?php endif; ?></div>
<?php if ($error): ?><p class="notice" role="alert"><?= e($error) ?></p><?php endif; ?><?php if ($flash): ?><p class="notice success" role="status"><?= e($flash) ?></p><?php endif; ?>
<?php if ($view === 'list'): ?><section class="panel"><div class="table-heading"><h2><?= e(t('list_' . $table)) ?></h2><span><?= count($rows) ?> <?= e(t('records')) ?></span></div><?php if (!$rows): ?><p class="empty"><?= e($error ? t('db_error') : t('empty')) ?></p><?php else: ?><div class="table-wrap"><table><thead><tr><?php foreach ($fields as $field): ?><th scope="col"><?= e($label($field)) ?></th><?php endforeach; ?><th scope="col"><?= e(t('actions')) ?></th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><?php foreach ($fields as $field): ?><td data-label="<?= e($label($field)) ?>"><?= e($row[$field]) ?></td><?php endforeach; ?><td class="row-actions" data-label="<?= e(t('actions')) ?>"><a class="button" href="index.php?view=edit&amp;id=<?= e($row['id']) ?>"><?= e(t('edit')) ?></a><a class="button danger" href="index.php?view=delete&amp;id=<?= e($row['id']) ?>"><?= e(t('delete')) ?></a></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php elseif ($view === 'delete'): ?><section class="panel editor"><h2><?= e(t('delete_title')) ?></h2><p><?= e(t('delete_intro')) ?></p><dl><?php foreach ($fields as $field): ?><dt><?= e($label($field)) ?></dt><dd><?= e($values[$field]) ?></dd><?php endforeach; ?></dl><p><?= e(t('confirm_delete')) ?></p><form method="post"><?php csrf(); ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($id) ?>"><div class="form-actions"><button class="primary"><?= e(t('delete')) ?></button><a class="button" href="index.php"><?= e(t('cancel')) ?></a></div></form></section>
<?php else: ?><section class="panel editor"><h2><?= e(t($view === 'new' ? 'new' : 'edit_record')) ?></h2><form method="post"><?php csrf(); ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $view === 'edit' ? e($id) : '' ?>"><div class="fields"><?php foreach ($config['fields'] as $field => [$type, $limit]): ?><div class="field"><label for="<?= e($field) ?>"><?= e($label($field)) ?></label><input id="<?= e($field) ?>" name="<?= e($field) ?>" type="<?= e($type) ?>" value="<?= e($values[$field]) ?>" required <?= $type === 'number' ? 'min="' . ($field === 'year' ? 1 : 0) . '" max="' . $limit . '" step="1"' : 'maxlength="' . $limit . '"' ?> <?= isset($errors[$field]) ? 'aria-invalid="true" aria-describedby="error-' . e($field) . '"' : '' ?>><?php if (isset($errors[$field])): ?><p class="field-error" id="error-<?= e($field) ?>"><?= e($errors[$field]) ?></p><?php endif; ?></div><?php endforeach; ?></div><div class="form-actions"><button class="primary"><?= e(t('save')) ?></button><a class="button" href="index.php"><?= e(t('cancel')) ?></a></div></form></section><?php endif; footer_page();
