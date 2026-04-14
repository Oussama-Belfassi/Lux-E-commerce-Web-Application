<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/<?= $file ?>.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <title><?= $title ?></title>
</head>
<style>
    body {
        opacity: 0;
    }
</style>

<body>

    <?php $currentPage = basename($view); ?>

    <?php if (!in_array($currentPage, ['login', 'register', '404', '403', '500', 'reset-password', 'forgot-password'])): ?>

        <?php if ($isGuest): ?>
            <nav>
                <div class="nav-logo">Lux<span>é</span></div>
                <div class="nav-links">
                    <a href="/home">New Arrivals</a>
                    <a href="/home">Collections</a>
                    <a href="/home">Sale</a>
                    <a href="/home">About</a>
                    <a href="/contact">Contact</a>
                </div>
                <div class="nav-actions">
                    <div class="cart-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 01-8 0" />
                        </svg>
                        <div class="cart-badge">3</div>
                    </div>
                    <a href="/" class="btn-login">Log in</a>
                    <a href="/register" class="btn-register">Register</a>
                </div>
            </nav>
        <?php else: ?>
            <nav>
                <div class="nav-logo">Lux<span>é</span></div>
                <div class="nav-links">
                    <a href="/home">New Arrivals</a>
                    <a href="/home">Collections</a>
                    <a href="/home">Sale</a>
                    <a href="/home">About</a>
                    <a href="/contact">Contact</a>
                </div>
                <div class="nav-actions">
                    <div class="cart-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 01-8 0" />
                        </svg>
                        <div class="cart-badge">3</div>
                    </div>

                    <!-- FIX 1: action changed from "/" to "/logout" -->
                    <!-- FIX 2: csrf_token read through Session class, not raw $_SESSION -->
                    <form method="POST" action="/logout" class="form">
                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <div class="container">
        <?php if ($flashSuccess): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($flashSuccess) ?>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </div>

    <script>
        // Fade in — runs on every page load including cache
        window.addEventListener('pageshow', () => {
            document.body.style.transition = 'none';
            document.body.style.opacity = '0';

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    document.body.style.transition = 'opacity 0.3s ease';
                    document.body.style.opacity = '1';
                });
            });
        });

        // Fade out — single listener on the whole document
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');

            if (!link) return;

            const href = link.getAttribute('href');

            if (
                !href ||
                href.startsWith('#') ||
                href.startsWith('http') ||
                href.startsWith('mailto') ||
                href.startsWith('tel') ||
                link.target === '_blank' ||
                e.ctrlKey || e.metaKey || e.shiftKey // allow open in new tab
            ) return;

            e.preventDefault();

            document.body.style.transition = 'opacity 0.3s ease';
            document.body.style.opacity = '0';

            setTimeout(() => {
                window.location.href = href;
            }, 200);
        });
    </script>
</body>

</html>