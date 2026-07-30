<?php
require_once __DIR__ . '/../includes/config.php';

$pdo = getDBConnection();
$products = [];

$stmt = $pdo->query("SELECT id, name, slug, short_description, description, featured_image FROM products WHERE status = 1 ORDER BY created_at DESC");
$allProducts = $stmt->fetchAll();

if (!empty($allProducts)) {
    $specStmt = $pdo->prepare("SELECT label, value FROM product_specifications WHERE product_id = ? ORDER BY id");

    foreach ($allProducts as $row) {
        $specStmt->execute([$row['id']]);
        $specs = $specStmt->fetchAll();
        $specLabels = array_map(function ($s) {
            return $s['label'] . ': ' . $s['value'];
        }, $specs);

        $image = $row['featured_image']
            ? $row['featured_image']
            : 'https://placehold.co/600x400/e2e8f0/1f2937?text=' . urlencode($row['name']);

        $products[] = [
            'name'  => $row['name'],
            'slug'  => $row['slug'],
            'desc'  => $row['short_description'] ?? '',
            'specs' => $specLabels,
            'image' => $image,
        ];
    }
}
?>
<section class="products-section">
    <div class="products-container">

        <div class="products-header">
            <span class="products-sub-heading">Our Range</span>
            <h2 class="products-main-heading">
                EXPLORE OUR <span>PREMIUM PRODUCTS</span>
            </h2>
        </div>

        <div class="product-grid" id="productGrid">
            <?php if (empty($products)): ?>
                <p class="text-center text-muted py-5" style="grid-column:1/-1;">No products available yet. Check back soon.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <a href="?page=product&slug=<?= urlencode($product['slug']) ?>" class="product-link">
                        <div class="product-image-wrapper">
                            <img
                                src="<?= htmlspecialchars($product['image']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                class="product-image"
                                loading="lazy"
                                onerror="this.src='https://placehold.co/600x400/e2e8f0/1f2937?text=<?= urlencode($product['name']) ?>'"
                            >
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-desc"><?= htmlspecialchars($product['desc'] ?? '') ?></p>
                            <?php if (!empty($product['specs'])): ?>
                            <div class="product-specs">
                                <?php foreach ($product['specs'] as $spec): ?>
                                    <span class="spec-item"><i class="fa-solid fa-circle"></i> <?= htmlspecialchars($spec) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        </a>
                        <div class="product-card-actions">
                            <a href="?page=product&slug=<?= urlencode($product['slug']) ?>" class="btn-view-details">
                                <i class="fa-regular fa-eye"></i> View Details
                            </a>
                            <button class="btn-enquire" data-product="<?= htmlspecialchars($product['name']) ?>">
                                <i class="fa-regular fa-envelope"></i> Enquire Now
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<div class="modal-overlay" id="enquiryModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-regular fa-paper-plane" style="margin-right:8px;color:var(--brand-orange);"></i>Product Enquiry</h3>
            <button class="modal-close" id="modalClose" aria-label="Close modal">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-product-info" id="modalProductInfo">
                <img src="" alt="" class="modal-product-img" id="modalProductImg">
                <div class="modal-product-details">
                    <h4 id="modalProductName">Product Name</h4>
                </div>
            </div>
            <form id="enquiryForm" onsubmit="return handleEnquirySubmit(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="enqName">Your Name *</label>
                        <input type="text" class="form-input" id="enqName" required placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="enqPhone">Phone Number *</label>
                        <input type="tel" class="form-input" id="enqPhone" required placeholder="Enter phone number">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="enqEmail">Email Address</label>
                    <input type="email" class="form-input" id="enqEmail" placeholder="Enter email address">
                </div>
                <div class="form-group">
                    <label class="form-label" for="enqQuantity">Quantity Required</label>
                    <select class="form-select" id="enqQuantity">
                        <option value="1">1 Unit</option>
                        <option value="2">2 Units</option>
                        <option value="3">3 Units</option>
                        <option value="5">5 Units</option>
                        <option value="10">10+ Units</option>
                        <option value="bulk">Bulk / Custom Quote</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="enqMessage">Message / Requirements</label>
                    <textarea class="form-textarea" id="enqMessage" placeholder="Tell us your specific requirements, specifications, or any questions..."></textarea>
                </div>
                <button type="submit" class="btn-submit-enquiry">
                    <i class="fa-regular fa-paper-plane" style="margin-right:8px;"></i>Submit Enquiry
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('enquiryModal');
    const modalClose = document.getElementById('modalClose');
    const modalProductName = document.getElementById('modalProductName');
    const modalProductImg = document.getElementById('modalProductImg');

    document.querySelectorAll('.btn-enquire').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('.product-card');
            const name = card.querySelector('.product-name').textContent;
            const img = card.querySelector('.product-image').src;

            modalProductName.textContent = name;
            modalProductImg.src = img;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
});

function handleEnquirySubmit(e) {
    e.preventDefault();
    const name = document.getElementById('enqName').value.trim();
    const phone = document.getElementById('enqPhone').value.trim();
    const product = document.getElementById('modalProductName').textContent;

    if (!name || !phone) return false;

    const btn = e.target.querySelector('.btn-submit-enquiry');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right:8px;"></i>Sending...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('name', name);
    formData.append('phone', phone);
    formData.append('email', document.getElementById('enqEmail').value.trim());
    formData.append('quantity', document.getElementById('enqQuantity').value);
    formData.append('message', document.getElementById('enqMessage').value.trim());
    formData.append('product', product);

    fetch('includes/enquiry.php', {
        method: 'POST',
        body: formData
    }).then(function (res) { return res.json(); }).then(function (data) {
        if (data.success) {
            btn.innerHTML = '<i class="fa-regular fa-circle-check" style="margin-right:8px;"></i>Enquiry Sent!';
            btn.style.background = '#16a34a';
        } else {
            btn.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Failed to send';
            btn.style.background = '#dc2626';
        }
        setTimeout(function () {
            document.getElementById('enquiryModal').classList.remove('active');
            document.body.style.overflow = '';
            e.target.reset();
            btn.innerHTML = '<i class="fa-regular fa-paper-plane" style="margin-right:8px;"></i>Submit Enquiry';
            btn.style.background = '';
            btn.disabled = false;
        }, 1500);
    }).catch(function () {
        btn.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>Network error';
        btn.style.background = '#dc2626';
        setTimeout(function () {
            btn.innerHTML = '<i class="fa-regular fa-paper-plane" style="margin-right:8px;"></i>Submit Enquiry';
            btn.style.background = '';
            btn.disabled = false;
        }, 2000);
    });

    return false;
}
</script>
