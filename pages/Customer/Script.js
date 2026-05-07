// Menu Data
const menuItems = [
    {
        id: 1,
        name: "Wader Goreng",
        price: 25000,
        warung: "Warung 1",
        image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380"
    },
    {
        id: 2,
        name: "Soto Babat",
        price: 25000,
        warung: "Warung 1",
        image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380"
    },
    {
        id: 3,
        name: "Mie Instan",
        price: 25000,
        warung: "Warung 1",
        image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380"
    },
    {
        id: 4,
        name: "Rawon Jumbo",
        price: 25000,
        warung: "Warung 2",
        image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380"
    },
    {
        id: 5,
        name: "Bubur Ayam",
        price: 25000,
        warung: "Warung 2",
        image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380"
    },
    {
        id: 6,
        name: "Nasi Kuning Tel..",
        price: 25000,
        warung: "Warung 2",
        image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380"
    }
];

const warungTabs = ["Semua", "Warung 1", "Warung 2", "Warung 3 "];
const categoryTabs = ["Semua", "Makanan", "Minuman", "Jajanan"];

// State
let activeWarung = "Semua";
let activeCategory = "Semua";
let cart = new Set([1, 3, 4]); // Default items in cart

// Utility function to format Rupiah
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Initialize tabs
function initTabs() {
    // Warung tabs
    const warungTabsContainer = document.getElementById('warungTabs');
    warungTabs.forEach(tab => {
        const btn = document.createElement('button');
        btn.className = 'flex-shrink-0 px-4 py-2 rounded-xl text-base transition-all';
        btn.textContent = tab;
        btn.onclick = () => setActiveWarung(tab);
        
        if (tab === activeWarung) {
            btn.className += ' bg-maroon text-white shadow-md';
        } else {
            btn.className += ' border border-gray-200 bg-white text-[#4B5563]';
        }
        
        warungTabsContainer.appendChild(btn);
    });

    // Category tabs
    const categoryTabsContainer = document.getElementById('categoryTabs');
    categoryTabs.forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'pb-3 text-base whitespace-nowrap flex-shrink-0 border-b-2 transition-all';
        btn.textContent = cat;
        btn.onclick = () => setActiveCategory(cat);
        
        if (cat === activeCategory) {
            btn.className += ' border-maroon text-maroon font-bold';
        } else {
            btn.className += ' border-transparent text-gray-400 font-medium';
        }
        
        categoryTabsContainer.appendChild(btn);
    });
}

// Set active warung
function setActiveWarung(warung) {
    activeWarung = warung;
    updateWarungTabs();
    renderMenuItems();
}

// Set active category
function setActiveCategory(category) {
    activeCategory = category;
    updateCategoryTabs();
    renderMenuItems();
}

// Update warung tab UI
function updateWarungTabs() {
    const buttons = document.querySelectorAll('#warungTabs button');
    buttons.forEach((btn, idx) => {
        if (warungTabs[idx] === activeWarung) {
            btn.className = 'flex-shrink-0 px-4 py-2 rounded-xl text-base transition-all bg-maroon text-white shadow-md';
        } else {
            btn.className = 'flex-shrink-0 px-4 py-2 rounded-xl text-base transition-all border border-gray-200 bg-white text-[#4B5563]';
        }
    });
}

// Update category tab UI
function updateCategoryTabs() {
    const buttons = document.querySelectorAll('#categoryTabs button');
    buttons.forEach((btn, idx) => {
        if (categoryTabs[idx] === activeCategory) {
            btn.className = 'pb-3 text-base whitespace-nowrap flex-shrink-0 border-b-2 transition-all border-maroon text-maroon font-bold';
        } else {
            btn.className = 'pb-3 text-base whitespace-nowrap flex-shrink-0 border-b-2 transition-all border-transparent text-gray-400 font-medium';
        }
    });
}

// Render menu items
function renderMenuItems() {
    const grid = document.getElementById('menuGrid');
    grid.innerHTML = '';

    menuItems.forEach(item => {
        const isAdded = cart.has(item.id);
        const card = document.createElement('div');
        card.className = 'rounded-[20px] bg-white shadow-[3px_3px_15px_0_rgba(0,0,0,0.15)] overflow-hidden';
        
        card.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="w-full h-[149px] object-cover">
            <div class="p-0 pt-2 pb-4 px-3 text-center">
                <p class="text-base font-bold text-black font-plus-jakarta leading-tight">${item.name}</p>
                <p class="text-xs text-[#919191] mt-0.5">${item.warung}</p>
                <p class="text-sm font-semibold text-maroon font-plus-jakarta mt-1">${formatRupiah(item.price)}</p>
                <button class="mt-2 w-[132px] h-[26px] rounded-[20px] text-[10px] font-semibold font-plus-jakarta transition-all"
                    style="background-color: #8B2424; color: white; border: 1px solid transparent;"
                    onclick="handleAddClick(this, ${item.id})">
                    Tambah
                </button>
            </div>
        `;
        
        grid.appendChild(card);
    });
}

// Add item to cart with button animation
function handleAddClick(button, itemId) {
    flashButton(button);

    if (!cart.has(itemId)) {
        cart.add(itemId);
        updateCart();
    }

    setTimeout(() => {
        button.style.backgroundColor = '#8B2424';
        button.style.color = 'white';
        button.style.border = '1px solid transparent';
    }, 180);
}

function flashButton(button) {
    button.style.backgroundColor = 'white';
    button.style.color = '#8B2424';
    button.style.border = '1px solid #8B2424';
}

// Update cart display
function updateCart() {
    const cartCount = cart.size;
    const cartTotal = Array.from(cart).reduce((sum, id) => {
        const item = menuItems.find(m => m.id === id);
        return sum + (item ? item.price : 0);
    }, 0);

    const cartBar = document.getElementById('cartBar');
    const countBadge = document.getElementById('cartCount');
    const itemsLabel = document.getElementById('cartItemsLabel');
    const totalDisplay = document.getElementById('cartTotal');

    if (cartCount === 0) {
        cartBar.classList.add('hidden');
    } else {
        cartBar.classList.remove('hidden');
        countBadge.textContent = cartCount;
        itemsLabel.textContent = `${cartCount} ITEMS`;
        totalDisplay.textContent = formatRupiah(cartTotal);
    }
}

// Checkout
function checkout() {
    alert(`Checkout: ${cart.size} items, Total: ${formatRupiah(
        Array.from(cart).reduce((sum, id) => {
            const item = menuItems.find(m => m.id === id);
            return sum + (item ? item.price : 0);
        }, 0)
    )}`);
}

// Search functionality
document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    renderMenuItems();
    updateCart();

    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', (e) => {
        // Could implement search filtering here
        console.log('Search:', e.target.value);
    });
});
