<?php

$batas = [
    "code"      => 30,
    "title"     => 160,
    "author"    => 120,
    "publisher" => 120,
    "year"      => 2100
];

$kolom = [
    "code"      => $t["book_code"],
    "title"     => $t["title"],
    "author"    => $t["author"],
    "publisher" => $t["publisher"],
    "year"      => $t["year"]
];

// Menentukan tampilan yang dibuka
$view = "list";
if (isset($_GET["view"])) {
    $view = $_GET["view"];
}
if (!in_array($view, ["list", "new", "edit", "delete"])) {
    $view = "list";
}

$id = false;
if (isset($_GET["id"])) {
    $id = filter_var($_GET["id"], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
}

$values = ["code" => "", "title" => "", "author" => "", "publisher" => "", "year" => ""];
$errors = [];
$error  = "";
$flash  = "";
$rows   = [];

try {
    require "koneksi.php";

    if (isset($_POST["action"])) {

        $id_post = "";
        if (isset($_POST["id"])) {
            $id_post = $_POST["id"];
        }
        $id = filter_var($id_post, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

        switch ($_POST["action"]) {

            case "save":
                if ($id_post == "") {
                    $view = "new";
                } else {
                    $view = "edit";
                }

                foreach ($batas as $nama => $maks) {
                    $nilai = "";
                    if (isset($_POST[$nama])) {
                        $nilai = $_POST[$nama];
                    }
                    $values[$nama] = $nilai;

                    if ($nilai == "") {
                        $errors[$nama] = $t["required"];
                    } elseif ($nama == "year") {
                        $tahun = filter_var($nilai, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 2100]]);
                        if ($tahun === false) {
                            $errors[$nama] = $t["integer"]." (1 - 2100).";
                        } else {
                            $values[$nama] = $tahun;
                        }
                    }
                }

                if ($view == "edit" && !$id) {
                    $error = $t["missing"];
                }

                if (empty($errors) && empty($error)) {

                    if ($view == "edit") {
                        $stmt = $pdo->prepare("SELECT id FROM books WHERE id = :id");
                        $stmt->execute([":id" => $id]);
                        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                            $error = $t["missing"];
                        }
                    }

                    if (empty($error)) {
                        if ($view == "new") {
                            $stmt = $pdo->prepare("SELECT id FROM books WHERE code = :code");
                            $stmt->execute([":code" => $values["code"]]);
                        } else {
                            $stmt = $pdo->prepare("SELECT id FROM books WHERE code = :code AND id <> :id");
                            $stmt->execute([":code" => $values["code"], ":id" => $id]);
                        }
                        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                            $errors["code"] = $t["duplicate"];
                        }
                    }

                    // Semua valid: simpan ke database
                    if (empty($error) && empty($errors)) {
                        if ($view == "new") {
                            $stmt = $pdo->prepare(
                                "INSERT INTO books (code, title, author, publisher, year)
                                 VALUES (:code, :title, :author, :publisher, :year)"
                            );
                            $stmt->execute([
                                ":code"      => $values["code"],
                                ":title"     => $values["title"],
                                ":author"    => $values["author"],
                                ":publisher" => $values["publisher"],
                                ":year"      => $values["year"]
                            ]);
                        } else {
                            $stmt = $pdo->prepare(
                                "UPDATE books
                                 SET code = :code, title = :title, author = :author, publisher = :publisher, year = :year
                                 WHERE id = :id"
                            );
                            $stmt->execute([
                                ":code"      => $values["code"],
                                ":title"     => $values["title"],
                                ":author"    => $values["author"],
                                ":publisher" => $values["publisher"],
                                ":year"      => $values["year"],
                                ":id"        => $id
                            ]);
                        }
                        // Berhasil: kembali menampilkan daftar buku
                        $flash  = $t["saved"];
                        $view   = "list";
                        $values = ["code" => "", "title" => "", "author" => "", "publisher" => "", "year" => ""];
                    }
                }
                break;

            case "delete":
                if (!$id) {
                    $error = $t["missing"];
                } else {
                    // Delete
                    $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
                    $stmt->execute([":id" => $id]);
                    if ($stmt->rowCount() > 0) {
                        $flash = $t["deleted"];
                    } else {
                        $flash = $t["missing"];
                    }
                    $view = "list";
                }
                break;

            default:
                $error = $t["bad_action"];
        }
    }

    if (($view == "edit" || $view == "delete") && !isset($_POST["action"])) {
        $cari = 0;
        if ($id) {
            $cari = $id;
        }
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute([":id" => $cari]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            $error = $t["missing"];
            $view  = "list";
        } else {
            $values = $record;
        }
    }

    if ($view == "list") {
        $result = $pdo->query("SELECT * FROM books ORDER BY id DESC");
        $rows   = $result->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $ex) {
    $error = $t["db_error"];
}

