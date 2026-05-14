<?php

declare(strict_types=1);

use App\Support\Money;

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=keranjang');
    exit;
}

$sum = $customerCartSummary ?? ['groups' => [], 'subtotal' => 0.0, 'itemCount' => 0, 'warungCount' => 0];
$groups = $sum['groups'];
$totalLabel = Money::formatIdr((float) $sum['subtotal']);
$itemCount = (int) $sum['itemCount'];
?>

<main class="flex flex-col gap-6 px-4 pt-6 pb-44" id="keranjangPage" data-item-count="<?php echo (int) $itemCount; ?>">

  <?php if ($groups === []) { ?>
    <div class="flex flex-col items-center justify-center py-20 text-center gap-4">
      <p class="text-[#5F5E5B] text-base">Keranjang masih kosong.</p>
      <a href="./index.php?page=home" class="px-6 py-3 rounded-2xl bg-[#800000] text-white font-bold text-sm">Pilih menu</a>
    </div>
  <?php } else {
      foreach ($groups as $g) {
          ?>
    <div class="flex flex-col rounded-3xl bg-white shadow-[0_2px_8px_0_rgba(0,0,0,0.04)] overflow-hidden" data-warung-id="<?php echo (int) $g['warung_id']; ?>">
      <div class="flex items-center gap-2 px-6 py-3 bg-[#F4F3F1] border-b border-[#EFEEEB]">
        <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M14.2852 6.0375V12C14.2852 12.4125 14.1383 12.7656 13.8446 13.0594C13.5508 13.3531 13.1977 13.5 12.7852 13.5H2.2852C1.8727 13.5 1.51958 13.3531 1.22583 13.0594C0.932079 12.7656 0.785204 12.4125 0.785204 12V6.0375C0.497704 5.775 0.275829 5.4375 0.119579 5.025C-0.0366709 4.6125 -0.0397959 4.1625 0.110204 3.675L0.897704 1.125C0.997704 0.8 1.17583 0.53125 1.43208 0.31875C1.68833 0.10625 1.9852 0 2.3227 0H12.7477C13.0852 0 13.379 0.103125 13.629 0.309375C13.879 0.515625 14.0602 0.7875 14.1727 1.125L14.9602 3.675C15.1102 4.1625 15.1071 4.60625 14.9508 5.00625C14.7946 5.40625 14.5727 5.75 14.2852 6.0375Z" fill="#1A1C1A"/>
        </svg>
        <span class="text-[#1A1C1A] text-base font-medium leading-6 tracking-[1.6px] uppercase">
          <?php echo htmlspecialchars((string) $g['warung_name'], ENT_QUOTES, 'UTF-8'); ?>
        </span>
      </div>

      <div class="flex flex-col px-6 pt-6 gap-6">
        <?php
              $items = $g['items'];
          foreach ($items as $idx => $it) {
              $mid = (int) $it['menu_id'];
              $isLast = $idx === count($items) - 1;
              ?>
        <div class="flex flex-col gap-3" data-menu-id="<?php echo $mid; ?>">
          <div class="flex items-start justify-between">
            <div class="flex flex-col gap-1 flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <span class="text-[#800000] text-base font-normal leading-6 shrink-0"><?php echo (int) $it['qty']; ?>x</span>
                <span class="text-[#1A1C1A] text-base font-medium leading-6"><?php echo htmlspecialchars((string) $it['name'], ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <div class="inline-flex self-start px-2 py-[2px] rounded-md border border-[#F3F4F6] bg-[#F9FAFB]">
                <span class="text-[#6B7280] text-xs font-medium leading-4"><?php echo htmlspecialchars($it['note'] !== '' ? (string) $it['note'] : '-', ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <span class="text-[#800000] text-base font-semibold leading-6"><?php echo htmlspecialchars((string) $it['line_subtotal_label'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <button type="button" class="p-1 flex-shrink-0 ml-2 keranjang-remove" data-menu-id="<?php echo $mid; ?>" aria-label="Hapus item">
              <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M3 18C2.45 18 1.97917 17.8042 1.5875 17.4125C1.19583 17.0208 1 16.55 1 16V3H0V1H5V0H11V1H16V3H15V16C15 16.55 14.8042 17.0208 14.4125 17.4125C14.0208 17.8042 13.55 18 13 18H3ZM13 3H3V16H13V3ZM5 14H7V5H5V14ZM9 14H11V5H9V14ZM3 3V16V3Z" fill="#656461"/>
              </svg>
            </button>
          </div>
          <div class="flex justify-end">
            <div class="flex items-center gap-3 p-1 rounded-lg bg-[#EFEEEB]">
              <button type="button" class="w-8 h-8 flex items-center justify-center rounded-md border border-[#E2BFB9] bg-white keranjang-qty" data-menu-id="<?php echo $mid; ?>" data-delta="-1" aria-label="Kurangi">
                <svg width="11" height="2" viewBox="0 0 11 2" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 1.5V0H10.5V1.5H0Z" fill="#800000"/></svg>
              </button>
              <span class="keranjang-qty-value min-w-[20px] text-center text-[#1A1C1A] text-base font-normal leading-6 select-none"><?php echo (int) $it['qty']; ?></span>
              <button type="button" class="w-8 h-8 flex items-center justify-center rounded-md border border-[#E2BFB9] bg-white keranjang-qty" data-menu-id="<?php echo $mid; ?>" data-delta="1" aria-label="Tambah">
                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 6H0V4.5H4.5V0H6V4.5H10.5V6H6V10.5H4.5V6Z" fill="#800000"/></svg>
              </button>
            </div>
          </div>
        </div>
            <?php if (!$isLast) { ?>
        <div class="h-px bg-[#EFEEEB]"></div>
            <?php }
          }
          ?>
      </div>

      <div class="flex items-center justify-between px-6 py-3 mt-6 bg-[#F4F3F1] border-t border-[#EFEEEB]">
        <span class="text-[#5A413D] text-sm font-normal leading-6 uppercase tracking-wide">SUBTOTAL WARUNG</span>
        <span class="text-[#800000] text-base font-bold leading-6 text-right"><?php echo htmlspecialchars((string) $g['subtotal_label'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    </div>
          <?php
      }
  }
?>

  <?php if ($groups !== []) { ?>
  <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4 pt-0">
    <div class="flex flex-col gap-4 bg-[#800000] rounded-3xl px-6 py-5 shadow-[0_-4px_30px_rgba(128,0,0,0.20)]">
      <div class="flex items-center justify-between">
        <div class="flex flex-col gap-0.5">
          <span class="text-white/70 text-xs font-medium tracking-[2px] uppercase">TOTAL PEMBAYARAN</span>
          <span class="text-white text-2xl font-bold leading-tight" id="keranjangTotal"><?php echo htmlspecialchars($totalLabel, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="flex items-center gap-1.5 bg-white/20 rounded-xl px-3 py-2">
          <span class="text-white text-sm font-semibold"><?php echo (int) $itemCount; ?> item</span>
        </div>
      </div>
      <a href="./index.php?page=checkout" class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-white shadow text-center">
        <span class="text-[#800000] text-base font-bold leading-6">Lanjut ke Ringkasan</span>
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M10.1458 7.5H0V5.83333H10.1458L5.47917 1.16667L6.66667 0L13.3333 6.66667L6.66667 13.3333L5.47917 12.1667L10.1458 7.5Z" fill="#800000"/></svg>
      </a>
    </div>
  </div>
  <?php } ?>
</main>

<script>
(function () {
  const root = document.body.getAttribute("data-api-root");
  if (!root) return;

  async function postCart(body) {
    const res = await fetch(root + "/cart.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "same-origin",
      body: JSON.stringify(body),
    });
    const data = await res.json().catch(() => ({}));
    if (!data.ok) throw new Error(data.error || "Gagal memperbarui keranjang");
    return data;
  }

  document.querySelectorAll(".keranjang-qty").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = parseInt(btn.getAttribute("data-menu-id") || "0", 10);
      const delta = parseInt(btn.getAttribute("data-delta") || "0", 10);
      const row = btn.closest("[data-menu-id]");
      const qtyEl = row ? row.querySelector(".keranjang-qty-value") : null;
      const cur = qtyEl ? parseInt(qtyEl.textContent || "1", 10) : 1;
      const next = Math.max(0, cur + delta);
      try {
        await postCart({ action: "set", menu_id: id, qty: next, note: "" });
        window.location.reload();
      } catch (e) {
        window.ScanteenUi?.showError?.({
          title: "Keranjang",
          message: e && e.message ? String(e.message) : "Gagal memperbarui keranjang.",
        });
      }
    });
  });

  document.querySelectorAll(".keranjang-remove").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = parseInt(btn.getAttribute("data-menu-id") || "0", 10);
      try {
        await postCart({ action: "remove", menu_id: id });
        window.location.reload();
      } catch (e) {
        window.ScanteenUi?.showError?.({
          title: "Keranjang",
          message: e && e.message ? String(e.message) : "Gagal memperbarui keranjang.",
        });
      }
    });
  });
})();
</script>
