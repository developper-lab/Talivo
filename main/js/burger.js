const burger = document.querySelector('.burger');

burger.addEventListener('click', function () {
    this.classList.toggle('active');
});

document.addEventListener('click', function (event) {
    // const isClickInsideNav = nav.contains(event.target);
    const isClickInsideBurger = burger.contains(event.target);

    // if (!isClickInsideNav && !isClickInsideBurger && nav.classList.contains('active')) {
    //     burger.classList.remove('active');
    // }
    if (!isClickInsideBurger) {
        burger.classList.remove('active');
    }
});