<?php

/** @var array $errors */
/** @var bool $tokenValid */
/** @var string $token */ ?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
        </div>

        <h1>Reset password</h1>
        <p class="auth-subtitle">Choose a new password for your account.</p>

        <?php if (!$tokenValid): ?>

            <div class="auth-alert error">
                <?= htmlspecialchars($errors['token'][0]) ?>
            </div>
            <div class="auth-footer">
                <a href="/forgot-password">Request a new reset link</a>
            </div>

        <?php else: ?>

            <form method="POST" action="/reset-password?token=<?= htmlspecialchars($token) ?>">

                <div class="form-group">
                    <label for="password">New password</label>
                    <div class="password-wrap">
                        <input
                            type="password"
                            id="r-password"
                            name="password"
                            placeholder="Min. 8 characters"
                            minlength="8"
                            maxlength="24"
                            class="<?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            required>
                        <button type="button" class="toggle-pw" onclick="togglePw('r-password', this)">&#9679;</button>
                    </div>
                    <?php if (!empty($errors['password'])): ?>
                        <?php foreach ($errors['password'] as $error): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm new password</label>
                    <div class="password-wrap">
                        <input
                            type="password"
                            id="r-confirm-password"
                            name="confirm_password"
                            placeholder="Repeat password"
                            minlength="8"
                            maxlength="24"
                            class="<?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                            required>
                        <button type="button" class="toggle-pw" onclick="togglePw('r-confirm-password', this)">&#9679;</button>
                    </div>
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <?php foreach ($errors['confirm_password'] as $error): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary">Reset password</button>

            </form>

        <?php endif; ?>

    </div>
</div>

<script>
    function togglePw(inputId, btn) {
        const inp = document.getElementById(inputId);
        const isText = inp.type === 'text';
        inp.type = isText ? 'password' : 'text';
        btn.innerHTML = isText ? '&#9679;' : '&#9675;';
    }
</script>