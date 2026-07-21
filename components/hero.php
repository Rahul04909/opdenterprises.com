<section class="hero-carousel-section">
        <div class="carousel-container" id="heroCarousel">
            
            <!-- Slides Track -->
            <div class="carousel-track" id="carouselTrack">
                
                <!-- Slide 1 (Your Specific Image Link) -->
                <div class="slide">
                    <div class="slide-content">
                        <span class="slide-badge">Heavy Duty Machinery</span>
                        <h2 class="slide-title">Tumble Type Shot Blasting Machine</h2>
                        <p class="slide-desc">High-performance automatic tumbling system for fast cleaning, descaling, and surface finishing of small to medium components.</p>
                        <a href="#" class="slide-btn">
                            <span>Get Best Quote</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="slide-image-wrapper">
                        <img src="https://shotblasting.org.in/images/product/tumble-type.png" alt="Tumble Type Shot Blasting Machine" class="slide-image">
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="slide">
                    <div class="slide-content">
                        <span class="slide-badge">Industrial Grade</span>
                        <h2 class="slide-title">High Efficiency Sand Blasting Cabinets</h2>
                        <p class="slide-desc">Precision sandblasting machinery engineered for metal surface preparation, rust removal, and industrial applications.</p>
                        <a href="#" class="slide-btn">
                            <span>Explore Range</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="slide-image-wrapper">
                        <img src="http://googleusercontent.com/image_collection/image_retrieval/3165883106926044587_0" alt="Industrial Sand Blasting Machine" class="slide-image">
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="slide">
                    <div class="slide-content">
                        <span class="slide-badge">Custom Engineering</span>
                        <h2 class="slide-title">Advanced Industrial Paint Booths</h2>
                        <p class="slide-desc">Environment-friendly spray paint booths with optimized air flow controls for uniform, high-quality finishes.</p>
                        <a href="#" class="slide-btn">
                            <span>Request Callback</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="slide-image-wrapper">
                        <img src="http://googleusercontent.com/image_collection/image_retrieval/3165883106926044587_1" alt="Industrial Paint Booth" class="slide-image">
                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="slide">
                    <div class="slide-content">
                        <span class="slide-badge">Original Spares</span>
                        <h2 class="slide-title">Blasting Machine Spare Parts & Services</h2>
                        <p class="slide-desc">Genuine OEM spare parts, impellers, control cages, and certified maintenance services to keep your operations running continuously.</p>
                        <a href="#" class="slide-btn">
                            <span>View Parts Catalog</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="slide-image-wrapper">
                        <img src="http://googleusercontent.com/image_collection/image_retrieval/3165883106926044587_2" alt="Spare Parts and Service" class="slide-image">
                    </div>
                </div>

            </div>

            <!-- Arrows -->
            <button class="nav-btn prev" id="prevBtn" aria-label="Previous Slide">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="nav-btn next" id="nextBtn" aria-label="Next Slide">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Dots Indicators -->
            <div class="dots-container" id="dotsContainer"></div>

        </div>
    </section>

    <!-- JavaScript Carousel Engine -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const track = document.getElementById("carouselTrack");
            const slides = Array.from(track.children);
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const dotsContainer = document.getElementById("dotsContainer");
            const carousel = document.getElementById("heroCarousel");

            let currentIndex = 0;
            let autoSlideInterval;
            let startX = 0;
            let currentTranslate = 0;
            let prevTranslate = 0;
            let isDragging = false;

            // Generate Pagination Dots Dynamically
            slides.forEach((_, index) => {
                const dot = document.createElement("div");
                dot.classList.add("dot");
                if (index === 0) dot.classList.add("active");
                dot.addEventListener("click", () => goToSlide(index));
                dotsContainer.appendChild(dot);
            });

            const dots = Array.from(dotsContainer.children);

            function updateCarousel() {
                track.style.transform = `translateX(-${currentIndex * 100}%)`;
                dots.forEach((dot, index) => {
                    dot.classList.toggle("active", index === currentIndex);
                });
            }

            function goToSlide(index) {
                currentIndex = index;
                updateCarousel();
                resetAutoSlide();
            }

            function nextSlide() {
                currentIndex = (currentIndex + 1) % slides.length;
                updateCarousel();
            }

            function prevSlide() {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateCarousel();
            }

            nextBtn.addEventListener("click", () => {
                nextSlide();
                resetAutoSlide();
            });

            prevBtn.addEventListener("click", () => {
                prevSlide();
                resetAutoSlide();
            });

            // Autoplay Setup
            function startAutoSlide() {
                autoSlideInterval = setInterval(nextSlide, 4500);
            }

            function resetAutoSlide() {
                clearInterval(autoSlideInterval);
                startAutoSlide();
            }

            carousel.addEventListener("mouseenter", () => clearInterval(autoSlideInterval));
            carousel.addEventListener("mouseleave", startAutoSlide);

            // --- Mobile Touch / Swipe Gesture Support ---
            track.addEventListener("touchstart", touchStart);
            track.addEventListener("touchend", touchEnd);
            track.addEventListener("touchmove", touchMove);

            function touchStart(event) {
                startX = event.touches[0].clientX;
                isDragging = true;
                clearInterval(autoSlideInterval);
            }

            function touchMove(event) {
                if (!isDragging) return;
                const currentX = event.touches[0].clientX;
                const diff = currentX - startX;
            }

            function touchEnd(event) {
                if (!isDragging) return;
                isDragging = false;
                const endX = event.changedTouches[0].clientX;
                const diffX = endX - startX;

                if (diffX < -50) {
                    nextSlide(); // Swiped Left
                } else if (diffX > 50) {
                    prevSlide(); // Swiped Right
                }
                startAutoSlide();
            }

            // Initialize
            startAutoSlide();
        });
    </script>