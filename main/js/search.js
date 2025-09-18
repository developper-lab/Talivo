document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.search input'); 
    const btnSearch = document.querySelector('.btn_search');
    const cards = document.querySelectorAll('.card'); 

    function filterCards() {
        const query = searchInput.value.toLowerCase();
        cards.forEach(card => {
            const title = card.querySelector('.title').textContent.toLowerCase();
            card.style.display = title.includes(query) ? 'flex' : 'none';
        });
    }

    btnSearch.addEventListener('click', filterCards);

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            filterCards();
        }
    });
});
