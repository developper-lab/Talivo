const track = document.querySelector('.slider-track');
let slides = document.querySelectorAll('.slide');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

const firstClone = slides[0].cloneNode(true);
const lastClone = slides[slides.length - 1].cloneNode(true);

track.appendChild(firstClone);
track.insertBefore(lastClone, slides[0]);

slides = document.querySelectorAll('.slide');
let index = 1; const slideWidth = slides[0].offsetWidth;

track.style.transform = `translateX(-${index * slideWidth}px)`;

function setActive() {
    slides.forEach(slide => slide.classList.remove('active'));
    slides[index + 1].classList.add('active');
}

function moveToSlide() {
    track.style.transition = 'transform 0.5s ease';
    track.style.transform = `translateX(-${index * slideWidth}px)`;
    setActive();
}

nextBtn.addEventListener('click', () => {
    if (index >= slides.length - 1) return;
    index++;
    moveToSlide();
});

prevBtn.addEventListener('click', () => {
    if (index <= 0) return;
    index--;
    moveToSlide();
});

track.addEventListener('transitionend', () => {
    if (slides[index].isSameNode(firstClone)) {
        track.style.transition = 'none';
        index = 1;
        track.style.transform = `translateX(-${index * slideWidth}px)`;
        setActive();
    }
    if (slides[index].isSameNode(lastClone)) {
        track.style.transition = 'none';
        index = slides.length - 2;
        track.style.transform = `translateX(-${index * slideWidth}px)`;
        setActive();
    }
});

setActive();