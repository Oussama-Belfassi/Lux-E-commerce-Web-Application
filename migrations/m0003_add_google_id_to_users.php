<?php

namespace app\migrations;

class m0003_add_google_id_to_users
{
    public function up(): string
    {
        return "ALTER TABLE users
                ADD COLUMN google_id VARCHAR(255) NULL DEFAULT NULL AFTER password;";
    }

    public function down(): string
    {
        return "ALTER TABLE users DROP COLUMN google_id;";
    }
}