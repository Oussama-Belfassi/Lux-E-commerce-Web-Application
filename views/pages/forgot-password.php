<?php

/** @var array $errors */
/** @var bool $success */ ?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-icon">
            <svg viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
        </div>

        <h1>Forgot password</h1>
        <p class="auth-subtitle">Enter your email and we'll send you a reset link.</p>

        <?php if ($success): ?>

            <div class="auth-alert success">
                If that email is registered, you'll receive a reset link shortly.
            </div>
            <div class="auth-footer">
                <a href="/">Back to login</a>
            </div>

        <?php else: ?>

            <form method="POST" action="/forgot-password">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="you@example.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        class="<?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                        required>
                    <?php if (!empty($errors['email'])): ?>
                        <?php foreach ($errors['email'] as $error): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary">Send reset link</button>
            </form>

            <div class="auth-footer">
                <a href="/">Back to login</a>
            </div>

        <?php endif; ?>

    </div>
</div>