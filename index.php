<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $pageTitle = 'O.P Defence Enterprises | India\'s Best Manufacturer of Shot Blasting Machines';
    $metaDesc = 'Leading manufacturer of Sand Blasting Machines, Shot Blasting Machines, Paint Booths, and industrial spare parts in Bhiwadi, India.';
    $canonical = 'https://opdenterprises.com/';
    $ogImage = 'favicon.png';

    if (isset($_GET['page']) && $_GET['page'] === 'product' && isset($_GET['slug'])) {
        require_once 'includes/config.php';
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT name, short_description, featured_image FROM products WHERE slug = ? AND status = 1");
        $stmt->execute([$_GET['slug']]);
        $prod = $stmt->fetch();
        if ($prod) {
            $pageTitle = htmlspecialchars($prod['name']) . ' | O.P Defence Enterprises';
            $metaDesc = htmlspecialchars(strip_tags($prod['short_description'] ?: $prod['name'] . ' - ' . $pageTitle));
            $canonical = 'https://opdenterprises.com/?page=product&slug=' . urlencode($_GET['slug']);
            $ogImage = $prod['featured_image'] ? 'https://opdenterprises.com/' . $prod['featured_image'] : 'favicon.png';
        }
    }
    ?>
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $metaDesc ?>">
    <link rel="canonical" href="<?= $canonical ?>">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $metaDesc ?>">
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:url" content="<?= $canonical ?>">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/hero.css">
    <link rel="stylesheet" href="assets/css/front-about.css">
    <link rel="stylesheet" href="assets/css/products.css">
    <link rel="stylesheet" href="assets/css/clients.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/pages.css">
    <link rel="stylesheet" href="assets/css/product-detail.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php
    $page = $_GET['page'] ?? 'home';
    switch ($page) {
        case 'contact':
            include 'pages/contact.php';
            break;
        case 'privacy-policy':
            include 'pages/privacy-policy.php';
            break;
        case 'terms-of-service':
            include 'pages/terms-of-service.php';
            break;
        case 'product':
            include 'pages/product-detail.php';
            break;
        default:
            include 'components/hero.php';
            include 'components/about.php';
            include 'components/products.php';
            include 'components/clients.php';
    }
    ?>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
