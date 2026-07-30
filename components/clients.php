<?php
$clients = [
    ['name' => 'Tata Steel', 'logo' => 'https://th.bing.com/th/id/OIP.9Bpdby4S-D2mLEvW-zxVfQHaCm'],
    ['name' => 'Larsen & Toubro', 'logo' => 'https://static.brandirectory.com/logos/TATL001_tata_steel_logo_jpg.jpg'],
    ['name' => 'Reliance Industries', 'logo' => 'https://tse3.mm.bing.net/th/id/OIP.0jxryrkmBVw0ATpx9QEzJQHaDt'],
    ['name' => 'BHEL', 'logo' => 'https://tse4.mm.bing.net/th/id/OIP.6Kc2fq73JM69OhAfbhxElQHaGN'],
    ['name' => 'Mahindra & Mahindra', 'logo' => 'https://vectorseek.com/wp-content/uploads/2023/10/Larsen-Toubro-LT-Logo-Vector.svg-.png'],
    ['name' => 'Indian Railways', 'logo' => 'https://1000logos.net/wp-content/uploads/2016/10/acc-logo-768x230.jpg'],
    ['name' => 'HAL', 'logo' => 'https://www.adgully.com/img/800/61376_vedanta-rgb.JPG'],
    ['name' => 'Ashok Leyland', 'logo' => 'https://tse3.mm.bing.net/th/id/OIP.ESnLjwD8p-G2cHTAIB9JRwHaEt'],
];

$testimonials = [
    [
        'text' => 'O.P Defence Enterprises delivered a custom shot blasting machine that exceeded our expectations. The build quality is outstanding and the after-sales support has been exceptional.',
        'name' => 'Vikram Sharma',
        'company' => 'Tata Steel',
    ],
    [
        'text' => 'We have been using their sand blasting equipment for over 3 years now. Minimal maintenance, consistent performance, and great value for money.',
        'name' => 'Rajesh Mehta',
        'company' => 'Larsen & Toubro',
    ],
    [
        'text' => 'The team understood our unique requirements and delivered a tailored solution on time. Highly recommended for industrial surface preparation equipment.',
        'name' => 'Suresh Patel',
        'company' => 'Mahindra & Mahindra',
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

        <div class="testimonials-header">
            <span class="clients-sub-heading">Testimonials</span>
            <h2 class="clients-main-heading">
                WHAT OUR <span>CLIENTS SAY</span>
            </h2>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card">
                <div class="testimonial-quote"><i class="fa-solid fa-quote-right"></i></div>
                <p class="testimonial-text"><?= htmlspecialchars($t['text']) ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar"><i class="fa-solid fa-user"></i></div>
                    <div class="testimonial-info">
                        <span class="testimonial-name"><?= htmlspecialchars($t['name']) ?></span>
                        <span class="testimonial-company"><?= htmlspecialchars($t['company']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
