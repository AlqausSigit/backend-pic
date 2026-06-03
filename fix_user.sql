DELETE FROM users WHERE username = 'admin123';
INSERT INTO users (nama, username, email, password, role, created_at, updated_at)
VALUES ('Admin Script', 'admin123', 'admin@localhost', '$2y$10$OfWVc42fAlyY.y2CFlHgSeAYA1Rany5r6hdvBe8Q3AICmNMVjdeXC', 'admin', NOW(), NOW());
