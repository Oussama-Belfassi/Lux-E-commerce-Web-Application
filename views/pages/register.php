<form action="/register" method="post">
    <div class="row">
        <div class="col">
            <label class="form-label">FirstName</label>
            <div class="input-group has-validation position-relative">
                <input type="text" name="Firstname"
                    placeholder="FirstName"
                    value="<?= htmlspecialchars($userData['Firstname']) ?>"
                    class="form-control <?= isset($errors['Firstname']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['Firstname'])): ?>
                    <?php foreach ($errors['Firstname'] as $error): ?>
                        <div class="invalid-feedback"><?= $error ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col">
            <label class="form-label">LastName</label>
            <div class="input-group has-validation position-relative">
                <input type="text" name="Lastname"
                    placeholder="LastName"
                    value="<?= htmlspecialchars($userData['Lastname']) ?>"
                    class="form-control <?= isset($errors['Lastname']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['Lastname'])): ?>
                    <?php foreach ($errors['Lastname'] as $error): ?>
                        <div class="invalid-feedback"><?= $error ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="email">
        <label class="form-label">Email</label>
        <div class="input-group has-validation position-relative">
            <span class="input-group-text">@</span>
            <input type="email" name="email"
                placeholder="You@example.com"
                value="<?= htmlspecialchars($userData['email']) ?>"
                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>">
            <?php if (isset($errors['email'])): ?>
                <?php foreach ($errors['email'] as $error): ?>
                    <div class="invalid-feedback"><?= $error ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="pass">
        <label class="form-label">Password</label>
        <div class="input-group has-validation position-relative">
            <input type="password" name="password" id="r-password"
                placeholder="Create a Password"
                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
            <button type="button" class="toggle-pw" onclick="togglePw(this)">&#9679;</button>
            <?php if (isset($errors['password'])): ?>
                <?php foreach ($errors['password'] as $error): ?>
                    <div class="invalid-feedback"><?= $error ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="pass">
        <label class="form-label">Confirm password</label>
        <div class="input-group has-validation position-relative">
            <input type="password" name="confirm_password" id="r-confirm-password"
                placeholder="Repeat your password"
                class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>">
            <button type="button" class="toggle-pw" onclick="togglePw(this)">&#9679;</button>
            <?php if (isset($errors['confirm_password'])): ?>
                <?php foreach ($errors['confirm_password'] as $error): ?>
                    <div class="invalid-feedback"><?= $error ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="frame">
        <button class="custom-btn btn-3" type="submit"><span>Create account</span></button>
    </div>

    <div class="divider">or Sign up with</div>

    <div>
        <div class="oauth-row">
            <a href="/auth/google" class="oauth-btn">
                <svg class="oauth-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05" />
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                </svg>
                Google
            </a>
        </div>
    </div>

    <div class="divider"></div>

    <div class="footer">
        <span>Already have an account?</span>
        <a href="/">Sign in</a>
    </div>
</form>

<script>
    function togglePw(btn) {
        const inp = btn.previousElementSibling;
        const isText = inp.type === 'text';
        inp.type = isText ? 'password' : 'text';
        btn.innerHTML = isText ? '&#9679;' : '&#9675;';
    }
</script>