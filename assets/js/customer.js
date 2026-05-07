// Customer pages combined script (home + keranjang).

// ===== HOME (menu) =====
const menuItems = [
  { id: 1, name: "Wader Goreng", price: 25000, warung: "Warung 1", image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380" },
  { id: 2, name: "Soto Babat", price: 25000, warung: "Warung 1", image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380" },
  { id: 3, name: "Mie Instan", price: 25000, warung: "Warung 1", image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380" },
  { id: 4, name: "Rawon Jumbo", price: 25000, warung: "Warung 2", image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380" },
  { id: 5, name: "Bubur Ayam", price: 25000, warung: "Warung 2", image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380" },
  { id: 6, name: "Nasi Kuning Tel..", price: 25000, warung: "Warung 2", image: "https://api.builder.io/api/v1/image/assets/TEMP/0047a570799b8fcb285774402e9e0c783c5d4046?width=380" },
];

const warungTabs = ["Semua", "Warung 1", "Warung 2", "Warung 3 "];
const categoryTabs = ["Semua", "Makanan", "Minuman", "Jajanan"];

let activeWarung = "Semua";
let activeCategory = "Semua";
let cart = new Set([1, 3, 4]);

function formatRupiah(amount) {
  return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(amount);
}

function initTabs() {
  const warungTabsContainer = document.getElementById("warungTabs");
  if (!warungTabsContainer) return;

  warungTabsContainer.innerHTML = "";
  warungTabs.forEach((tab) => {
    const btn = document.createElement("button");
    btn.className = "flex-shrink-0 px-4 py-2 rounded-xl text-base transition-all";
    btn.textContent = tab;
    btn.onclick = () => setActiveWarung(tab);

    if (tab === activeWarung) btn.className += " bg-maroon text-white shadow-md";
    else btn.className += " border border-gray-200 bg-white text-[#4B5563]";

    warungTabsContainer.appendChild(btn);
  });

  const categoryTabsContainer = document.getElementById("categoryTabs");
  if (!categoryTabsContainer) return;

  categoryTabsContainer.innerHTML = "";
  categoryTabs.forEach((cat) => {
    const btn = document.createElement("button");
    btn.className = "pb-3 text-base whitespace-nowrap flex-shrink-0 border-b-2 transition-all";
    btn.textContent = cat;
    btn.onclick = () => setActiveCategory(cat);

    if (cat === activeCategory) btn.className += " border-maroon text-maroon font-bold";
    else btn.className += " border-transparent text-gray-400 font-medium";

    categoryTabsContainer.appendChild(btn);
  });
}

function setActiveWarung(warung) {
  activeWarung = warung;
  initTabs();
  renderMenuItems();
}

function setActiveCategory(category) {
  activeCategory = category;
  initTabs();
  renderMenuItems();
}

function renderMenuItems() {
  const grid = document.getElementById("menuGrid");
  if (!grid) return;
  grid.innerHTML = "";

  menuItems.forEach((item) => {
    const card = document.createElement("div");
    card.className = "rounded-[20px] bg-white shadow-[3px_3px_15px_0_rgba(0,0,0,0.15)] overflow-hidden";

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

function handleAddClick(button, itemId) {
  flashButton(button);
  if (!cart.has(itemId)) {
    cart.add(itemId);
    updateCart();
  }

  setTimeout(() => {
    button.style.backgroundColor = "#8B2424";
    button.style.color = "white";
    button.style.border = "1px solid transparent";
  }, 180);
}

function flashButton(button) {
  button.style.backgroundColor = "white";
  button.style.color = "#8B2424";
  button.style.border = "1px solid #8B2424";
}

function updateCart() {
  const cartBar = document.getElementById("cartBar");
  const countBadge = document.getElementById("cartCount");
  const itemsLabel = document.getElementById("cartItemsLabel");
  const totalDisplay = document.getElementById("cartTotal");
  if (!cartBar || !countBadge || !itemsLabel || !totalDisplay) return;

  const cartCount = cart.size;
  const cartTotal = Array.from(cart).reduce((sum, id) => {
    const item = menuItems.find((m) => m.id === id);
    return sum + (item ? item.price : 0);
  }, 0);

  if (cartCount === 0) cartBar.classList.add("hidden");
  else {
    cartBar.classList.remove("hidden");
    countBadge.textContent = cartCount;
    itemsLabel.textContent = `${cartCount} ITEMS`;
    totalDisplay.textContent = formatRupiah(cartTotal);
  }
}

// ===== KERANJANG (cart page) =====
function incrementQuantity(btn) {
  const stepper = btn.closest(".stepper");
  const valueElement = stepper?.querySelector(".stepper-value");
  if (!valueElement) return;
  const currentValue = parseInt(valueElement.textContent);
  valueElement.textContent = currentValue + 1;
  updateTotal();
}

function decrementQuantity(btn) {
  const stepper = btn.closest(".stepper");
  const valueElement = stepper?.querySelector(".stepper-value");
  if (!valueElement) return;
  const currentValue = parseInt(valueElement.textContent);
  if (currentValue > 1) {
    valueElement.textContent = currentValue - 1;
    updateTotal();
  }
}

function deleteItem(btn) {
  const item = btn.closest(".cart-item");
  const warungCard = btn.closest(".warung-card");
  const warungContent = warungCard?.querySelector(".warung-content");
  if (!item || !warungCard || !warungContent) return;

  const nextElement = item.nextElementSibling;
  const prevElement = item.previousElementSibling;
  item.remove();

  if (nextElement && nextElement.classList.contains("item-divider")) nextElement.remove();
  else if (prevElement && prevElement.classList.contains("item-divider")) prevElement.remove();

  if (warungContent.querySelectorAll(".cart-item").length === 0) warungCard.remove();
  updateTotal();
}

function updateTotal() {
  const warungCards = document.querySelectorAll(".warung-card");
  if (warungCards.length === 0) return;

  let total = 0;
  warungCards.forEach((card) => {
    let warungTotal = 0;
    const items = card.querySelectorAll(".cart-item");
    const footerAmount = card.querySelector(".footer-amount");

    items.forEach((item) => {
      const quantity = parseInt(item.querySelector(".item-quantity")?.textContent || "0");
      const priceText = item.querySelector(".item-price")?.textContent || "0";
      const price = parseInt(priceText.replace(/[^0-9]/g, ""));
      warungTotal += price * quantity;
      total += price * quantity;
    });

    if (footerAmount) footerAmount.textContent = "Rp " + warungTotal.toLocaleString("id-ID");
  });

  const totalElement = document.querySelector(".summary-amount");
  if (totalElement) totalElement.textContent = "Rp " + total.toLocaleString("id-ID");
}

// ===== Shared checkout handler =====
function checkout() {
  // requirement: from home checkout -> go to keranjang page
  const isHome = !!document.getElementById("menuGrid");
  if (isHome) {
    window.location.href = "./index.php?page=keranjang";
    return;
  }

  // On keranjang page -> go to pesanan page
  window.location.href = "./index.php?page=pesanan";
}

function startCountdownFromElement(timerEl) {
  const rawSeconds = timerEl.getAttribute("data-countdown-seconds");
  const initialSeconds = rawSeconds ? parseInt(rawSeconds, 10) : NaN;
  if (!Number.isFinite(initialSeconds) || initialSeconds < 0) return;

  let seconds = initialSeconds;
  const tick = () => {
    const m = Math.floor(seconds / 60).toString().padStart(2, "0");
    const s = (seconds % 60).toString().padStart(2, "0");
    timerEl.textContent = `${m}:${s}`;
    if (seconds > 0) seconds -= 1;
  };

  tick();
  const interval = setInterval(() => {
    if (seconds <= 0) {
      clearInterval(interval);
      timerEl.textContent = "00:00";
      return;
    }
    tick();
  }, 1000);
}

document.addEventListener("DOMContentLoaded", () => {
  // init home if elements exist
  if (document.getElementById("menuGrid")) {
    initTabs();
    renderMenuItems();
    updateCart();

    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
      searchInput.addEventListener("input", (e) => {
        console.log("Search:", e.target.value);
      });
    }
  }

  // ===== KERANJANG UI toggles (dining + payment) =====
  const dineInBtn = document.getElementById("dineInBtn");
  const takeAwayBtn = document.getElementById("takeAwayBtn");
  if (dineInBtn && takeAwayBtn) {
    dineInBtn.addEventListener("click", () => {
      dineInBtn.classList.remove("bg-transparent", "text-[#5F5E5B]");
      dineInBtn.classList.add("bg-[#800000]", "text-white");
      takeAwayBtn.classList.remove("bg-[#800000]", "text-white");
      takeAwayBtn.classList.add("bg-transparent", "text-[#5F5E5B]");
    });

    takeAwayBtn.addEventListener("click", () => {
      takeAwayBtn.classList.remove("bg-transparent", "text-[#5F5E5B]");
      takeAwayBtn.classList.add("bg-[#800000]", "text-white");
      dineInBtn.classList.remove("bg-[#800000]", "text-white");
      dineInBtn.classList.add("bg-transparent", "text-[#5F5E5B]");
    });
  }

  const qrisBtn = document.getElementById("qrisBtn");
  const kasirBtn = document.getElementById("kasirBtn");
  const qrisRadio = document.getElementById("qrisRadio");
  const kasirRadio = document.getElementById("kasirRadio");
  if (qrisBtn && kasirBtn && qrisRadio && kasirRadio) {
    qrisBtn.addEventListener("click", () => {
      qrisBtn.classList.remove("opacity-70", "border-[#F5F5F4]");
      qrisBtn.classList.add("border-2", "border-[#800000]", "opacity-100");
      kasirBtn.classList.remove("border-2", "border-[#800000]");
      kasirBtn.classList.add("border-[#F5F5F4]", "opacity-70");

      qrisRadio.innerHTML = '<div class="w-3 h-3 rounded-full bg-[#800000]"></div>';
      kasirRadio.innerHTML = "";
    });

    kasirBtn.addEventListener("click", () => {
      kasirBtn.classList.remove("opacity-70", "border-[#F5F5F4]");
      kasirBtn.classList.add("border-2", "border-[#800000]", "opacity-100");
      qrisBtn.classList.remove("border-2", "border-[#800000]", "opacity-100");
      qrisBtn.classList.add("opacity-70", "border-[#F5F5F4]");

      kasirRadio.innerHTML = '<div class="w-3 h-3 rounded-full bg-[#800000]"></div>';
      qrisRadio.innerHTML = "";
    });
  }

  // init keranjang totals if on keranjang
  if (document.querySelector(".warung-card")) updateTotal();

  // init countdown if present (status / pembayaran)
  const timerEl = document.getElementById("timer");
  if (timerEl) startCountdownFromElement(timerEl);
});

