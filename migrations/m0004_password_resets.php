<?php

namespace app\migrations;

class m0004_password_resets
{
    public function up(): string
    {
        return "CREATE TABLE password_resets (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            email      VARCHAR(255) NOT NULL,
            token      VARCHAR(64)  NOT NULL UNIQUE,
            expires_at DATETIME     NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS password_resets;";
    }
}