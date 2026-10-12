<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? '') ?> | Yumio Admin</title>
    <link rel="icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anton&family=Be+Vietnam+Pro:wght@400;500;600;700&family=Playfair+Display:wght@400;500&display=swap">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin">
    <?php require BASE_PATH . '/app/Views/partials/admin_sidebar.php'; ?>
    <?php require BASE_PATH . '/app/Views/partials/flash.php'; ?>

    <main class="admin-main" id="main">
        <?= $content ?>
    </main>

    <script src="<?= asset('js/main.js') ?>" defer></script>
    <script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>
