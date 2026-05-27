<?php

require_once 'member_helper.php';

$message = '';
$account = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = trim($_POST['account'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, account, password, nickname, favorite_color, avatar, is_admin FROM members1 WHERE account = ? LIMIT 1');
    $stmt->execute([$account]);
    $member = $stmt->fetch();

    if ($member && password_verify($password, $member['password'])) {
        login_member($member);
        header('Location: index.php');
        exit;
    }

    $message = '帳號或密碼錯誤。';
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <title>會員登入</title>
    <style>
        body { font-family: system-ui, -apple-system, Arial, sans-serif; margin: 0; background: #f5f7fb; }
        .container { max-width: 480px; margin: 40px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        h1 { margin-top: 0; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #d8deea; border-radius: 8px; font: inherit; }
        button { background: #16a34a; color: #fff; border: 0; border-radius: 8px; padding: 10px 16px; font-size: 16px; cursor: pointer; }
        .message { margin-bottom: 14px; color: #b42318; }
        .links a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>會員登入</h1>
            <?php if ($message !== ''): ?>
                <div class="message"><?= escape($message) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="account">帳號</label>
                    <input id="account" name="account" value="<?= escape($account) ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">密碼</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <button type="submit">登入</button>
            </form>
            <p class="links"><a href="register.php">還沒有帳號，前往註冊</a></p>
        </div>
    </div>
</body>
</html>
