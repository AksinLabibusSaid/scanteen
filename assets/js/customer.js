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
    card.className = "rounded-[20px] bg-white shadow-[3px_3px_15px_0_rgba(0,0,0,0.15)] overflow-hidden cursor-pointer";

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
              ${isDisabled ? 'disabled' : ''}>
              ${isDisabled ? 'Habis' : 'Tambah'}
          </button>
      </div>
    `;

    const addBtn = card.querySelector(".menu-add-btn");
    if (!isDisabled && addBtn) {
      addBtn.addEventListener("click", (e) => {
        e.stopPropagation(); // Prevent opening modal
        handleAddClick(addBtn, item.id, 1, ""); // Add directly
      });
    }

    if (!isDisabled) {
      card.addEventListener("click", () => {
        showMenuDetailModal(item);
      });
    }

    grid.appendChild(card);
  });
}

function showMenuDetailModal(item) {
  const existing = document.querySelector(".menu-detail-backdrop");
  if (existing) existing.remove();

  const backdrop = document.createElement("div");
  backdrop.className = "menu-detail-backdrop fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4";
  backdrop.setAttribute("role", "dialog");
  backdrop.setAttribute("aria-modal", "true");

  const modal = document.createElement("div");
  modal.className = "w-full max-w-[360px] bg-white rounded-3xl overflow-hidden shadow-2xl relative";

  modal.innerHTML = `
    <!-- Top Part: Image -->
    <div class="relative h-[240px]">
      <img src="${item.image}" alt="${item.name}" class="w-full h-full object-cover">
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
      <h2 class="absolute bottom-4 left-4 text-white text-2xl font-bold font-plus-jakarta">${item.name}</h2>
      <button id="closeModalBtn" class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center text-white transition-colors">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Bottom Part: Details -->
    <div class="p-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <p class="text-sm text-gray-500 font-medium">${item.warung}</p>
          <p class="text-xl font-bold text-maroon font-plus-jakarta mt-1">${formatRupiah(item.price)}</p>
        </div>
        <div class="text-right">
          <p class="text-xs text-gray-500 font-medium">Tersedia</p>
          <p class="text-sm font-bold text-gray-700">Stok : ${item.stock ?? 0}</p>
        </div>
      </div>

      <div class="border-t border-gray-100 pt-4 mb-4">
        <label class="text-sm font-semibold text-gray-700 mb-2 block">Catatan Tambahan</label>
        <textarea id="modalNotes" placeholder="Tambah catatan..." class="w-full h-24 p-4 bg-[#FAF7F6] border-none rounded-2xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-maroon/30 resize-none"></textarea>
      </div>

      <div class="flex items-center justify-between gap-4">
        <!-- Quantity Selector -->
        <div class="flex items-center bg-[#FDE8E4] rounded-full px-3 py-2">
          <button id="modalDecQty" class="w-8 h-8 flex items-center justify-center text-maroon font-bold text-xl">-</button>
          <span id="modalQty" class="mx-4 font-bold text-gray-700">1</span>
          <button id="modalIncQty" class="w-8 h-8 flex items-center justify-center text-maroon font-bold text-xl">+</button>
        </div>

        <!-- Add Button -->
        <button id="modalAddBtn" class="flex-1 h-12 bg-maroon text-white rounded-full font-bold flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          Tambah
        </button>
      </div>
    </div>
  `;

  backdrop.appendChild(modal);
  document.body.appendChild(backdrop);

  const closeBtn = modal.querySelector("#closeModalBtn");
  const decBtn = modal.querySelector("#modalDecQty");
  const incBtn = modal.querySelector("#modalIncQty");
  const qtySpan = modal.querySelector("#modalQty");
  const addBtn = modal.querySelector("#modalAddBtn");
  const notesArea = modal.querySelector("#modalNotes");

  let qty = 1;
  const maxStock = item.stock ?? 0;

  closeBtn.addEventListener("click", () => backdrop.remove());
  backdrop.addEventListener("click", (e) => { if (e.target === backdrop) backdrop.remove(); });

  decBtn.addEventListener("click", () => {
    if (qty > 1) {
      qty--;
      qtySpan.textContent = qty;
    }
  });

  incBtn.addEventListener("click", () => {
    if (qty < maxStock) {
      qty++;
      qtySpan.textContent = qty;
    } else {
      alert("Stok tidak mencukupi");
    }
  });

  addBtn.addEventListener("click", async () => {
    const success = await handleAddClick(null, item.id, qty, notesArea.value);
    if (success) {
      backdrop.remove();
    }
  });
}

async function handleAddClick(button, itemId, qty = 1, note = "") {
  if (!apiRoot || !itemId) return false;
  if (button) flashButton(button);
  try {
    const r = await fetchJson(apiRoot + "/cart.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "add", menu_id: itemId, qty: qty, note: note }),
    });
    if (!r.ok) {
      alert(r.error || "Gagal menambah ke keranjang");
      return false;
    }
    await refreshCart();
    updateCart();
    return true;
  } catch (e) {
    console.error(e);
    alert("Gagal menambah ke keranjang");
    return false;
  } finally {
    if (button) {
      setTimeout(() => {
        button.style.backgroundColor = "#8B2424";
        button.style.color = "white";
        button.style.border = "1px solid transparent";
      }, 180);
    }
  }
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
