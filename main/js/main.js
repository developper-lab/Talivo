document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelector('.slides');
    const images = document.querySelectorAll('.slides img');
    const prevbutton = document.querySelector('.prev');
    const nextbutton = document.querySelector('.next');

    let index = 0;
    let interval = setInterval(() => showSlide(index+1), 5000);

    function showSlide(i){
        if (i < 0) index = images.length - 1;
        else if (i >= images.length) index = 0;
        else index = i;
        slides.style.transform = `translateX(${-index * 100}%)`;
    }

    function resetInterval(){
        clearInterval(interval)
        interval = setInterval(() => showSlide(index+1), 5000)
    }

    prevbutton.addEventListener('click', () =>{
        showSlide(index - 1);
        resetInterval();
    });

    nextbutton.addEventListener('click', () => {
        showSlide(index + 1);
        resetInterval();
    });
});
    