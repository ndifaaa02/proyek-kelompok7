<?php
session_start();

// 1. Kosongkan semua variabel session
$_SESSION = [];

// 2. Hancurkan cookie session di browser jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan session di server
session_destroy();

// 4. Alihkan ke halaman login admin
header("Location: admin_login.php");
exit;
?>