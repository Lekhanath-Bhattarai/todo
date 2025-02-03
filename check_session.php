<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$timeout_duration = 120;

if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    session_start();
    header('Location: login.php?timeout=true');
    exit;
}

session_regenerate_id(true);

$_SESSION['LAST_ACTIVITY'] = time();
?>
