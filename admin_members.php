<?php

require_once 'member_helper.php';

require_admin();

$message = '';
$currentMember = current_member();
$currentMemberId = $currentMember['id'] ?? 0;

$editing = [
    'id' => 0,
    'account' => '',
    'nickname' => '',
    'favorite_color' => default_color(),
    'avatar' => default_avatar(),
    'is_admin' => 0,
];

$editId = intval($_GET['edit'] ?? 0);
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT id, account, nickname, favorite_color, avatar, is_admin FROM members WHERE id = ?');
    $stmt->execute([$editId]);
    $member = $stmt->fetch();

    if ($member) {
        $editing = $member;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'save';
    $id = intval($_POST['id'] ?? 0);

    if ($action === 'delete') {
        if ($id === $currentMemberId) {
            $message = '不能刪除自己。';
        } else {
            $stmt = $pdo->prepare('DELETE FROM members WHERE id = ?');
            $stmt->execute([$id]);
            header('Location: admin_members.php');
            exit;
        }
    } else {
        $account = trim($_POST['account'] ?? '');
        $password = $_POST['password'] ?? '';
        $nickname = trim($_POST['nickname'] ?? '');
        $favoriteColor = member_color(trim($_POST['favorite_color'] ?? ''));
        $avatar = member_avatar(trim($_POST['avatar'] ?? ''));
        $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

        if ($account === '' || $nickname === '') {
            $message = '帳號與暱稱都必須填寫。';
        } elseif ($id === 0 && $password === '') {
            $message = '新增會員時必須輸入密碼。';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM members WHERE account = ? AND id <> ?');
            $stmt->execute([$account, $id]);

            if ($stmt->fetch()) {
                $message = '帳號已存在。';
            } else {
                if ($id === 0) {
                    $stmt = $pdo->prepare('INSERT INTO members (account, password, nickname, favorite_color, avatar, is_admin) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$account, password_hash($password, PASSWORD_DEFAULT), $nickname, $favoriteColor, $avatar, $isAdmin]);
                } elseif ($password === '') {
                    $stmt = $pdo->prepare('UPDATE members SET account = ?, nickname = ?, favorite_color = ?, avatar = ?, is_admin = ? WHERE id = ?');
                    $stmt->execute([$account, $nickname, $favoriteColor, $avatar, $isAdmin, $id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE members SET account = ?, password = ?, nickname = ?, favorite_color = ?, avatar = ?, is_admin = ? WHERE id = ?');
                    $stmt->execute([$account, password_hash($password, PASSWORD_DEFAULT), $nickname, $favoriteColor, $avatar, $isAdmin, $id]);
                }

                if ($id === $currentMemberId) {
                    $stmt = $pdo->prepare('SELECT id, account, nickname, favorite_color, avatar, is_admin FROM members WHERE id = ?');
                    $stmt->execute([$id]);
                    $latest = $stmt->fetch();

                    if ($latest) {
                        login_member($latest);
                    }
                }

                header('Location: admin_members.php');
                exit;
            }
        }
    }
}

$members = $pdo->query('SELECT id, account, nickname, favorite_color, avatar, is_admin FROM members ORDER BY id ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <title>會員管理</title>
    <style>
        body { font-family: system-ui, -apple-system, Arial, sans-serif; margin: 0; padding: 20px; background: #f5f7fb; }
        .container { max-width: 1000px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
        .topbar a { color: #2563eb; text-decoration: none; margin-left: 10px; }
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 8px 24px rgba(0,0,0,.08); margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input, select { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #d8deea; border-radius: 8px; font: inherit; }
        button { border: 0; border-radius: 8px; padding: 9px 14px; cursor: pointer; }
        .save { background: #2563eb; color: #fff; }
        .danger { background: #dc2626; color: #fff; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #e7ecf5; text-align: left; vertical-align: top; }
        .message { color: #b42318; margin-bottom: 14px; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .member-badge { display: inline-flex; align-items: center; gap: 8px; padding: 4px 10px; border: 1px solid; border-radius: 999px; }
        .member-avatar { font-size: 18px; }
        .small { width: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <h1>會員管理</h1>
            <div>
                <a href="index.php">回討論區</a>
                <a href="logout.php">登出</a>
            </div>
        </div>

        <div class="card">
            <h2><?= $editing['id'] > 0 ? '編輯會員' : '新增會員' ?></h2>
            <?php if ($message !== ''): ?>
                <div class="message"><?= escape($message) ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
                <div class="grid">
                    <div class="form-group">
                        <label for="account">帳號</label>
                        <input id="account" name="account" value="<?= escape($editing['account']) ?>" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label for="nickname">暱稱</label>
                        <input id="nickname" name="nickname" value="<?= escape($editing['nickname']) ?>" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label for="password">密碼<?= $editing['id'] > 0 ? '（留空不變更）' : '' ?></label>
                        <input id="password" name="password" type="password" <?= $editing['id'] > 0 ? '' : 'required' ?>>
                    </div>
                    <div class="form-group">
                        <label for="favorite_color">喜歡顏色</label>
                        <input id="favorite_color" name="favorite_color" type="color" value="<?= escape(member_color($editing['favorite_color'])) ?>">
                    </div>
                    <div class="form-group">
                        <label for="avatar">大頭貼</label>
                        <select id="avatar" name="avatar">
                            <?php foreach (avatar_options() as $option): ?>
                                <option value="<?= escape($option) ?>" <?= member_avatar($editing['avatar']) === $option ? 'selected' : '' ?>><?= escape($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input class="small" type="checkbox" name="is_admin" <?= !empty($editing['is_admin']) ? 'checked' : '' ?>> 管理員
                        </label>
                    </div>
                </div>
                <button class="save" type="submit">儲存</button>
            </form>
        </div>

        <div class="card">
            <h2>會員列表</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>帳號</th>
                        <th>暱稱</th>
                        <th>管理員</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?= (int) $member['id'] ?></td>
                            <td><?= escape($member['account']) ?></td>
                            <td><?= member_badge($member['nickname'], $member['avatar'], $member['favorite_color']) ?></td>
                            <td><?= !empty($member['is_admin']) ? '是' : '否' ?></td>
                            <td>
                                <div class="actions">
                                    <a href="admin_members.php?edit=<?= (int) $member['id'] ?>">編輯</a>
                                    <form method="post" onsubmit="return confirm('確定刪除這個會員嗎？');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $member['id'] ?>">
                                        <button class="danger" type="submit">刪除</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
