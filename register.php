<?php

require_once 'member_helper.php';

$message = '';
$account = '';
$nickname = '';
$favoriteColor = default_color();
$avatar = default_avatar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = trim($_POST['account'] ?? '');
    $password = $_POST['password'] ?? '';
    $nickname = trim($_POST['nickname'] ?? '');
    $favoriteColor = member_color(trim($_POST['favorite_color'] ?? ''));
    $avatar = member_avatar(trim($_POST['avatar'] ?? ''));

    if ($account === '' || $password === '' || $nickname === '') {
        $message = '帳號、密碼、暱稱都必須填寫。';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM members1 WHERE account = ?');
        $stmt->execute([$account]);

        if ($stmt->fetch()) {
            $message = '帳號已存在。';
        } else {
            $stmt = $pdo->prepare('INSERT INTO members1 (account, password, nickname, favorite_color, avatar, is_admin) VALUES (?, ?, ?, ?, ?, 0)');
            $stmt->execute([$account, password_hash($password, PASSWORD_DEFAULT), $nickname, $favoriteColor, $avatar]);

            login_member([
                'id' => $pdo->lastInsertId(),
                'account' => $account,
                'nickname' => $nickname,
                'favorite_color' => $favoriteColor,
                'avatar' => $avatar,
                'is_admin' => 0,
            ]);

            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <title>註冊會員</title>
    <style>
        body { font-family: system-ui, -apple-system, Arial, sans-serif; margin: 0; background: #f5f7fb; }
        .container { max-width: 520px; margin: 40px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        h1 { margin-top: 0; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input, select { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #d8deea; border-radius: 8px; font: inherit; }
        button { background: #2563eb; color: #fff; border: 0; border-radius: 8px; padding: 10px 16px; font-size: 16px; cursor: pointer; }
        .message { margin-bottom: 14px; color: #b42318; }
        .links a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>註冊會員</h1>
            <?php if ($message !== ''): ?>
                <div class="message"><?= escape($message) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="account">帳號</label>
                    <input id="account" name="account" value="<?= escape($account) ?>" maxlength="50" required>
                </div>
                <div class="form-group">
                    <label for="password">密碼</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <div class="form-group">
                    <label for="nickname">暱稱</label>
                    <input id="nickname" name="nickname" value="<?= escape($nickname) ?>" maxlength="50" required>
                </div>
                <div class="form-group">
                    <label for="favorite_color">喜歡顏色</label>
                    <input id="favorite_color" name="favorite_color" type="color" value="<?= escape($favoriteColor) ?>">
                </div>
                <div class="form-group">
                    <label for="avatar">大頭貼</label>
                    <select id="avatar" name="avatar">
                        <?php foreach (avatar_options() as $option): ?>
                            <option value="<?= escape($option) ?>" <?= $option === $avatar ? 'selected' : '' ?>><?= escape($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">送出註冊</button>
            </form>
            <p class="links"><a href="login.php">已有帳號，前往登入</a></p>
        </div>
    </div>
</body>
</html>
