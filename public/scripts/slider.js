document.addEventListener('DOMContentLoaded', () => {
    const sliderWrapper = document.querySelector('.slider-wrapper');
    const sliderItems = document.querySelectorAll('.slider-item');
    const itemsCount = sliderItems.length;
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    if (itemsCount <= 1) {
        prevBtn.parentElement.removeChild(prevBtn);
        nextBtn.parentElement.removeChild(nextBtn);
    }
    let currentIndex = 0;

    const updateSliderPosition = () => {
        const offset = -currentIndex * 100;
        sliderWrapper.style.transform = `translateX(${offset}%)`;
    };

    prevBtn.addEventListener('click', () => {
        if (itemsCount <= 1) {
            return;
        }
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = sliderItems.length - 1;
        }
        updateSliderPosition();
    });

    nextBtn.addEventListener('click', () => {
        if (itemsCount <= 1) {
            return;
        }
        if (currentIndex < sliderItems.length - 1) {
            currentIndex++;
        } else {
            currentIndex = 0;
        }
        updateSliderPosition();
    });
});