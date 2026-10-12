<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fffdf7">
    <title><?= e($title ?? '') ?> | <?= e(config('app')['name']) ?></title>
    <link rel="icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anton&family=Be+Vietnam+Pro:wght@400;500;600;700&family=Playfair+Display:wght@400;500&display=swap">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <a class="skip-link" href="#main">Bỏ qua để tới nội dung</a>

    <?php require BASE_PATH . '/app/Views/partials/header.php'; ?>
    <?php require BASE_PATH . '/app/Views/partials/flash.php'; ?>

    <main id="main">
        <?= $content ?>
    </main>

    <?php require BASE_PATH . '/app/Views/partials/footer.php'; ?>

    <script>window.YUMIO = { csrf: <?= json_encode(csrf_token()) ?>, cartUrl: <?= json_encode(url('/cart')) ?>, checkoutUrl: <?= json_encode(url('/checkout')) ?> };</script>
    <script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
