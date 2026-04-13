<?php

namespace app\models;

use app\Database;

class Users
{
    public int $id = 0;
    public string $email = '';
    public string $password = '';
    public string $Firstname = '';
    public string $Lastname = '';
    public string $message = '';
    public string $confirm_password = '';
    public string $phone = '';
    public string $prefix = '';
    public string $google_id = '';


    public function load(array $data): void
    {
        $this->email = trim($data['email'] ?? '');
        $this->message = trim($data['message'] ?? '');
        $this->Firstname = trim($data['Firstname'] ?? '');
        $this->Lastname = trim($data['Lastname'] ?? '');
        $this->phone = trim($data['phonenumber'] ?? '');
        $this->prefix = trim($data['phonenumber-prefix'] ?? '');
        $this->password  = trim($data['password'] ?? '');
        $this->confirm_password = trim($data['confirm_password'] ?? '');
    }

    public function save(): array
    {
        $errors   = [];
        $required = ['email', 'password', 'Firstname', 'Lastname'];

        foreach ($required as $field) {
            if (empty($this->$field)) {
                $errors[$field][] = "The $field is required.";
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email is not valid.';
        }

        if (!empty($this->password) && (strlen($this->password) < 8 || strlen($this->password) > 24)) {
            $errors['password'][] = 'Password must be between 8 and 24 characters.';
        }

        if (!empty($this->password) && $this->password !== $this->confirm_password) {
            $errors['confirm_password'][] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        if (Database::$db->emailExists($this->email)) {
            $errors['email'][] = 'This email is already taken.';
            return $errors;
        }

        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->confirm_password = '';

        try {
            Database::$db->insertUser([
                'email'     => $this->email,
                'Firstname' => $this->Firstname,
                'Lastname'  => $this->Lastname,
                'password'  => $this->password,
            ]);
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errors['email'][] = 'This email is already taken.';
            } else {
                throw $e;
            }
        }

        return $errors;
    }

    public static function attempt(string $email, string $password): array
    {
        $errors = [];

        if (empty($email)) {
            $errors['email'][] = 'Email is required.';
        }

        if (empty($password)) {
            $errors['password'][] = 'Password is required.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'user' => null, 'errors' => $errors];
        }

        $row = Database::$db->getUserByEmail($email);

        if (!$row) {
            $errors['email'][] = 'No account found with this email.';
        } elseif (!password_verify($password, $row['password'])) {
            $errors['password'][] = 'Incorrect password.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'user' => null, 'errors' => $errors];
        }

        $user = new static();
        $user->id = $row['id'];
        $user->email = $row['email'];
        $user->password = $row['password'];
        $user->Lastname = $row['Lastname'];
        $user->Firstname = $row['Firstname'];

        return ['success' => true, 'user' => $user, 'errors' => []];
    }

    public static function getUserById(int $id): ?static
    {
        $row = Database::$db->getUserById($id);

        if (!$row) {
            return null;
        }

        $user = new static();
        $user->id = $row['id'];
        $user->email = $row['email'];
        $user->password = $row['password'];
        $user->Lastname = $row['Lastname'];
        $user->Firstname = $row['Firstname'];

        return $user;
    }

    public function saveContact(): array
    {
        $errors   = [];
        $required = ['email', 'message', 'Lastname', 'Firstname'];

        foreach ($required as $field) {
            if (empty($this->$field)) {
                $errors[$field][] = "The $field is required.";
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = "Email is not valid.";
        }

        if (strlen($this->message) < 10) {
            $errors['message'][] = 'Message must be at least 10 characters.';
        }

        if (strlen($this->message) > 1000) {
            $errors['message'][] = 'Message cannot exceed 1000 characters.';
        }

        if (!empty($this->phone) && !preg_match('/^\d{6,15}$/', $this->phone)) {
            $errors['phonenumber'][] = 'Phone number is not valid.';
        }

        if (empty($errors)) {
            Database::$db->insertContact([
                'email'     => $this->email,
                'Firstname' => $this->Firstname,
                'Lastname'  => $this->Lastname,
                'message'   => $this->message,
                'phone'     => $this->phone,
                'prefix'    => $this->prefix,
            ]);
        }

        return $errors;
    }

    public static function findOrCreateGoogleUser(array $googleData): ?static
    {
        $googleId = $googleData['google_id'];
        $email = $googleData['email'];

        $row = Database::$db->getUserByGoogleId($googleId);

        if (!$row && !empty($email)) {
            $row = Database::$db->getUserByEmail($email);

            if ($row) {
                Database::$db->linkGoogleId($row['id'], $googleId);
            }
        }

        if (!$row) {
            $id = Database::$db->insertGoogleUser([
                'google_id' => $googleId,
                'email' => $email,
                'Firstname' => $googleData['Firstname'],
                'Lastname'  => $googleData['Lastname'],
            ]);

            $row = Database::$db->getUserById($id);
        }

        if (!$row) {
            return null;
        }

        $user = new static();
        $user->id = $row['id'];
        $user->email = $row['email'];
        $user->Firstname = $row['Firstname'];
        $user->Lastname = $row['Lastname'];
        $user->password = $row['password']  ?? '';
        $user->google_id = $row['google_id'] ?? '';

        return $user;
    }
}
