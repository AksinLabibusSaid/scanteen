// Customer: home (menu + cart API), keranjang/checkout helpers, countdown.

const apiRoot = document.body.getAttribute("data-api-root") || "";

/**
 * Dialog error / info mengikuti tema maroon Scanteen (tanpa alert bawaan browser).
 * @param {{ title?: string, message?: string, detail?: string }} opts
 */
window.ScanteenUi = window.ScanteenUi || {};
window.ScanteenUi.showError = function (opts) {
  const title = (opts && opts.title) || "Terjadi kesalahan";
  const message = (opts && opts.message) || "";
  const detail = (opts && opts.detail) || "";

  const existing = document.querySelector(".scanteen-modal-backdrop");
  if (existing) existing.remove();

  const backdrop = document.createElement("div");
  backdrop.className = "scanteen-modal-backdrop";
  backdrop.setAttribute("role", "alertdialog");
  backdrop.setAttribute("aria-modal", "true");
  backdrop.setAttribute("aria-labelledby", "scanteen-modal-title");

  const panel = document.createElement("div");
  panel.className = "scanteen-modal";
  panel.addEventListener("click", function (e) {
    e.stopPropagation();
  });

  const accent = document.createElement("div");
  accent.className = "scanteen-modal__accent";

  const h = document.createElement("h3");
  h.id = "scanteen-modal-title";
  h.className = "scanteen-modal__title";
  h.textContent = title;

  const p = document.createElement("p");
  p.className = "scanteen-modal__message";
  p.textContent = message || "Silakan coba lagi. Jika masalah berlanjut, hubungi petugas kantin.";

  panel.appendChild(accent);
  panel.appendChild(h);
  panel.appendChild(p);

  if (detail) {
    const pre = document.createElement("pre");
    pre.className = "scanteen-modal__detail";
    pre.textContent = detail;
    panel.appendChild(pre);
  }

  const btn = document.createElement("button");
  btn.type = "button";
  btn.className = "scanteen-modal__btn";
  btn.textContent = "Mengerti";
  btn.addEventListener("click", close);
  panel.appendChild(btn);

  function close() {
    backdrop.remove();
    document.removeEventListener("keydown", onKey);
  }

  function onKey(e) {
    if (e.key === "Escape") close();
  }
  document.addEventListener("keydown", onKey);

  backdrop.addEventListener("click", function (e) {
    if (e.target === backdrop) close();
  });

  backdrop.appendChild(panel);
  document.body.appendChild(backdrop);
  btn.focus();
};

// ===== HOME (menu) =====
let menuItems = [];
let warungTabs = [];
let categoryTabs = [];
let activeWarung = "Semua";
let activeCategory = "Semua";
/** Ringkasan cart dari server */
let cartSummary = { itemCount: 0, subtotal: 0 };

function formatRupiah(amount) {
  return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(amount);
}

async function fetchJson(url, options) {
  const res = await fetch(url, { credentials: "same-origin", ...options });
  return res.json();
}

async function refreshCart() {
  if (!apiRoot) return;
  const data = await fetchJson(apiRoot + "/cart.php", { method: "GET" });
  if (!data.ok) return;
  cartSummary = {
    itemCount: data.cart?.itemCount ?? 0,
    subtotal: data.cart?.subtotal ?? 0,
  };
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

  const filtered = menuItems.filter((item) => {
    const okWarung = activeWarung === "Semua" || item.warung === activeWarung;
    const okCat = activeCategory === "Semua" || item.category === activeCategory;
    return okWarung && okCat;
  });

  filtered.forEach((item) => {
    const card = document.createElement("div");
    card.className = "rounded-[20px] bg-white shadow-[3px_3px_15px_0_rgba(0,0,0,0.15)] overflow-hidden";

    const isDisabled = (item.is_available === 0) || ((item.stock ?? 0) <= 0);
    card.innerHTML = `
      <div class="relative">
          <img src="${item.image}" alt="${item.name}" class="w-full h-[149px] object-cover ${isDisabled ? 'opacity-40 grayscale' : ''}">
          ${isDisabled ? `
          <div class="absolute inset-0 flex items-center justify-center">
              <span class="bg-black/60 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Habis</span>
          </div>
          ` : ''}
      </div>
      <div class="p-0 pt-2 pb-4 px-3 text-center">
          <p class="text-base font-bold text-black font-plus-jakarta leading-tight">${item.name}</p>
          <p class="text-xs text-[#919191] mt-0.5">${item.warung}</p>
          <p class="text-sm font-semibold text-maroon font-plus-jakarta mt-1">${formatRupiah(item.price)}</p>
          <button type="button" class="mt-2 w-[132px] h-[26px] rounded-[20px] text-[10px] font-semibold font-plus-jakarta transition-all menu-add-btn ${isDisabled ? 'opacity-50 cursor-not-allowed' : ''}"
              style="background-color: ${isDisabled ? '#9CA3AF' : '#8B2424'}; color: white; border: 1px solid transparent;"
              data-menu-id="${item.id}"
              ${isDisabled ? 'disabled' : ''}>
              ${isDisabled ? 'Habis' : 'Tambah'}
          </button>
      </div>
    `;

    grid.appendChild(card);
  });

  grid.querySelectorAll(".menu-add-btn").forEach((btn) => {
    btn.addEventListener("click", () => handleAddClick(btn, parseInt(btn.getAttribute("data-menu-id") || "0", 10)));
  });
}

