<?php

require_once __DIR__ . '/../config/db.php';

session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'يجب تسجيل الدخول أولاً'
    ]);
    exit;
}

$topicId = isset($_POST['topic_id']) ? (int) $_POST['topic_id'] : 0;
$userId = (int) $_SESSION['user_id'];

if ($topicId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'موضوع غير صالح'
    ]);
    exit;
}$stmt = $pdo->prepare("
    SELECT id
    FROM topic_likes
    WHERE topic_id = ? AND user_id = ?
");

$stmt->execute([$topicId, $userId]);

$existingLike = $stmt->fetch();
if ($existingLike) {

    $delete = $pdo->prepare("
        DELETE FROM topic_likes
        WHERE topic_id = ? AND user_id = ?
    ");

    $delete->execute([$topicId, $userId]);

    $liked = false;

} else {

    $insert = $pdo->prepare("
        INSERT INTO topic_likes (topic_id, user_id, created_at)
        VALUES (?, ?, NOW())
    ");

    $insert->execute([$topicId, $userId]);

    $liked = true;
}$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM topic_likes
    WHERE topic_id = ?
");

$countStmt->execute([$topicId]);

$count = (int) $countStmt->fetchColumn();
echo json_encode([
    'success' => true,
    'liked' => $liked,
    'count' => $count
]);