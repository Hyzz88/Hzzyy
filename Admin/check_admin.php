<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

function is_admin_user($user) {
    if (!is_array($user) || !isset($user['PhanQuyen'])) {
        return false;
    }

    $role = strtolower(trim((string)$user['PhanQuyen']));
    return $role === 'admin' || $role === '1';
}

if (!is_admin_user($_SESSION['admin'] ?? null)) {
    if ($current_page !== 'loginadmin.php') {
        header("Location: loginadmin.php");
        exit();
    }
}
?>
