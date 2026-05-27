<?php

require_once 'db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function avatar_options()
{
    return ['😀', '😎', '😊', '🧑‍💻', '🌟', '🔥', '🐱', '🐶'];
}

function default_avatar()
{
    return '🙂';
}

function default_color()
{
    return '#f3f6fb';
}

function member_avatar($value)
{
    return in_array($value, avatar_options(), true) ? $value : default_avatar();
}

function member_color($value)
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) ? $value : default_color();
}

function current_member()
{
    return $_SESSION['member'] ?? null;
}

function is_logged_in()
{
    return !empty($_SESSION['member']);
}

function is_admin()
{
    return !empty($_SESSION['member']['is_admin']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin()
{
    if (!is_admin()) {
        die('權限不足。<br><a href="index.php">返回首頁</a>');
    }
}

function login_member(array $member)
{
    $_SESSION['member'] = [
        'id' => (int) $member['id'],
        'account' => $member['account'],
        'nickname' => $member['nickname'],
        'favorite_color' => $member['favorite_color'],
        'avatar' => $member['avatar'],
        'is_admin' => (int) $member['is_admin'],
    ];
}

function logout_member()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function member_badge($name, $avatar, $color)
{
    $safeName = escape($name);
    $safeAvatar = escape(member_avatar($avatar));
    $safeColor = escape(member_color($color));

    return '<span class="member-badge" style="background:' . $safeColor . '20;border-color:' . $safeColor . ';">'
        . '<span class="member-name">' . $safeName . '</span>'
        . '<span class="member-avatar">' . $safeAvatar . '</span>'
        . '</span>';
}
