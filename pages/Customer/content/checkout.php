<?php

declare(strict_types=1);

use App\Support\Money;

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=checkout');
    exit;
}

$sum = $customerCartSummary ?? ['groups' => [], 'subtotal' => 0.0, 'itemCount' => 0, 'warungCount' => 0];
if (($sum['groups'] ?? []) === []) {
    header('Location: ./index.php?page=keranjang');
    exit;
}

$draft = $checkoutDraft ?? null;
$nameVal = htmlspecialchars((string) ($draft['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$emailVal = htmlspecialchars((string) ($draft['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$dineInActive = ($draft['dining_type'] ?? 'dine_in') !== 'take_away';

$subtotal = (float) $sum['subtotal'];
$serviceTax = round($subtotal * 0.10, 2);
$grandTotal = round($subtotal + $serviceTax, 2);
?>

<main class="flex-1 flex flex-col gap-6 px-6 pt-6 pb-48">

  <section class="flex flex-col gap-3">
    <div class="flex items-center justify-between">
      <h2 class="text-[#570000] text-2xl font-semibold leading-[1.4]">Detail Customer</h2>
      <a href="./index.php?page=keranjang" class="text-sm font-semibold text-[#800000] underline">Ubah keranjang</a>
    </div>
    <div class="bg-white rounded-3xl shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] px-6 py-[23px] flex flex-col gap-[15px]">
      <div class="flex flex-col gap-[4.39px]">
        <label class="text-[#5F5E5B] text-xs font-medium leading-[1.2]" for="customer_name">Nama Lengkap</label>
        <input id="customer_name" name="customer_name" type="text" required
          class="w-full px-4 py-[14px] rounded-lg border border-[#E7E5E4] bg-[#faf9f6] text-[#1A1C1A] text-base outline-none focus:border-[#800000]"
          placeholder="Masukkan nama Anda" value="<?php echo $nameVal; ?>">
      </div>
      <div class="flex flex-col gap-[4.39px]">
        <label class="text-[#5F5E5B] text-xs font-medium leading-[1.2]" for="customer_email">Kirim struk ke email (opsional)</label>
        <input id="customer_email" name="customer_email" type="email"
          class="w-full px-4 py-[14px] rounded-lg border border-[#E7E5E4] bg-[#faf9f6] text-[#1A1C1A] text-base outline-none focus:border-[#800000]"
          placeholder="name@email.com" value="<?php echo $emailVal; ?>">
      </div>
    </div>
  </section>

  <section class="flex flex-col gap-3">
    <h2 class="text-[#570000] text-2xl font-semibold leading-[1.4]">Dining Option</h2>
    <div class="bg-white rounded-3xl shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] p-2 flex gap-1">
      <button type="button" id="dineInBtn" class="dine-in-btn flex-1 flex items-center justify-center gap-3 h-11 rounded-[20px] transition-colors font-semibold text-sm tracking-[0.7px] <?php echo $dineInActive ? 'bg-[#800000] text-white' : 'bg-transparent text-[#5F5E5B]'; ?>">
        Dine In
      </button>
      <button type="button" id="takeAwayBtn" class="take-away-btn flex-1 flex items-center justify-center gap-3 h-11 rounded-[20px] transition-colors font-semibold text-sm tracking-[0.7px] <?php echo !$dineInActive ? 'bg-[#800000] text-white' : 'bg-transparent text-[#5F5E5B]'; ?>">
        Take Away
      </button>
    </div>
    <input type="hidden" id="dining_type" value="<?php echo $dineInActive ? 'dine_in' : 'take_away'; ?>">
  </section>

  <section class="flex flex-col gap-3">
    <h2 class="text-[#570000] text-2xl font-semibold leading-[1.4]">Ringkasan Pesanan</h2>
    <?php foreach ($sum['groups'] as $g) { ?>
    <div class="warung-card rounded-3xl bg-white shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] overflow-hidden border border-[#F3F4F6]">
      <div class="warung-header flex items-center gap-2 px-5 py-4 border-b border-[#F3F4F6]">
        <span class="warung-name font-bold text-[#1A1C1A]"><?php echo htmlspecialchars((string) $g['warung_name'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
      <div class="warung-content px-5 py-4 flex flex-col gap-4">
        <?php foreach ($g['items'] as $it) { ?>
        <div class="cart-item">
          <div class="item-row flex justify-between gap-3">
            <div class="item-details flex-1">
              <div class="item-header flex gap-2">
                <span class="item-quantity text-[#800000] font-bold"><?php echo (int) $it['qty']; ?></span>
                <span class="item-name font-semibold text-[#1A1C1A]"><?php echo htmlspecialchars((string) $it['name'], ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <div class="item-note text-xs text-[#6B7280] mt-1"><?php echo htmlspecialchars($it['note'] !== '' ? (string) $it['note'] : '-', ENT_QUOTES, 'UTF-8'); ?></div>
              <div class="item-price text-[#800000] font-semibold mt-2"><?php echo htmlspecialchars((string) $it['line_subtotal_label'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
      <div class="warung-footer flex justify-between px-5 py-3 bg-[#FAFAF9] border-t border-[#F3F4F6]">
        <span class="footer-label text-sm text-[#5F5E5B]">Subtotal</span>
        <span class="footer-amount font-bold text-[#800000]"><?php echo htmlspecialchars((string) $g['subtotal_label'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    </div>
    <?php } ?>
  </section>

  <section class="flex flex-col gap-2 text-sm text-[#5F5E5B]">
    <div class="flex justify-between"><span>Subtotal</span><span><?php echo htmlspecialchars(Money::formatIdr($subtotal), ENT_QUOTES, 'UTF-8'); ?></span></div>
    <div class="flex justify-between"><span>Service (10%)</span><span><?php echo htmlspecialchars(Money::formatIdr($serviceTax), ENT_QUOTES, 'UTF-8'); ?></span></div>
  </section>
</main>

<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
  <div class="flex flex-col gap-4 bg-[#800000] rounded-3xl px-6 py-5 shadow-[0_-4px_30px_rgba(128,0,0,0.20)]">
    <div class="flex items-center justify-between">
      <div class="flex flex-col gap-0.5">
        <span class="text-white/70 text-xs font-medium tracking-[2px] uppercase">TOTAL PEMBAYARAN</span>
        <span class="summary-amount text-white text-2xl font-bold leading-tight"><?php echo htmlspecialchars(Money::formatIdr($grandTotal), ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
      <div class="flex items-center gap-1.5 bg-white/20 rounded-xl px-3 py-2">
        <span class="text-white text-sm font-semibold"><?php echo (int) $sum['warungCount']; ?> Toko</span>
      </div>
    </div>
    <button type="button" id="btnCheckoutContinue" class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-white shadow">
      <span class="text-[#800000] text-base font-bold leading-6">Lanjut pembayaran</span>
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M12.175 9H0V7H12.175L6.575 1.4L8 0L16 8L8 16L6.575 14.6L12.175 9Z" fill="#800000"/></svg>
    </button>
  </div>
</div>

<script>
(function () {
  const apiRoot = document.body.getAttribute("data-api-root");
  const dineInBtn = document.getElementById("dineInBtn");
  const takeAwayBtn = document.getElementById("takeAwayBtn");
  const diningType = document.getElementById("dining_type");
  const btn = document.getElementById("btnCheckoutContinue");

  function setDining(mode) {
    if (!diningType) return;
    diningType.value = mode;
    if (dineInBtn && takeAwayBtn) {
      if (mode === "dine_in") {
        dineInBtn.className = "dine-in-btn flex-1 flex items-center justify-center gap-3 h-11 rounded-[20px] transition-colors font-semibold text-sm tracking-[0.7px] bg-[#800000] text-white";
        takeAwayBtn.className = "take-away-btn flex-1 flex items-center justify-center gap-3 h-11 rounded-[20px] transition-colors font-semibold text-sm tracking-[0.7px] bg-transparent text-[#5F5E5B]";
      } else {
        takeAwayBtn.className = "take-away-btn flex-1 flex items-center justify-center gap-3 h-11 rounded-[20px] transition-colors font-semibold text-sm tracking-[0.7px] bg-[#800000] text-white";
        dineInBtn.className = "dine-in-btn flex-1 flex items-center justify-center gap-3 h-11 rounded-[20px] transition-colors font-semibold text-sm tracking-[0.7px] bg-transparent text-[#5F5E5B]";
      }
    }
  }

  dineInBtn?.addEventListener("click", () => setDining("dine_in"));
  takeAwayBtn?.addEventListener("click", () => setDining("take_away"));

  btn?.addEventListener("click", async () => {
    const name = document.getElementById("customer_name")?.value?.trim() || "";
    const email = document.getElementById("customer_email")?.value?.trim() || "";
    const dtype = diningType?.value || "dine_in";
    if (!name) {
      window.ScanteenUi?.showError?.({
        title: "Data belum lengkap",
        message: "Nama wajib diisi sebelum melanjutkan ke pembayaran.",
      });
      return;
    }
    if (!apiRoot) return;
    const fd = new FormData();
    fd.append("customer_name", name);
    fd.append("customer_email", email);
    fd.append("dining_type", dtype);
    const res = await fetch(apiRoot + "/checkout-draft.php", { method: "POST", body: fd, credentials: "same-origin" });
    const data = await res.json().catch(() => ({}));
    if (!data.ok) {
      window.ScanteenUi?.showError?.({
        title: "Tidak dapat menyimpan",
        message: data.error || "Gagal menyimpan data pemesan.",
        detail: data.detail || "",
      });
      return;
    }
    window.location.href = "./index.php?page=pilih-pembayaran";
  });
})();
</script>
