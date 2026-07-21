<?php
$clients = [
    ['name' => 'Tata Steel', 'logo' => 'https://placehold.co/400x200/0b4a83/ffffff?text=TATA+STEEL'],
    ['name' => 'Larsen & Toubro', 'logo' => 'https://placehold.co/400x200/f26522/ffffff?text=L%26T'],
    ['name' => 'Reliance Industries', 'logo' => 'https://placehold.co/400x200/0b4a83/ffffff?text=RELIANCE'],
    ['name' => 'BHEL', 'logo' => 'https://placehold.co/400x200/1f2937/ffffff?text=BHEL'],
    ['name' => 'Mahindra & Mahindra', 'logo' => 'https://placehold.co/400x200/f26522/ffffff?text=MAHINDRA'],
    ['name' => 'Indian Railways', 'logo' => 'https://placehold.co/400x200/0b4a83/ffffff?text=INDIAN+RAILWAYS'],
    ['name' => 'HAL', 'logo' => 'https://placehold.co/400x200/07355f/ffffff?text=HAL'],
    ['name' => 'Ashok Leyland', 'logo' => 'https://placehold.co/400x200/f26522/ffffff?text=ASHOK+LEYLAND'],
];

$testimonials = [
    [
        'text' => 'O.P Defence Enterprises delivered a custom shot blasting solution that exceeded our expectations. The build quality is outstanding and the team provided excellent after-sales support.',
        'name' => 'Rajesh Mehta',
        'company' => 'Tata Steel',
        'avatar' => 'https://placehold.co/80x80/0b4a83/ffffff?text=RM',
    ],
    [
        'text' => 'We have been using their sand blasting machines for over 3 years now. Reliable, durable, and cost-effective. Highly recommended for industrial surface preparation.',
        'name' => 'Suresh Patil',
        'company' => 'Larsen & Toubro',
        'avatar' => 'https://placehold.co/80x80/f26522/ffffff?text=SP',
    ],
    [
        'text' => 'The paint booth installation was seamless and the performance is exceptional. Their team understood our requirements perfectly and delivered on time.',
        'name' => 'Amit Verma',
        'company' => 'BHEL',
        'avatar' => 'https://placehold.co/80x80/07355f/ffffff?text=AV',
    ],
];
?>
<section class="clients-section">
    <div class="clients-container">

        <div class="clients-header">
            <span class="clients-sub-heading">Our Clients</span>
            <h2 class="clients-main-heading">
                TRUSTED BY <span>INDUSTRY LEADERS</span>
            </h2>
            <p class="clients-subtitle">
                We are proud to serve some of the most reputed names in the industrial sector across India and beyond.
            </p>
        </div>

        <div class="clients-stats">
            <div class="client-stat">
                <span class="client-stat-number">200+</span>
                <span class="client-stat-label">Happy Clients</span>
            </div>
            <div class="client-stat">
                <span class="client-stat-number">3000+</span>
                <span class="client-stat-label">Machines Delivered</span>
            </div>
            <div class="client-stat">
                <span class="client-stat-number">10+</span>
                <span class="client-stat-label">Countries Served</span>
            </div>
            <div class="client-stat">
                <span class="client-stat-number">15+</span>
                <span class="client-stat-label">Years Experience</span>
            </div>
        </div>

        <div class="client-grid">
            <?php foreach ($clients as $client): ?>
                <div class="client-card" title="<?= htmlspecialchars($client['name']) ?>">
                    <img
                        src="<?= htmlspecialchars($client['logo']) ?>"
                        alt="<?= htmlspecialchars($client['name']) ?>"
                        class="client-logo"
                        loading="lazy"
                    >
                </div>
            <?php endforeach; ?>
        </div>

        <div class="testimonials-grid">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fa-solid fa-quote-right"></i>
                    </div>
                    <p class="testimonial-text"><?= htmlspecialchars($testimonial['text']) ?></p>
                    <div class="testimonial-author">
                        <img
                            src="<?= htmlspecialchars($testimonial['avatar']) ?>"
                            alt="<?= htmlspecialchars($testimonial['name']) ?>"
                            class="testimonial-avatar"
                            loading="lazy"
                            onerror="this.style.display='none'"
                        >
                        <div class="testimonial-info">
                            <span class="testimonial-name"><?= htmlspecialchars($testimonial['name']) ?></span>
                            <span class="testimonial-company"><?= htmlspecialchars($testimonial['company']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
