<?php

session_start();

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $topic_id = $_POST['topic_id'];
    $user_id = $_POST['user_id'];
    $comment_text = trim($_POST['comment_text']);

    // التأكد إن التعليق مش فاضي
    if (!empty($comment_text)) {

        // إضافة التعليق إلى قاعدة البيانات
        $sql = "INSERT INTO comments (topic_id, user_id, comment_text)
                VALUES (?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $topic_id,
            $user_id,
            $comment_text
        ]);

        // الرجوع للصفحة السابقة
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}