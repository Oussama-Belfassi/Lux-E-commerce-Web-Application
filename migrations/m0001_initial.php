<?php

namespace app\migrations;

class m0001_initial
{
    public function up(): string
    {
        return "CREATE TABLE users (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            Firstname  VARCHAR(100) NOT NULL,
            Lastname   VARCHAR(100) NOT NULL,
            email      VARCHAR(255) NOT NULL UNIQUE,
            password   VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=INNODB;";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS users;";
    }
}