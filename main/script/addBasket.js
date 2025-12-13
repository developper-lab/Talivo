
function updateCartCount(count) {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) cartCount.textContent = count;
}

document.querySelectorAll('.add-to-basket').forEach(btn => {
    btn.addEventListener('click', () => {
        const postId = btn.dataset.postId;

        fetch('../ajax/add_to_basket.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'post_id=' + postId
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    alert('Товар добавлен в корзину');
                    updateCartCount(data.count); 
                } else {
                    alert(data.message);
                }
            })
            .catch(() => alert('Ошибка добавления'));
    });
});
