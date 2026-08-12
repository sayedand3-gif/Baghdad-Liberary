<?php
session_start();

// 1. تفريغ جميع متغيرة الجلسة (Session Variables)
$_SESSION = array();

// 2. تدمير كوكيز الجلسة إذا كانت موجودة
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 3. إنهاء الجلسة بالكامل
session_destroy();

// 4. إعادة التوجيه للصفحة الرئيسية
header("Location: ../index.php");
exit;
?>