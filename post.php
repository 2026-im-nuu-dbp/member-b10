<?php

header('Content-Type: text/html; charset=utf-8');
require_once 'member_helper.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$member = current_member();

if ($title === '' || $content === '') {
    die('標題與內容都必須填寫。<br><a href="index.php">返回</a>');
}

try {
    $stmt = $pdo->prepare('INSERT INTO news1 (title, content, author, avatar, favorite_color) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        substr($title, 0, 200),
        substr($content, 0, 10000),
        $member['nickname'],
        $member['avatar'],
        $member['favorite_color'],
    ]);

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    die('發表討論失敗: ' . $e->getMessage() . '<br><a href="index.php">返回</a>');
}
