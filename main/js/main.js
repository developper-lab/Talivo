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

const burger = document.querySelector('.burger');

burger.addEventListener('click', function () {
    this.classList.toggle('active');
});

document.addEventListener('click', function (event) {
    const isClickInsideNav = nav.contains(event.target);
    const isClickInsideBurger = burger.contains(event.target);

    if (!isClickInsideNav && !isClickInsideBurger && nav.classList.contains('active')) {
        burger.classList.remove('active');
    }
});


const searchInput = document.querySelector('input[type="search"]');
const searchButton = document.querySelector('.btn_search');

searchButton.addEventListener('click', function () {
    if (searchInput.value.trim() !== '') {
        alert('Поиск: ' + searchInput.value);
        searchInput.value = '';
    }
});

searchInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        searchButton.click();
    }
});