<style>
    .slider {
        display: none;
    }

  @media(max-width: 600px){
    .slider {
        position: relative;
        width: 100%;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        touch-action: pan-y;
        overflow: hidden;
    }

    .slide-track {
        display: flex;
        gap: 25px;
    }

    .slide {
        width: 55%;
        height: 135px;
        border-radius: 15px;
        overflow: hidden;
        flex-shrink: 0;
        transition: transform 1s ease, opacity 0.4s ease;
    }

    .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
  }
    

</style>


@if(!empty($images) && count($images) > 0)
<div class="slider">
    
    <div class="slide-track">
        @foreach($images as $image)
            <div class="slide">
                <img src="{{ RvMedia::getImageUrl($image) }}" alt="Slide image">
            </div>
        @endforeach
    </div>
</div>



<script>
    const track = document.querySelector(".slide-track");
    const slides = Array.from(document.querySelectorAll(".slide"));

    if (slides.length > 0) {
        // Clone đầu – cuối
        const firstClone = slides[0].cloneNode(true);
        const lastClone = slides[slides.length - 1].cloneNode(true);

        track.appendChild(firstClone);
        track.insertBefore(lastClone, slides[0]);

        let allSlides = Array.from(document.querySelectorAll(".slide"));

        let index = 0;
        let speed = 2500;
        let isTransitioning = false;

        function updateSlides(animate = true) {
            if (!animate) {
                track.style.transition = "none";

                // Tắt transition cho mọi slide để tránh nháy
                allSlides.forEach(slide => {
                    slide.style.transition = "none";
                });
            } else {
                track.style.transition = "transform 0.55s ease";

                // Bật lại transition của slide
                allSlides.forEach(slide => {
                    slide.style.transition = "transform 0.4s ease, opacity 0.4s ease";
                });
            }

            // Scale + opacity theo index
            allSlides.forEach((slide, i) => {
                const diff = Math.abs(i - index);
                
                if (i === index) {
                    slide.style.transform = "scale(1.2)"; // Ảnh giữa
                    slide.style.opacity = "1";
                } else if (diff === 1) {
                    slide.style.transform = "scale(0.9)"; // Ảnh 2 bên
                    slide.style.opacity = "0.8";
                } else {
                    slide.style.transform = "scale(0.60)";
                    slide.style.opacity = "0.4";
                }
            });

            // Căn giữa slide
            const offset =
                -index * (allSlides[0].offsetWidth + 20) +
                window.innerWidth / 2 -
                allSlides[0].offsetWidth / 2;

            track.style.transform = `translateX(${offset}px)`;
        }

        function nextSlide() {
            if (isTransitioning) return;
            isTransitioning = true;

            index++;
            updateSlides(true);

            setTimeout(() => {
                if (index === allSlides.length - 1) {
                    index = 1;
                    requestAnimationFrame(() => updateSlides(false));
                }
                isTransitioning = false;
            }, 470);
        }

        function prevSlide() {
            if (isTransitioning) return;
            isTransitioning = true;

            index--;
            updateSlides(true);

            setTimeout(() => {
                if (index === 0) {
                    index = allSlides.length - 2;
                    requestAnimationFrame(() => updateSlides(false));
                }
                isTransitioning = false;
            }, 470);
        }

        // Khởi tạo
        updateSlides(false);
        let auto = setInterval(nextSlide, speed);

        // Swipe
        let startX = 0;
        track.addEventListener("touchstart", e => {
            clearInterval(auto);
            startX = e.touches[0].clientX;
        });

        track.addEventListener("touchend", e => {
            const diff = e.changedTouches[0].clientX - startX;

            if (diff > 50) prevSlide();
            if (diff < -50) nextSlide();

            auto = setInterval(nextSlide, speed);
        });
    }
</script>
@endif