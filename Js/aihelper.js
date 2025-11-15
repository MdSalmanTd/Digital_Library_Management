const sendButtons = document.querySelectorAll('.sendicon');
const aiCards = document.querySelectorAll('.aicard');
const aiOverview = document.querySelector('.aioverview');

// When clicking send icon → show ai overview
sendButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        aiOverview.style.display = 'block';
    });
});

// When clicking any AI card → show ai overview
aiCards.forEach(card => {
    card.addEventListener('click', () => {
        aiOverview.style.display = 'block';
    });
});
