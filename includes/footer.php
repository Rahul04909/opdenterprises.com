<footer>
        <div class="footer-container">
            <div class="footer-grid">
                
                <!-- Company Overview Column -->
                <div class="brand-info">
                    <div class="footer-logo">
                        <img src="assets/images/logo.png" alt="O.P Defence Enterprises" class="footer-logo-img">
                    </div>
                    <p class="company-desc">
                        Leading manufacturer and service provider of industrial Sand Blasting Machines, Shot Blasting Machines, Paint Booths, and high-precision spare parts.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-btn" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="social-btn" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="social-btn" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="social-btn" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Quick Navigation Column -->
                <div>
                    <h2 class="footer-heading">Quick Links</h2>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Company Profile</a></li>
                        <li><a href="#">Quality Assurance</a></li>
                        <li><a href="#">Client Testimonials</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Product Range Column -->
                <div>
                    <h2 class="footer-heading">Our Range</h2>
                    <ul class="footer-links">
                        <li><a href="#">Sand Blasting Machine</a></li>
                        <li><a href="#">Shot Blasting Machine</a></li>
                        <li><a href="#">Blasting Service</a></li>
                        <li><a href="#">Industrial Paint Booth</a></li>
                        <li><a href="#">Blasting Machine Spare Parts</a></li>
                    </ul>
                </div>

                <!-- Direct Contact Details Column -->
                <div>
                    <h2 class="footer-heading">Contact Info</h2>
                    <ul class="contact-info-list">
                        <li class="contact-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Plot No. 134, Sector 4, UIT, Bhiwadi-301019</span>
                        </li>
                        <li class="contact-item">
                            <i class="fa-solid fa-phone"></i>
                            <span><strong>Call:</strong> 09071606342</span>
                        </li>
                        <li class="contact-item">
                            <i class="fa-solid fa-id-card"></i>
                            <span><strong>GSTIN:</strong> 08BVDPV3183L1ZY</span>
                        </li>
                        <li class="contact-item">
                            <i class="fa-regular fa-clock"></i>
                            <span><strong>Working Hours:</strong> Mon - Sat (9:00 AM - 6:00 PM)</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Sub Footer Bar -->
        <div class="footer-bottom">
            <div class="footer-container">
                <div class="bottom-content">
                    <p>&copy; <span id="currentYear"></span> O.P Defence Enterprises. All Rights Reserved.</p>
                    <div class="bottom-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll To Top Button -->
    <button class="scroll-top-btn" id="scrollTopBtn" aria-label="Back to Top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- JavaScript Logic -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Dynamic Copyright Year
            document.getElementById("currentYear").textContent = new Date().getFullYear();

            // Scroll-To-Top Button Functionality
            const scrollTopBtn = document.getElementById("scrollTopBtn");

            window.addEventListener("scroll", function () {
                if (window.scrollY > 300) {
                    scrollTopBtn.classList.add("visible");
                } else {
                    scrollTopBtn.classList.remove("visible");
                }
            });

            scrollTopBtn.addEventListener("click", function () {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            });
        });
    </script>