async function handleAddClick(button, itemId) {
  if (!apiRoot || !itemId) return;
  flashButton(button);
  try {
    const r = await fetchJson(apiRoot + "/cart.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "add", menu_id: itemId, qty: 1, note: "" }),
    });
    if (!r.ok) {
      alert(r.error || "Gagal menambah ke keranjang");
      return;
    }
    await refreshCart();
    updateCart();
  } catch (e) {
    console.error(e);
    alert("Gagal menambah ke keranjang");
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

  const cartCount = cartSummary.itemCount;
  const cartTotal = cartSummary.subtotal;

  if (cartCount === 0) cartBar.classList.add("hidden");
  else {
    cartBar.classList.remove("hidden");
    countBadge.textContent = String(cartCount);
    itemsLabel.textContent = `${cartCount} ITEMS`;
    totalDisplay.textContent = formatRupiah(cartTotal);
  }
}

async function bootHomeMenu() {
  const grid = document.getElementById("menuGrid");
  if (!grid || !apiRoot) return;

  try {
    const mData = await fetchJson(apiRoot + "/menus.php", { method: "GET" });
    if (!mData.ok) {
      grid.innerHTML = `<p class="col-span-2 text-center text-sm text-gray-500 px-4">${mData.error || "Gagal memuat menu."}</p>`;
      return;
    }
    menuItems = mData.menus || [];
    warungTabs = mData.warung_tabs || ["Semua"];
    categoryTabs = mData.category_tabs || ["Semua"];
    await refreshCart();
    initTabs();
    renderMenuItems();
    updateCart();
  } catch (e) {
    console.error(e);
    grid.innerHTML = `<p class="col-span-2 text-center text-sm text-red-600 px-4">Tidak dapat memuat menu. Periksa koneksi.</p>`;
  }
}

// ===== KERANJANG (legacy steppers — halaman keranjang utama memakai PHP) =====
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

function checkout() {
  const isHome = !!document.getElementById("menuGrid");
  if (isHome) {
    window.location.href = "./index.php?page=keranjang";
    return;
  }
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

/**
 * Tampilan pelanggan diperuntukkan ponsel / tablet. Desktop / laptop (lebar >= 1024px,
 * pointer presisi, hover) diblokir; jendela diperkecil atau DevTools device mode tetap bisa uji.
 */
function enforceCustomerDeviceGate() {
  const gate = document.getElementById("customer-device-gate");
  const shell = document.getElementById("customer-app-shell");
  if (!gate || !shell) return;

  const w = window.innerWidth;
  const fine = window.matchMedia("(pointer: fine)").matches;
  const hover = window.matchMedia("(hover: hover)").matches;
  const coarse = window.matchMedia("(pointer: coarse)").matches;

  const block = w >= 1024 && fine && hover && !coarse;

  if (block) {
    gate.removeAttribute("hidden");
    shell.setAttribute("inert", "");
    shell.setAttribute("aria-hidden", "true");
    document.body.classList.add("customer-device-gate--open");
  } else {
    gate.setAttribute("hidden", "");
    shell.removeAttribute("inert");
    shell.removeAttribute("aria-hidden");
    document.body.classList.remove("customer-device-gate--open");
  }
}

let customerDeviceGateResizeTimer = 0;
function scheduleCustomerDeviceGateRefresh() {
  window.clearTimeout(customerDeviceGateResizeTimer);
  customerDeviceGateResizeTimer = window.setTimeout(() => {
    enforceCustomerDeviceGate();
  }, 120);
}

document.addEventListener("DOMContentLoaded", () => {
  enforceCustomerDeviceGate();
  window.addEventListener("resize", scheduleCustomerDeviceGateRefresh);
  window.addEventListener("orientationchange", scheduleCustomerDeviceGateRefresh);

  if (document.getElementById("menuGrid")) {
    bootHomeMenu();

    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
      searchInput.addEventListener("input", (e) => {
        const q = (e.target.value || "").toLowerCase();
        const grid = document.getElementById("menuGrid");
        if (!grid) return;
        grid.querySelectorAll(":scope > div").forEach((card) => {
          const text = card.textContent?.toLowerCase() || "";
          card.style.display = text.includes(q) ? "" : "none";
        });
      });
    }
  }

  const dineInBtn = document.getElementById("dineInBtn");
  const takeAwayBtn = document.getElementById("takeAwayBtn");
  if (dineInBtn && takeAwayBtn && !document.getElementById("dining_type")) {
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

  if (document.querySelector(".warung-card")) updateTotal();

  const timerEl = document.getElementById("timer");
  if (timerEl) startCountdownFromElement(timerEl);

  const qrisCd = document.getElementById("countdown-qris");
  if (qrisCd && qrisCd.hasAttribute("data-countdown-seconds")) {
    startCountdownFromElement(qrisCd);
  }
  const midtransCd = document.getElementById("countdown-midtrans");
  if (midtransCd && midtransCd.hasAttribute("data-countdown-seconds")) {
    startCountdownFromElement(midtransCd);
  }
});
