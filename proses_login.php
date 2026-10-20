<?php
$error    = "";
$username = "";

if (isset($_POST["username"])) {

    $username = $_POST["username"];
    $password = "";
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }

    if ($username == "" || $password == "") {
        $error = $t["required"];
    } else {
        try {
            require "koneksi.php";

            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = :username AND password = :password");
            $stmt->execute([":username" => $username, ":password" => $password]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Login berhasil: simpan data
                $_SESSION["user"] = [
                    "id"       => $user["id"],
                    "username" => $user["username"]
                ];
            } else {
                $error = $t["invalid_login"];
            }
        } catch (PDOException $ex) {
            $error = $t["db_error"];
        }
    }
}