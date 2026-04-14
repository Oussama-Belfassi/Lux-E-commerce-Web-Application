<?php

namespace app;

use PDO;

class Database
{
    private PDO $pdo;
    public static ?Database $db = null;

    public function __construct(array $_config)
    {
        $dsn  = $_config['dsn']      ?? '';
        $user = $_config['user']     ?? '';
        $pass = $_config['password'] ?? '';

        // append charset=utf8mb4 to the DSN if it is not already present,
        // preventing silent encoding issues with accented/non-ASCII names.

        if (!str_contains($dsn, 'charset=')) {
            $dsn .= ';charset=utf8mb4';
        }

        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT => 5,
        ]);

        self::$db = $this;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() !== false;
    }

    public function insertUser(array $data): void
    {
        try {
            $sql  = "INSERT INTO users (email, Firstname, Lastname, password, created_at)
                     VALUES (:email, :Firstname, :Lastname, :password, :date)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email',     $data['email']);
            $stmt->bindValue(':Firstname', $data['Firstname']);
            $stmt->bindValue(':Lastname',  $data['Lastname']);
            $stmt->bindValue(':password',  $data['password']);
            $stmt->bindValue(':date',      date('Y-m-d H:i:s'));
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to insert user: ' . $e->getMessage());
        }
    }

    // explicit column list instead of SELECT * — avoids returning the hashed
    // password on every lookup and prevents silent breakage on schema changes.

    public function getUserByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, Firstname, Lastname, password, google_id, created_at
             FROM users WHERE email = :email'
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    // explicit column list (same reason as above)

    public function getUserById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, Firstname, Lastname, password, google_id, created_at
             FROM users WHERE id = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function applyMigrations(): void
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $files  = scandir(__DIR__ . '/migrations');
        $toApply = array_diff($files, $appliedMigrations);

        // Sort so migrations always run in filename order regardless of filesystem ordering.

        sort($toApply);

        foreach ($toApply as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once __DIR__ . '/migrations/' . $migration;
            $className = "app\\migrations\\" . pathinfo($migration, PATHINFO_FILENAME);
            $instance  = new $className();

            try {
                $this->pdo->exec($instance->up());
                $newMigrations[] = $migration;
                echo "Applied: $migration" . PHP_EOL;
            } catch (\Exception $e) {
                echo "Failed: $migration — " . $e->getMessage() . PHP_EOL;
                break;
            }
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            echo "All migrations are already applied." . PHP_EOL;
        }
    }

    public function rollbackMigrations(int $steps = 1): void
    {
        $applied    = $this->getAppliedMigrations();
        $toRollback = array_slice(array_reverse($applied), 0, $steps);

        foreach ($toRollback as $migration) {
            require_once __DIR__ . '/migrations/' . $migration;

            $className = "app\\migrations\\" . pathinfo($migration, PATHINFO_FILENAME);
            $instance  = new $className();

            try {
                $this->pdo->exec($instance->down());
                $this->removeMigration($migration);
                echo "Rolled back: $migration" . PHP_EOL;
            } catch (\Exception $e) {
                echo "Failed to roll back: $migration — " . $e->getMessage() . PHP_EOL;
                break;
            }
        }
    }

    private function createMigrationsTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            migration  VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;");
    }

    private function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function saveMigrations(array $migrations): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
        foreach ($migrations as $migration) {
            $stmt->execute([':migration' => $migration]);
        }
    }

    private function removeMigration(string $migration): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration = :migration");
        $stmt->execute([':migration' => $migration]);
    }

    public function insertContact(array $data): void
    {
        try {
            $sql  = "INSERT INTO contacts (email, Firstname, Lastname, message, phone, prefix, created_at)
                     VALUES (:email, :Firstname, :Lastname, :message, :phone, :prefix, :date)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email',     $data['email']);
            $stmt->bindValue(':Firstname', $data['Firstname']);
            $stmt->bindValue(':Lastname',  $data['Lastname']);
            $stmt->bindValue(':message',   $data['message']);
            $stmt->bindValue(':phone',     $data['phone']);
            $stmt->bindValue(':prefix',    $data['prefix']);
            $stmt->bindValue(':date',      date('Y-m-d H:i:s'));
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to insert contact: ' . $e->getMessage());
        }
    }

    public function insertGoogleUser(array $data): int
    {
        try {
            $sql  = "INSERT INTO users (email, Firstname, Lastname, google_id, created_at)
                     VALUES (:email, :Firstname, :Lastname, :google_id, :date)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email',     $data['email']);
            $stmt->bindValue(':Firstname', $data['Firstname']);
            $stmt->bindValue(':Lastname',  $data['Lastname']);
            $stmt->bindValue(':google_id', $data['google_id']);
            $stmt->bindValue(':date',      date('Y-m-d H:i:s'));
            $stmt->execute();
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to insert Google user: ' . $e->getMessage());
        }
    }

    // explicit column list instead of SELECT *

    public function getUserByGoogleId(string $googleId): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, Firstname, Lastname, password, google_id, created_at
             FROM users WHERE google_id = :google_id'
        );
        $stmt->bindValue(':google_id', $googleId);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function linkGoogleId(int $userId, string $googleId): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET google_id = :google_id WHERE id = :id');
        $stmt->bindValue(':google_id', $googleId);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // the delete + insert are wrapped in a transaction so that if the insert
    // fails the old token is NOT lost, keeping the user's reset flow intact.

    public function insertPasswordReset(string $email, string $token, string $expiresAt): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->deletePasswordResetByEmail($email);

            $stmt = $this->pdo->prepare(
                "INSERT INTO password_resets (email, token, expires_at)
                 VALUES (:email, :token, :expires_at)"
            );
            $stmt->bindValue(':email',      $email);
            $stmt->bindValue(':token',      $token);
            $stmt->bindValue(':expires_at', $expiresAt);
            $stmt->execute();

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }


    public function getPasswordReset(string $token): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM password_resets WHERE token = :token"
        );
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function deletePasswordResetByEmail(string $email): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM password_resets WHERE email = :email"
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
    }

    public function deletePasswordResetByToken(string $token): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM password_resets WHERE token = :token"
        );
        $stmt->bindValue(':token', $token);
        $stmt->execute();
    }

    public function updateUserPassword(string $email, string $hashedPassword): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET password = :password WHERE email = :email"
        );
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':email',    $email);
        $stmt->execute();
    }
}
