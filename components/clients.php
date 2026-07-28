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

    </div>
</section>
