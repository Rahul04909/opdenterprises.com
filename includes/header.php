<header>
        <!-- Top Row: Logo, Info, Navigation & Actions -->
        <div class="top-header">
            <div class="brand-container">
                <div class="logo-wrapper">
                    <img src="favicon.png" alt="O.P Defence Enterprises" class="logo-img">
                </div>
                <div class="company-details">
                    <h1 class="company-name">O.P Defence Enterprises</h1>
                    <div class="company-meta">
                        <div class="meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Plot No. 134, Sector 4, UIT, Bhiwadi-301019</span>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>GST No. <strong>08BVDPV3183L1ZY</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Primary Navigation -->
            <nav class="top-nav">
                <a href="#" class="nav-item active">HOME</a>
                <a href="#" class="nav-item">PROFILE</a>
                <a href="#" class="nav-item">CONTACT US</a>
            </nav>

            <!-- Call & Enquiry Actions -->
            <div class="action-buttons">
                <a href="tel:09079106342" class="btn-call">
                    <i class="fa-solid fa-phone"></i>
                    <div class="call-info">
                        <span class="phone-number">Call 09079106342</span>
                        <span class="response-rate">100% Response Rate</span>
                    </div>
                </a>
                <button class="btn-enquiry" id="enquiryBtn">
                    <i class="fa-regular fa-envelope"></i>
                    <span>Send Enquiry</span>
                </button>
            </div>
        </div>

        <!-- Bottom Row: Products Range & Search Bar -->
        <div class="bottom-header">
            <div class="bottom-container">
                <ul class="category-nav">
                    <li class="range-title">OUR RANGE</li>
                    <li class="cat-item">
                        <a href="#" class="cat-link">Sand Blasting Machine</a>
                        <span class="cat-divider"></span>
                    </li>
                    <li class="cat-item">
                        <a href="#" class="cat-link">Shot Blasting Machine</a>
                        <span class="cat-divider"></span>
                    </li>
                    <li class="cat-item">
                        <a href="#" class="cat-link">Blasting Service</a>
                        <span class="cat-divider"></span>
                    </li>
                    <li class="cat-item">
                        <a href="#" class="cat-link">Paint Booth</a>
                        <span class="cat-divider"></span>
                    </li>
                    <li class="cat-item">
                        <a href="#" class="cat-link">Shot Blasting Machine Spare Parts</a>
                    </li>
                </ul>

                <div class="search-box">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search Products/Services">
                    <button class="search-btn" id="searchBtn" aria-label="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- JavaScript Interaction Logic -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Enquiry Button Click Action
            const enquiryBtn = document.getElementById("enquiryBtn");
            enquiryBtn.addEventListener("click", function () {
                alert("Opening Enquiry Modal / Form...");
            });

            // Search Trigger Logic
            const searchInput = document.getElementById("searchInput");
            const searchBtn = document.getElementById("searchBtn");

            function performSearch() {
                const query = searchInput.value.trim();
                if (query !== "") {
                    alert("Searching for: " + query);
                    // Add your PHP search redirect or AJAX call here:
                    // window.location.href = 'search.php?q=' + encodeURIComponent(query);
                }
            }

            searchBtn.addEventListener("click", performSearch);
            searchInput.addEventListener("keypress", function (e) {
                if (e.key === "Enter") {
                    performSearch();
                }
            });
        });
    </script>