$judul_halaman = $t["list_books"];
require "header.php";
?>
<div class="page-heading"><div><p class="section-label">BookNest</p><h1><?php echo e($t["list_books"]); ?></h1><p class="muted"><?php echo e($t["intro_books"]); ?></p></div><?php if ($view == "list") { ?><a class="button primary" href="index.php?view=new"><?php echo e($t["add"]); ?></a><?php } else { ?><a class="button" href="index.php"><?php echo e($t["back"]); ?></a><?php } ?></div>
<?php if (!empty($error)) { ?><p class="notice" role="alert"><?php echo e($error); ?></p><?php } ?><?php if (!empty($flash)) { ?><p class="notice success" role="status"><?php echo e($flash); ?></p><?php } ?>

<?php if ($view == "list") { ?>
<section class="panel"><div class="table-heading"><h2><?php echo e($t["list_books"]); ?></h2><span><?php echo count($rows); ?> <?php echo e($t["records"]); ?></span></div>
<?php if (count($rows) == 0) { ?>
<p class="empty"><?php if (!empty($error)) { echo e($t["db_error"]); } else { echo e($t["empty"]); } ?></p>
<?php } else { ?>
<div class="table-wrap"><table><thead><tr>
<?php foreach ($kolom as $nama => $label) { ?>
<th scope="col"><?php echo e($label); ?></th>
<?php } ?>
<th scope="col"><?php echo e($t["actions"]); ?></th></tr></thead><tbody>
<?php foreach ($rows as $row) { ?>
<tr>
<?php foreach ($kolom as $nama => $label) { ?>
<td data-label="<?php echo e($label); ?>"><?php echo e($row[$nama]); ?></td>
<?php } ?>
<td class="row-actions" data-label="<?php echo e($t["actions"]); ?>"><a class="button" href="index.php?view=edit&amp;id=<?php echo e($row["id"]); ?>"><?php echo e($t["edit"]); ?></a><a class="button danger" href="index.php?view=delete&amp;id=<?php echo e($row["id"]); ?>"><?php echo e($t["delete"]); ?></a></td>
</tr>
<?php } ?>
</tbody></table></div>
<?php } ?>
</section>

<?php } elseif ($view == "delete") { ?>
<section class="panel editor"><h2><?php echo e($t["delete_title"]); ?></h2><p><?php echo e($t["delete_intro"]); ?></p><dl>
<?php foreach ($kolom as $nama => $label) { ?>
<dt><?php echo e($label); ?></dt><dd><?php echo e($values[$nama]); ?></dd>
<?php } ?>
</dl><p><?php echo e($t["confirm_delete"]); ?></p><form method="post" action="index.php"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo e($id); ?>"><div class="form-actions"><button class="primary"><?php echo e($t["delete"]); ?></button><a class="button" href="index.php"><?php echo e($t["cancel"]); ?></a></div></form></section>

<?php } else { ?>
<section class="panel editor"><h2><?php if ($view == "new") { echo e($t["new"]); } else { echo e($t["edit_record"]); } ?></h2><form method="post" action="index.php"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?php if ($view == "edit") { echo e($id); } ?>"><div class="fields">
<?php foreach ($batas as $nama => $maks) { ?>
<div class="field"><label for="<?php echo e($nama); ?>"><?php echo e($kolom[$nama]); ?></label><input id="<?php echo e($nama); ?>" name="<?php echo e($nama); ?>" type="<?php if ($nama == "year") { echo "number"; } else { echo "text"; } ?>" value="<?php echo e($values[$nama]); ?>" required <?php if ($nama == "year") { echo 'min="1" max="'.$maks.'" step="1"'; } else { echo 'maxlength="'.$maks.'"'; } ?> <?php if (isset($errors[$nama])) { echo 'aria-invalid="true" aria-describedby="error-'.e($nama).'"'; } ?>><?php if (isset($errors[$nama])) { ?><p class="field-error" id="error-<?php echo e($nama); ?>"><?php echo e($errors[$nama]); ?></p><?php } ?></div>
<?php } ?>
</div><div class="form-actions"><button class="primary"><?php echo e($t["save"]); ?></button><a class="button" href="index.php"><?php echo e($t["cancel"]); ?></a></div></form></section>
<?php } ?>
<?php require "footer.php"; ?>