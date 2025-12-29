// Cart Logic
let cart = JSON.parse(localStorage.getItem('jj_cart')) || [];

document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    updateWishlistUI();

    // If on checkout page, render cart
    if (window.location.pathname.includes('checkout.php')) {
        renderCheckout();
    }

    // If on wishlist page, render items
    if (window.location.pathname.includes('wishlist.php')) {
        renderWishlist();
    }
});

function addToCart(id, name, price, image, shipping_fee = 0) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ id, name, price, image, shipping_fee, quantity: 1 });
    }
    saveCart();
    showToast(name + ' added to cart!', 'success');
}

function saveCart() {
    localStorage.setItem('jj_cart', JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    const countEl = document.getElementById('cart-count');
    if (countEl) countEl.innerText = count;
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    saveCart();
    renderCheckout();
}

function updateQuantity(id, change) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            saveCart();
            renderCheckout();
        }
    }
}

function renderCheckout() {
    const container = document.getElementById('cart-items-display');
    const summaryDiv = document.getElementById('cart-summary');
    const emptyCart = document.getElementById('empty-cart');
    const checkoutContent = document.getElementById('checkout-content');

    if (!container) return;

    if (cart.length === 0) {
        if (checkoutContent) checkoutContent.style.display = 'none';
        if (emptyCart) emptyCart.style.display = 'block';
        return;
    }

    if (checkoutContent) checkoutContent.style.display = 'block';
    if (emptyCart) emptyCart.style.display = 'none';

    let html = '';
    let subtotal = 0;

    cart.forEach((item) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        html += `
            <div class="cart-item">
                <img src="${item.image || 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCI+PHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjZTJlOGYwIi8+PC9zdmc+'}" class="cart-thumb" alt="${item.name}">
                <div class="cart-details">
                    <div class="cart-title">${item.name}</div>
                    <div class="cart-controls">
                        <button class="stepper-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button class="stepper-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                        <button class="remove-btn" onclick="removeFromCart(${item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="cart-price">${CURRENCY_SYMBOL}${itemTotal.toFixed(2)}</div>
            </div>
        `;
    });

    container.innerHTML = html;

    // Render summary
    let totalShipping = 0;
    cart.forEach(item => {
        totalShipping += (item.shipping_fee || 0) * item.quantity;
    });

    const total = subtotal + totalShipping;

    if (summaryDiv) {
        summaryDiv.innerHTML = `
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <span style="color: var(--text-secondary);">Subtotal</span>
                <span style="font-weight: 600;">${CURRENCY_SYMBOL}${subtotal.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <span style="color: var(--text-secondary);">Shipping</span>
                <span style="font-weight: 600;">${CURRENCY_SYMBOL}${totalShipping.toFixed(2)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 2px solid var(--border-color);">
                <span style="font-size: 1.2rem; font-weight: 700;">Total</span>
                <span style="font-size: 1.2rem; font-weight: 700; color: var(--accent-color);">${CURRENCY_SYMBOL}${total.toFixed(2)}</span>
            </div>
        `;
    }
}

// Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.querySelector('.mobile-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    } else {
        sidebar.classList.add('open');
        overlay.classList.add('active');
    }
}

// Auto-Sliding Carousel
document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('.hero-carousel');
    if (carousel) {
        setInterval(() => {
            const slideWidth = carousel.offsetWidth;
            const scrollPos = carousel.scrollLeft;
            const maxScroll = carousel.scrollWidth - slideWidth;

            let nextPos = scrollPos + slideWidth;
            if (nextPos > maxScroll) {
                nextPos = 0; // Loop back to start
            }

            carousel.scrollTo({
                left: nextPos,
                behavior: 'smooth'
            });
        }, 4000); // 4 seconds
    }
});

// Checkout Step Logic
function showDeliveryStep() {
    document.body.classList.add('step-delivery-active');
    window.scrollTo(0, 0);
}

function showCartStep() {
    document.body.classList.remove('step-delivery-active');
    window.scrollTo(0, 0);
}

// Admin Sidebar Toggle
function toggleAdminSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.querySelector('.admin-sidebar-overlay');

    if (sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    } else {
        sidebar.classList.add('open');
        overlay.classList.add('active');
    }
}

