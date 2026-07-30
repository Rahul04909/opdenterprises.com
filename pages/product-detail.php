<?php
require_once __DIR__ . '/../includes/config.php';

$pdo = getDBConnection();
$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    echo '<div class="page-container"><h1>Product not found</h1><a href="index.php">&larr; Back to Home</a></div>';
    exit;
}

// Auto-create reviews table
$pdo->exec("CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT '',
    rating TINYINT(1) NOT NULL DEFAULT 5,
    review TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Handle review submission
$reviewMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rName = trim($_POST['review_name'] ?? '');
    $rEmail = trim($_POST['review_email'] ?? '');
    $rRating = (int)($_POST['rating'] ?? 5);
    $rReview = trim($_POST['review_text'] ?? '');
    if ($rName && $rRating >= 1 && $rRating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO product_reviews (product_id, name, email, rating, review, status) VALUES (?,?,?,?,?,1)");
        $stmt->execute([$product['id'], $rName, $rEmail, $rRating, $rReview]);
        $reviewMsg = 'Thank you for your review!';
    } else {
        $reviewMsg = 'Please fill in your name and rating.';
    }
}

// Reviews
$revStmt = $pdo->prepare("SELECT name, rating, review, created_at FROM product_reviews WHERE product_id = ? AND status = 1 ORDER BY created_at DESC");
$revStmt->execute([$product['id']]);
$reviews = $revStmt->fetchAll();

$avgRating = 0;
$ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
if (!empty($reviews)) {
    $total = 0;
    foreach ($reviews as $r) {
        $total += $r['rating'];
        $ratingCounts[(int)$r['rating']]++;
    }
    $avgRating = round($total / count($reviews), 1);
}

// Gallery
$galStmt = $pdo->prepare("SELECT image_path FROM product_gallery WHERE product_id = ? ORDER BY sort_order");
$galStmt->execute([$product['id']]);
$gallery = $galStmt->fetchAll();

// Specs
$specStmt = $pdo->prepare("SELECT label, value FROM product_specifications WHERE product_id = ? ORDER BY id");
$specStmt->execute([$product['id']]);
$specs = $specStmt->fetchAll();

// Related products
$relStmt = $pdo->query("SELECT id, name, slug, short_description, featured_image FROM products WHERE status = 1 AND id != " . (int)$product['id'] . " ORDER BY RAND() LIMIT 4");
$related = $relStmt->fetchAll();

$baseUrl = 'https://opdenterprises.com';
$prodUrl = $baseUrl . '/?page=product&slug=' . urlencode($product['slug']);
$prodImg = $product['featured_image'] ? $baseUrl . '/' . $product['featured_image'] : $baseUrl . '/favicon.png';

function starHTML($rating) {
    $h = '';
    for ($i = 1; $i <= 5; $i++) {
        $h .= $i <= $rating
            ? '<i class="fa-solid fa-star"></i>'
            : ($i - 0.5 <= $rating ? '<i class="fa-solid fa-star-half-alt"></i>' : '<i class="fa-regular fa-star"></i>');
    }
    return $h;
}
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>",
    "description": "<?= htmlspecialchars(strip_tags($product['short_description'] ?: $product['description']), ENT_QUOTES) ?>",
    "image": "<?= $prodImg ?>",
    "url": "<?= $prodUrl ?>",
    "brand": {"@type": "Brand", "name": "O.P Defence Enterprises"},
    <?php if ($avgRating > 0): ?>
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?= $avgRating ?>",
        "bestRating": "5",
        "ratingCount": "<?= count($reviews) ?>"
    },
    <?php endif; ?>
    "offers": {
        "@type": "Offer",
        "availability": "https://schema.org/InStock",
        "priceCurrency": "INR",
        "price": "0",
        "url": "<?= $prodUrl ?>"
    }
}
</script>

<!-- Breadcrumbs -->
<div class="pd-breadcrumbs">
    <div class="pd-container">
        <a href="index.php">Home</a>
        <span class="sep">&rsaquo;</span>
        <span class="current"><?= htmlspecialchars($product['name']) ?></span>
    </div>
</div>

