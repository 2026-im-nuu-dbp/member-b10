DROP TABLE IF EXISTS replies;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS members;

CREATE TABLE members1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nickname VARCHAR(50) NOT NULL,
    favorite_color CHAR(7) NOT NULL DEFAULT '#f3f6fb',
    avatar VARCHAR(20) NOT NULL DEFAULT '🙂',
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE news1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(100) NOT NULL,
    avatar VARCHAR(20) NOT NULL DEFAULT '🙂',
    favorite_color CHAR(7) NOT NULL DEFAULT '#f3f6fb',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE replies1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(100) NOT NULL,
    avatar VARCHAR(20) NOT NULL DEFAULT '🙂',
    favorite_color CHAR(7) NOT NULL DEFAULT '#f3f6fb',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    INDEX idx_news_id (news_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO members (account, password, nickname, favorite_color, avatar, is_admin)
VALUES ('admin', '$2y$10$cADrfstqQDbJw9bY6n0mM.nt.JFJhgv.bfZZsWUjfhkViMGn.iBNC', '管理員', '#2563eb', '😎', 1);
