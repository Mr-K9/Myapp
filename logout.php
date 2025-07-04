<?php
// includes/auth.php
session_start();

function is_logged_in() {
    return isset($_SESSION['user']);
}

function is_admin() {
    return is_logged_in() && $_SESSION['user']['is_admin'] == 1;
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: loginandsingup.php");
        exit;
    }
}
