const burger = document.getElementById('burger');

burger.addEventListener("click", function () {
    this.classList.toggle('active');
});

document.addEventListener("click",function(event){
    const isClickInsideBurger = burger.contains(event.target);

    if(!isClickInsideBurger){
        burger.classList.remove('active');
    }
}) 