// Search Autocomplete with Caching
(function () {
    const searchInput = document.getElementById('search-input');
    const suggestionsBox = document.getElementById('search-suggestions');

    if (!searchInput || !suggestionsBox) return;

    // Cache management
    const CACHE_KEY = 'jj_search_cache';
    const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

    function getCache() {
        try {
            const cached = localStorage.getItem(CACHE_KEY);
            return cached ? JSON.parse(cached) : {};
        } catch {
            return {};
        }
    }

    function setCache(query, data) {
        try {
            const cache = getCache();
            cache[query.toLowerCase()] = {
                data: data,
                timestamp: Date.now()
            };
            localStorage.setItem(CACHE_KEY, JSON.stringify(cache));
        } catch { }
    }

    function getCachedResult(query) {
        const cache = getCache();
        const cached = cache[query.toLowerCase()];
        if (cached && (Date.now() - cached.timestamp) < CACHE_DURATION) {
            return cached.data;
        }
        return null;
    }

    let debounceTimer;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsBox.classList.remove('active');
            return;
        }

        debounceTimer = setTimeout(() => {
            // Check cache first
            const cached = getCachedResult(query);
            if (cached) {
                renderSuggestions(cached.suggestions);
                return;
            }

            // Fetch from server
            fetch(`api/search_suggestions.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    setCache(query, data);
                    renderSuggestions(data.suggestions);
                })
                .catch(() => {
                    suggestionsBox.innerHTML = '<div class="no-suggestions">Search unavailable</div>';
                    suggestionsBox.classList.add('active');
                });
        }, 300);
    });

    function renderSuggestions(suggestions) {
        if (!suggestions || suggestions.length === 0) {
            suggestionsBox.innerHTML = '<div class="no-suggestions">No results found</div>';
            suggestionsBox.classList.add('active');
            return;
        }

        let html = '';
        suggestions.forEach(item => {
            const url = item.type === 'category'
                ? `index.php?category=${item.id}`
                : `product.php?id=${item.id}`;

            let iconHtml = '';
            if (item.type === 'category') {
                iconHtml = `<div class="suggestion-icon"><i class="fas fa-folder"></i></div>`;
            } else {
                // Product - show image or placeholder
                const imgSrc = item.image ? item.image : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDQwIDQwIj48cmVjdCB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIGZpbGw9IiNlMmU4ZjAiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzY0NzQ4YiIgZm9udC1zaXplPSIxMCI+PGkgY2xhc3M9ImZhcyBmYS1ib3giPjwvaT48L3RleHQ+PC9zdmc+';
                iconHtml = `<img src="${imgSrc}" class="suggestion-image" alt="${escapeHtml(item.name)}">`;
            }

            html += `
                <a href="${url}" class="suggestion-item">
                    ${iconHtml}
                    <div class="suggestion-text">
                        <div class="suggestion-name">${escapeHtml(item.name)}</div>
                        <div class="suggestion-type">${item.type}</div>
                    </div>
                </a>
            `;
        });

        suggestionsBox.innerHTML = html;
        suggestionsBox.classList.add('active');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Close suggestions when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.remove('active');
        }
    });

    // Show suggestions on focus if there's a value
    searchInput.addEventListener('focus', function () {
        if (this.value.trim().length >= 2) {
            const cached = getCachedResult(this.value.trim());
            if (cached) {
                renderSuggestions(cached.suggestions);
            }
        }
    });
})();

// Wishlist Logic with localStorage
let wishlist = JSON.parse(localStorage.getItem('jj_wishlist')) || [];

document.addEventListener('DOMContentLoaded', () => {
    updateWishlistUI();

    // If on wishlist page, render items
    if (window.location.pathname.includes('wishlist.php')) {
        renderWishlist();
    }
});

function addToWishlist(id, name, price, image, shipping_fee = 0) {
    // Check if already in wishlist
    const existing = wishlist.find(item => item.id === id);
    if (existing) {
        // Remove if already in wishlist (toggle)
        removeFromWishlist(id);
        return;
    }

    wishlist.push({ id, name, price, image, shipping_fee });
    saveWishlist();

    // Show toast notification
    showToast(name + ' added to wishlist!', 'success');
}

function removeFromWishlist(id) {
    wishlist = wishlist.filter(item => item.id !== id);
    saveWishlist();

    // Re-render if on wishlist page
    if (window.location.pathname.includes('wishlist.php')) {
        renderWishlist();
    }

    showToast('Removed from wishlist', 'info');
}

function saveWishlist() {
    localStorage.setItem('jj_wishlist', JSON.stringify(wishlist));
    updateWishlistUI();
}

function updateWishlistUI() {
    // Update wishlist icon/button states across the site
    wishlist.forEach(item => {
        const buttons = document.querySelectorAll(`[data-wishlist-id="${item.id}"]`);
        buttons.forEach(btn => {
            btn.classList.add('in-wishlist');
            btn.innerHTML = '<i class="fas fa-heart"></i>';
        });
    });
}

function isInWishlist(id) {
    return wishlist.some(item => item.id === id);
}

function renderWishlist() {
    const container = document.getElementById('wishlist-display');
    const emptyState = document.getElementById('empty-wishlist');

    if (!container) return;

    if (wishlist.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }

    container.style.display = 'grid';
    emptyState.style.display = 'none';

    let html = '';
    wishlist.forEach(item => {
        html += `
            <div class="product-card">
                <div class="product-image-container">
                    <img src="${item.image || 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjUwIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjI1MCIgZmlsbD0iI2UyZThmMCIvPjwvc3ZnPg=='}" alt="${escapeHtml(item.name)}">
                    <button class="wishlist-btn in-wishlist" onclick="removeFromWishlist(${item.id})" title="Remove from wishlist">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                <div class="product-info">
                    <div class="product-title">${escapeHtml(item.name)}</div>
                    <div class="product-price">${CURRENCY_SYMBOL}${parseFloat(item.price).toFixed(2)}</div>
                    <button class="btn btn-block" onclick="addToCart(${item.id}, '${escapeHtml(item.name)}', ${item.price}, '${item.image}', ${item.shipping_fee || 0})" style="margin-top: 8px; font-size: 0.85rem; padding: 8px;">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Toast notification helper
function showToast(message, type = 'info') {
    // Remove any existing toast
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Ensure showDeliveryStep and showCartStep are defined
if (typeof showDeliveryStep === 'undefined') {
    function showDeliveryStep() {
        const step1 = document.getElementById('checkout-step-1');
        const step2 = document.getElementById('delivery-details-container');
        if (step1 && step2) {
            step1.style.display = 'none';
            step2.style.display = 'block';
            window.scrollTo(0, 0);
        }
    }
}

if (typeof showCartStep === 'undefined') {
    function showCartStep() {
        const step1 = document.getElementById('checkout-step-1');
        const step2 = document.getElementById('delivery-details-container');
        if (step1 && step2) {
            step1.style.display = 'block';
            step2.style.display = 'none';
            window.scrollTo(0, 0);
        }
    }
}
