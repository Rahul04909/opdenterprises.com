<section class="hero-carousel-section">
    <div class="carousel-container" id="heroCarousel">
        <div class="carousel-track" id="carouselTrack">
            <div class="slide">
                <a href="#" class="slide-link">
                    <img src="https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif" alt="Tumble Type Shot Blasting Machine" class="slide-image" loading="eager">
                </a>
            </div>
            <div class="slide">
                <a href="#" class="slide-link">
                    <img src="https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif" alt="Industrial Sand Blasting Machine" class="slide-image" loading="lazy">
                </a>
            </div>
            <div class="slide">
                <a href="#" class="slide-link">
                    <img src="https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif" alt="Industrial Paint Booth" class="slide-image" loading="lazy">
                </a>
            </div>
            <div class="slide">
                <a href="#" class="slide-link">
                    <img src="https://jhdindustrialsolution.com/storage/c8238112-5e9a-4a68-b5b1-e2bb4246ae2a.avif" alt="Spare Parts and Service" class="slide-image" loading="lazy">
                </a>
            </div>
        </div>

        <button class="nav-btn prev" id="prevBtn" aria-label="Previous Slide">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button class="nav-btn next" id="nextBtn" aria-label="Next Slide">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <div class="dots-container" id="dotsContainer"></div>
    </div>
</section>

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
        let isDragging = false;

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

        nextBtn.addEventListener("click", () => { nextSlide(); resetAutoSlide(); });
        prevBtn.addEventListener("click", () => { prevSlide(); resetAutoSlide(); });

        function startAutoSlide() {
            autoSlideInterval = setInterval(nextSlide, 4500);
        }

        function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            startAutoSlide();
        }

        carousel.addEventListener("mouseenter", () => clearInterval(autoSlideInterval));
        carousel.addEventListener("mouseleave", startAutoSlide);

        // Touch / Swipe
        track.addEventListener("touchstart", (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
            clearInterval(autoSlideInterval);
        });

        track.addEventListener("touchend", (e) => {
            if (!isDragging) return;
            isDragging = false;
            const diffX = e.changedTouches[0].clientX - startX;
            if (diffX < -50) nextSlide();
            else if (diffX > 50) prevSlide();
            startAutoSlide();
        });

        startAutoSlide();
    });
</script>