<!-- Main Product Section -->
<section class="pd-main">
    <div class="pd-container">
        <div class="pd-layout">

            <!-- LEFT: Gallery -->
            <div class="pd-gallery">
                <div class="pd-main-image">
                    <img id="pdMainImg"
                         src="<?= $product['featured_image'] ? htmlspecialchars($product['featured_image']) : 'https://placehold.co/600x600/e2e8f0/1f2937?text=' . urlencode($product['name']) ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <?php if (!empty($gallery)): ?>
                <div class="pd-thumbs">
                    <?php if ($product['featured_image']): ?>
                    <img src="<?= htmlspecialchars($product['featured_image']) ?>" alt="" class="pd-thumb active" onclick="document.getElementById('pdMainImg').src=this.src;document.querySelectorAll('.pd-thumb').forEach(t=>t.classList.remove('active'));this.classList.add('active');">
                    <?php endif; ?>
                    <?php foreach ($gallery as $g): ?>
                    <img src="<?= htmlspecialchars($g['image_path']) ?>" alt="" class="pd-thumb" onclick="document.getElementById('pdMainImg').src=this.src;document.querySelectorAll('.pd-thumb').forEach(t=>t.classList.remove('active'));this.classList.add('active');">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Product Info -->
            <div class="pd-info">
                <h1 class="pd-title"><?= htmlspecialchars($product['name']) ?></h1>

                <div class="pd-rating-row">
                    <span class="pd-stars"><?= starHTML((int)$avgRating) ?></span>
                    <span class="pd-rating-text">
                        <?= $avgRating ?> &bull; <?= count($reviews) ?> review<?= count($reviews) !== 1 ? 's' : '' ?>
                    </span>
                </div>

                <?php if ($product['short_description']): ?>
                <p class="pd-desc"><?= nl2br(htmlspecialchars($product['short_description'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($specs)): ?>
                <div class="pd-specs">
                    <h3>Product Specifications</h3>
                    <table class="pd-specs-table">
                        <?php foreach ($specs as $s): ?>
                        <tr>
                            <td class="pd-spec-label"><?= htmlspecialchars($s['label']) ?></td>
                            <td class="pd-spec-value"><?= htmlspecialchars($s['value']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>

                <div class="pd-actions">
                    <a href="tel:09079106342" class="pd-btn pd-btn-call"><i class="fa-solid fa-phone"></i> Call Now</a>
                    <button class="pd-btn pd-btn-enq" onclick="document.getElementById('pdEnquiryForm').scrollIntoView({behavior:'smooth'})"><i class="fa-regular fa-envelope"></i> Send Enquiry</button>
                    <a href="https://wa.me/919079106342?text=Enquiry%20for%20<?= urlencode($product['name']) ?>" target="_blank" class="pd-btn pd-btn-wa"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                </div>

                <div class="pd-share">
                    <span>Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($prodUrl) ?>" target="_blank" aria-label="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?text=<?= urlencode($product['name']) ?>&url=<?= urlencode($prodUrl) ?>" target="_blank" aria-label="Share on Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($prodUrl) ?>" target="_blank" aria-label="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Full Description -->
<?php if ($product['description']): ?>
<section class="pd-section">
    <div class="pd-container">
        <h2 class="pd-section-title">Product Description</h2>
        <div class="pd-description-content"><?= $product['description'] ?></div>
    </div>
</section>
<?php endif; ?>

<!-- Rating & Reviews -->
<section class="pd-section pd-bg-light" id="pdReviews">
    <div class="pd-container">
        <h2 class="pd-section-title">Ratings & Reviews</h2>

        <div class="pd-reviews-layout">
            <div class="pd-rating-summary">
                <div class="pd-rating-big"><?= $avgRating ?></div>
                <div class="pd-stars pd-stars-big"><?= starHTML((int)$avgRating) ?></div>
                <div class="pd-rating-total"><?= count($reviews) ?> review<?= count($reviews) !== 1 ? 's' : '' ?></div>

                <div class="pd-rating-bars">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <div class="pd-bar-row">
                        <span class="pd-bar-label"><?= $i ?> <i class="fa-solid fa-star"></i></span>
                        <div class="pd-bar-track">
                            <div class="pd-bar-fill" style="width:<?= count($reviews) > 0 ? ($ratingCounts[$i] / count($reviews) * 100) : 0 ?>%"></div>
                        </div>
                        <span class="pd-bar-count"><?= $ratingCounts[$i] ?></span>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="pd-reviews-list">
                <?php if ($reviewMsg): ?>
                <div class="pd-review-msg"><?= htmlspecialchars($reviewMsg) ?></div>
                <?php endif; ?>

                <?php if (empty($reviews)): ?>
                <p class="pd-no-reviews">No reviews yet. Be the first to review!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                    <div class="pd-review-card">
                        <div class="pd-review-header">
                            <span class="pd-review-name"><?= htmlspecialchars($rev['name']) ?></span>
                            <span class="pd-review-stars"><?= starHTML((int)$rev['rating']) ?></span>
                        </div>
                        <?php if ($rev['review']): ?>
                        <p class="pd-review-text"><?= htmlspecialchars($rev['review']) ?></p>
                        <?php endif; ?>
                        <span class="pd-review-date"><?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="pd-review-form" id="pdEnquiryForm">
                    <h3>Write a Review</h3>
                    <form method="POST">
                        <div class="pd-form-row">
                            <div class="pd-form-group">
                                <label>Name <span class="req">*</span></label>
                                <input type="text" name="review_name" required>
                            </div>
                            <div class="pd-form-group">
                                <label>Email</label>
                                <input type="email" name="review_email">
                            </div>
                        </div>
                        <div class="pd-form-group">
                            <label>Rating <span class="req">*</span></label>
                            <div class="pd-star-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
                                <label for="star<?= $i ?>"><i class="fa-solid fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="pd-form-group">
                            <label>Your Review</label>
                            <textarea name="review_text" rows="4"></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="pd-btn pd-btn-submit">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enquiry Form -->
<section class="pd-section" id="pdEnquiry">
    <div class="pd-container">
        <h2 class="pd-section-title">Get Best Price for <?= htmlspecialchars($product['name']) ?></h2>
        <form class="pd-enquiry-form" onsubmit="return submitProductEnquiry(event)">
            <input type="hidden" name="product" value="<?= htmlspecialchars($product['name']) ?>">
            <div class="pd-form-row">
                <div class="pd-form-group">
                    <label>Your Name <span class="req">*</span></label>
                    <input type="text" name="enq_name" id="enq_name" required>
                </div>
                <div class="pd-form-group">
                    <label>Phone Number <span class="req">*</span></label>
                    <input type="tel" name="enq_phone" id="enq_phone" required>
                </div>
            </div>
            <div class="pd-form-row">
                <div class="pd-form-group">
                    <label>Email Address</label>
                    <input type="email" name="enq_email" id="enq_email">
                </div>
                <div class="pd-form-group">
                    <label>Quantity Required</label>
                    <select name="enq_quantity" id="enq_quantity">
                        <option value="1 Unit">1 Unit</option>
                        <option value="2 Units">2 Units</option>
                        <option value="3 Units">3 Units</option>
                        <option value="5 Units">5 Units</option>
                        <option value="10+ Units">10+ Units</option>
                        <option value="Bulk / Custom Quote">Bulk / Custom Quote</option>
                    </select>
                </div>
            </div>
            <div class="pd-form-group">
                <label>Message / Requirements</label>
                <textarea name="enq_message" id="enq_message" rows="4"></textarea>
            </div>
            <button type="submit" class="pd-btn pd-btn-submit" id="pdEnqBtn"><i class="fa-regular fa-paper-plane"></i> Send Enquiry</button>
            <div id="pdEnqStatus" style="margin-top:12px;"></div>
        </form>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($related)): ?>
<section class="pd-section pd-bg-light">
    <div class="pd-container">
        <h2 class="pd-section-title">Related Products</h2>
        <div class="pd-related-grid">
            <?php foreach ($related as $r): ?>
            <a href="?page=product&slug=<?= urlencode($r['slug']) ?>" class="pd-related-card">
                <div class="pd-related-img">
                    <img src="<?= $r['featured_image'] ? htmlspecialchars($r['featured_image']) : 'https://placehold.co/300x200/e2e8f0/1f2937?text=' . urlencode($r['name']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
                </div>
                <h4><?= htmlspecialchars($r['name']) ?></h4>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function submitProductEnquiry(e) {
    e.preventDefault();
    const btn = document.getElementById('pdEnqBtn');
    const status = document.getElementById('pdEnqStatus');
    const formData = new FormData();
    formData.append('name', document.getElementById('enq_name').value);
    formData.append('phone', document.getElementById('enq_phone').value);
    formData.append('email', document.getElementById('enq_email').value);
    formData.append('quantity', document.getElementById('enq_quantity').value);
    formData.append('message', document.getElementById('enq_message').value);
    formData.append('product', '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';

    fetch('includes/enquiry.php', {
        method: 'POST',
        body: formData
    }).then(function (r) { return r.json(); }).then(function (d) {
        if (d.success) {
            status.innerHTML = '<div class="pd-success-msg">Enquiry sent! We will contact you shortly.</div>';
            e.target.reset();
        } else {
            status.innerHTML = '<div class="pd-error-msg">' + (d.error || 'Failed to send') + '</div>';
        }
    }).catch(function () {
        status.innerHTML = '<div class="pd-error-msg">Network error. Please try again.</div>';
    }).finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-regular fa-paper-plane"></i> Send Enquiry';
    });

    return false;
}
</script>
