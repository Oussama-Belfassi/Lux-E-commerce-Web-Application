<?php

namespace app\migrations;

class m0005_nullable_password
{
    public function up(): string
    {
        return "ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NULL DEFAULT NULL;";
    }

    public function down(): string
    {
        return "ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL;";
    }
}