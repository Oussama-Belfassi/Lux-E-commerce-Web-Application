# Luxé — E-commerce Web Application

> A secure, lightweight PHP MVC e-commerce web application with Google OAuth, password reset, and contact form functionality.

---

## Tech Stack

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)
![PHPMailer](https://img.shields.io/badge/PHPMailer-FF0000?style=for-the-badge&logo=gmail&logoColor=white)

---

## Features

- **Authentication** — Register, login, and logout with full session management
- **Google OAuth** — Sign in with Google using OAuth 2.0
- **Password Reset** — Secure email-based password reset with expiring tokens
- **Contact Form** — Contact page with email notification to admin
- **CSRF Protection** — All forms protected against Cross-Site Request Forgery
- **Session Security** — Session fixation prevention, full session destruction on logout
- **Custom MVC Router** — Lightweight router with middleware support
- **Database Migrations** — Version-controlled schema with apply and rollback support

---

## Project Structure

```
project/
├── app/
│   ├── controller/
│   │   └── PagesController.php   # Handles all page logic
│   ├── middlewares/
│   │   └── AuthMiddleware.php    # Protects authenticated routes
│   ├── migrations/               # Database schema migrations
│   ├── models/
│   │   └── Users.php             # User model and validation
│   ├── views/
│   │   ├── pages/                # Page view files
│   │   └── _layout.php           # Shared layout template
│   ├── Database.php              # PDO database wrapper
│   ├── Mailer.php                # PHPMailer wrapper
│   ├── OAuth.php                 # Google OAuth handler
│   ├── Router.php                # MVC router
│   └── Session.php               # Session and CSRF management
├── public/
│   └── index.php                 # Application entry point
├── .env.example                  # Environment variable template
├── composer.json
└── composer.lock
```

---

## Getting Started

### Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- A mail SMTP account (e.g. Gmail, Mailtrap)
- A Google Cloud project for OAuth

---

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/your-username/luxe.git
cd luxe
```

**2. Install dependencies**

```bash
composer install
```

**3. Set up environment variables**

```bash
cp .env.example .env
```

Then open `.env` and fill in your values:

```env
# Database
DB_DSN=mysql:host=localhost;dbname=luxe
DB_USER=root
DB_PASSWORD=your_password

# Mail
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM=your_email@gmail.com
MAIL_FROM_NAME=Luxé
MAIL_ADMIN=admin@yourdomain.com

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback

# App
APP_URL=http://localhost
```

**4. Run database migrations**

```bash
php migrations.php
```

**5. Point your web server to the `/public` folder as the document root**

If you are using Apache, make sure `mod_rewrite` is enabled and your `.htaccess` is in the `/public` folder:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

---

## Routes

| Method | URL | Description | Auth required |
|--------|-----|-------------|---------------|
| GET | `/` | Login page | No |
| POST | `/` | Login form submit | No |
| GET | `/register` | Register page | No |
| POST | `/register` | Register form submit | No |
| POST | `/logout` | Logout | Yes |
| GET | `/home` | Home page | Yes |
| GET | `/contact` | Contact page | Yes |
| POST | `/contact` | Contact form submit | Yes |
| GET | `/forgot-password` | Forgot password page | No |
| POST | `/forgot-password` | Send reset email | No |
| GET | `/reset-password` | Reset password page | No |
| POST | `/reset-password` | Reset password submit | No |
| GET | `/auth/google` | Redirect to Google OAuth | No |
| GET | `/auth/google/callback` | Google OAuth callback | No |

---

## Security

This project implements the following security measures:

- **CSRF tokens** on all POST forms to prevent cross-site request forgery
- **Session destruction** on logout — full session wipe, not just key removal
- **Session regeneration** on login — prevents session fixation attacks
- **Password hashing** with `PASSWORD_BCRYPT` via `password_hash()`
- **Prepared statements** everywhere — no raw SQL with user input
- **HTML escaping** with `htmlspecialchars()` on all output
- **Expiring reset tokens** — password reset links expire after 1 hour
- **OAuth state validation** — prevents CSRF on the Google OAuth flow

---

## Environment Variables

| Variable | Description |
|----------|-------------|
| `DB_DSN` | PDO DSN string e.g. `mysql:host=localhost;dbname=luxe` |
| `DB_USER` | Database username |
| `DB_PASSWORD` | Database password |
| `MAIL_HOST` | SMTP host |
| `MAIL_PORT` | SMTP port (usually 587) |
| `MAIL_USERNAME` | SMTP username |
| `MAIL_PASSWORD` | SMTP password or app password |
| `MAIL_FROM` | From email address |
| `MAIL_FROM_NAME` | From display name |
| `MAIL_ADMIN` | Admin email to receive contact form submissions |
| `GOOGLE_CLIENT_ID` | Google OAuth client ID |
| `GOOGLE_CLIENT_SECRET` | Google OAuth client secret |
| `GOOGLE_REDIRECT_URI` | Google OAuth redirect URI |
| `APP_URL` | Base URL of your application |

---

## License

This project is for educational purposes.
