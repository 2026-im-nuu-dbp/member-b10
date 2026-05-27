<?php

header('Content-Type: text/html; charset=utf-8');
require_once 'member_helper.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

$newsId = intval($_POST['news_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$member = current_member();

if ($newsId <= 0) {
    die('無效的討論 ID。<br><a href="index.php">返回</a>');
}

if ($content === '') {
    die('內容都必須填寫。<br><a href="show_news.php?id=' . $newsId . '">返回</a>');
}

try {
    $stmt = $pdo->prepare('SELECT id FROM news1 WHERE id = ?');
    $stmt->execute([$newsId]);

    if (!$stmt->fetch()) {
        die('找不到此討論。<br><a href="index.php">返回首頁</a>');
    }

    $stmt = $pdo->prepare('INSERT INTO replies1 (news_id, content, author, avatar, favorite_color) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $newsId,
        substr($content, 0, 10000),
        $member['nickname'],
        $member['avatar'],
        $member['favorite_color'],
    ]);

    header('Location: show_news.php?id=' . $newsId);
    exit;
} catch (PDOException $e) {
    die('發表回應失敗: ' . $e->getMessage() . '<br><a href="show_news.php?id=' . $newsId . '">返回</a>');
}
