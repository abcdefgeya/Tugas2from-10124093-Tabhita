<?php
require "app.php";

if (!isset($_SESSION["user"])) {
    require "proses_login.php";
}

if (isset($_SESSION["user"])) {
    require "halaman_katalog.php";
} else {
    require "halaman_login.php";
}