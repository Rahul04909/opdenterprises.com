<?php
$categories = ['All', 'Sand Blasting', 'Shot Blasting', 'Blasting Service', 'Paint Booth', 'Spare Parts'];

$products = [
    [
        'name' => 'Portable Sand Blasting Machine',
        'category' => 'Sand Blasting',
        'desc' => 'Heavy-duty portable sand blasting machine ideal for surface preparation, rust removal, and cleaning of metal structures.',
        'specs' => ['Capacity: 100L', 'Pressure: 8-10 Bar', 'Weight: 45 kg'],
        'rating' => 4.5,
        'reviews' => 24,
        'image' => 'https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif',
        'badge' => 'Bestseller',
    ],
    [
        'name' => 'Tumble Type Shot Blasting Machine',
        'category' => 'Shot Blasting',
        'desc' => 'Automatic tumble type shot blasting machine for cleaning and surface finishing of small to medium castings and forgings.',
        'specs' => ['Capacity: 600 kg', 'Power: 15 kW', 'Drum Vol: 200L'],
        'rating' => 4.8,
        'reviews' => 36,
        'image' => 'https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif',
        'badge' => 'Popular',
    ],
    [
        'name' => 'Industrial Paint Booth',
        'category' => 'Paint Booth',
        'desc' => 'Down-draft industrial paint booth with advanced filtration system for automotive and industrial painting applications.',
        'specs' => ['Size: 6x4x3 m', 'Fan: 30 HP', 'Filtration: HEPA'],
        'rating' => 4.3,
        'reviews' => 18,
        'image' => 'https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif',
        'badge' => '',
    ],
    [
        'name' => 'Shot Blasting Machine Spare Parts',
        'category' => 'Spare Parts',
        'desc' => 'High-quality replacement spare parts including blades, impellers, liners, and control cages for all shot blasting machines.',
        'specs' => ['Material: Alloy Steel', 'Hardness: 58-62 HRC', 'Warranty: 6 mo'],
        'rating' => 4.6,
        'reviews' => 42,
        'image' => 'https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif',
        'badge' => 'Trending',
    ],
    [
        'name' => 'Sand Blasting Service',
        'category' => 'Blasting Service',
        'desc' => 'Professional on-site sand blasting services for bridges, tanks, pipelines, and large industrial structures.',
        'specs' => ['Coverage: Pan India', 'Crew: 4-8 persons', 'Finish: SA 2.5'],
        'rating' => 4.7,
        'reviews' => 31,
        'image' => 'https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif',
        'badge' => '',
    ],
    [
        'name' => 'Hanger Type Shot Blasting Machine',
        'category' => 'Shot Blasting',
        'desc' => 'Continuous hanger type shot blasting system designed for high-volume surface finishing of large and heavy components.',
        'specs' => ['Load: 1000 kg/hook', 'Speed: 2-5 m/min', 'Blast Wheel: 4 nos'],
        'rating' => 4.4,
        'reviews' => 15,
        'image' => 'https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif',
        'badge' => 'New',
    ],
    [
        'name' => 'Cyclone Dust Collector',
        'category' => 'Spare Parts',
        'desc' => 'High-efficiency cyclone dust collector system for effective air filtration and particulate matter control in industrial plants.',
        'specs' => ['Air Flow: 5000 CFM', 'Efficiency: 98%', 'Motor: 10 HP'],
        'rating' => 4.2,
        'reviews' => 12,
        'image' => 'https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif',
        'badge' => '',
    ],
    [
        'name' => 'Automated Sand Blasting Cabinet',
        'category' => 'Sand Blasting',
        'desc' => 'Fully automated pressure blasting cabinet with PLC controls, dust collection, and media recovery system.',
        'specs' => ['Media: Garnet/Sand', 'Nozzle: 8-12 mm', 'Recovery: Auto'],
        'rating' => 4.9,
        'reviews' => 9,
        'image' => 'https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif',
        'badge' => '',
    ],
];

function getStars($rating) {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '';
    for ($i = 0; $i < $full; $i++) {
        $html .= '<i class="fa-solid fa-star"></i>';
    }
    if ($half) {
        $html .= '<i class="fa-solid fa-star-half-alt"></i>';
    }
    for ($i = 0; $i < $empty; $i++) {
        $html .= '<i class="fa-regular fa-star"></i>';
    }
    return $html;
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

        <div class="category-tabs" id="categoryTabs">
            <?php foreach ($categories as $cat): ?>
                <button class="category-tab <?= $cat === 'All' ? 'active' : '' ?>" data-category="<?= htmlspecialchars($cat) ?>">
                    <?= htmlspecialchars($cat) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="product-grid" id="productGrid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-category="<?= htmlspecialchars($product['category']) ?>">
                    <div class="product-image-wrapper">
                        <img
                            src="<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                            class="product-image"
                            loading="lazy"
                            onerror="this.src='https://placehold.co/600x400/e2e8f0/1f2937?text=<?= urlencode($product['name']) ?>'"
                        >
                        <?php if (!empty($product['badge'])): ?>
                            <span class="product-badge"><?= htmlspecialchars($product['badge']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <span class="product-category"><?= htmlspecialchars($product['category']) ?></span>
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-desc"><?= htmlspecialchars($product['desc']) ?></p>
                        <div class="product-specs">
                            <?php foreach ($product['specs'] as $spec): ?>
                                <span class="spec-item"><i class="fa-solid fa-circle"></i> <?= htmlspecialchars($spec) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="product-rating">
                            <span class="stars"><?= getStars($product['rating']) ?></span>
                            <span class="rating-count"><?= htmlspecialchars($product['rating']) ?> (<?= $product['reviews'] ?>)</span>
                        </div>
                        <button class="btn-enquire" data-product="<?= htmlspecialchars($product['name']) ?>">
                            <i class="fa-regular fa-envelope"></i>
                            <span>Enquire Now</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
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
                    <span id="modalProductCategory">Category</span>
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
    // Category filter
    const tabs = document.querySelectorAll('.category-tab');
    const cards = document.querySelectorAll('.product-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const category = this.dataset.category;
            cards.forEach(card => {
                if (category === 'All' || card.dataset.category === category) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Enquiry modal
    const modal = document.getElementById('enquiryModal');
    const modalClose = document.getElementById('modalClose');
    const modalProductName = document.getElementById('modalProductName');
    const modalProductCategory = document.getElementById('modalProductCategory');
    const modalProductImg = document.getElementById('modalProductImg');

    document.querySelectorAll('.btn-enquire').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('.product-card');
            const name = card.querySelector('.product-name').textContent;
            const category = card.querySelector('.product-category').textContent;
            const img = card.querySelector('.product-image').src;

            modalProductName.textContent = name;
            modalProductCategory.textContent = category;
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

    setTimeout(function () {
        btn.innerHTML = '<i class="fa-regular fa-circle-check" style="margin-right:8px;"></i>Enquiry Sent!';
        btn.style.background = '#16a34a';
        btn.disabled = false;

        setTimeout(function () {
            document.getElementById('enquiryModal').classList.remove('active');
            document.body.style.overflow = '';
            e.target.reset();
            btn.innerHTML = '<i class="fa-regular fa-paper-plane" style="margin-right:8px;"></i>Submit Enquiry';
            btn.style.background = '';
        }, 1200);
    }, 1000);

    return false;
}
</script>
