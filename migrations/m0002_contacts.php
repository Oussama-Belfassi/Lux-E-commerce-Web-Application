<?php

namespace app\migrations;

class m0002_contacts
{
    public function up(): string
    {
        return "CREATE TABLE contacts (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            Firstname  VARCHAR(100) NOT NULL,
            Lastname   VARCHAR(100) NOT NULL,
            email      VARCHAR(255) NOT NULL,
            message    TEXT NOT NULL,
            phone      VARCHAR(20)  DEFAULT NULL,
            prefix     VARCHAR(10)  DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS contacts;";
    }
}