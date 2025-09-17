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