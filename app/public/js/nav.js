const burger = document.getElementById('burger');
const navLinks = document.getElementById('nav-links');
if (burger) {
    burger.addEventListener('click', () => {
        navLinks.classList.toggle('open');
        burger.classList.toggle('open');
        const expanded = burger.getAttribute('aria-expanded') === 'true' || false;
        burger.setAttribute('aria-expanded', !expanded);
    });
}

// Carrousel Derniers Articles (optimisé)
window.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('carousel-track');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    const pagination = document.getElementById('carousel-pagination');
    if (!track || !prevBtn || !nextBtn || !pagination) return;
    const items = Array.from(track.querySelectorAll('.carousel-item'));
    let current = 0;
    let visibleCount = 1;
    function updateVisibleCount() {
        if (window.innerWidth >= 900) visibleCount = 4;
        else if (window.innerWidth >= 600) visibleCount = 2;
        else visibleCount = 1;
    }
    function updateCarousel() {
        updateVisibleCount();
        const itemWidth = items[0].offsetWidth + 20;
        track.style.transform = `translateX(-${current * itemWidth}px)`;
        // Flèches
        prevBtn.disabled = current === 0;
        nextBtn.disabled = current >= items.length - visibleCount;
        // Pagination
        pagination.innerHTML = '';
        const pageCount = Math.max(1, items.length - visibleCount + 1);
        for (let i = 0; i < pageCount; i++) {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot' + (i === current ? ' active' : '');
            dot.setAttribute('aria-label', 'Aller à la page ' + (i + 1));
            dot.addEventListener('click', () => {
                current = i;
                updateCarousel();
            });
            pagination.appendChild(dot);
        }
    }
    prevBtn.addEventListener('click', function() {
        if (current > 0) current--;
        updateCarousel();
    });
    nextBtn.addEventListener('click', function() {
        if (current < items.length - visibleCount) current++;
        updateCarousel();
    });
    window.addEventListener('resize', function() {
        // recalcule la page courante pour éviter le vide
        updateVisibleCount();
        if (current > items.length - visibleCount) current = Math.max(0, items.length - visibleCount);
        updateCarousel();
    });
    updateCarousel();
}); 