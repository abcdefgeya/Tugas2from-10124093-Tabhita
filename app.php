<?php
session_start();

require "translations.php";

function e($teks) {
    return htmlspecialchars($teks);
}

if (isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "language":
            if (isset($_POST["language"]) && in_array($_POST["language"], ["id", "en"])) {
                $_SESSION["language"] = $_POST["language"];
            }
            unset($_POST["action"]);
            break;

        case "logout":
            session_unset();
            session_destroy();
            unset($_POST["action"]);
            break;
    }
}

if (!isset($_SESSION["language"])) {
    $_SESSION["language"] = "id";
}

$t = $translations[$_SESSION["language"]];