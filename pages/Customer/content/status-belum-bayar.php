<?php

declare(strict_types=1);

use App\Customer\OrderUi;
use App\Repositories\VenueRepository;
use App\Customer\CustomerSessionKeys;

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=status-belum-bayar');
    exit;
}

$ord = $customerOrder ?? null;
$tok = $ord !== null ? (string) $ord['public_token'] : '';
$secs = $ord !== null ? OrderUi::countdownSeconds($ord['payment_deadline_at'] !== null ? (string) $ord['payment_deadline_at'] : null) : 900;
$orderNum = htmlspecialchars((string) ($ord['order_number'] ?? '-'), ENT_QUOTES, 'UTF-8');
$tableNum = htmlspecialchars((string) ($customerContext->tableNumber ?? '?'), ENT_QUOTES, 'UTF-8');
$paidHref = './index.php?page=status-sudah-bayar&o=' . rawurlencode($tok);
$simulate = defined('SCANTEEN_CUSTOMER_SIMULATE_PAYMENT') && SCANTEEN_CUSTOMER_SIMULATE_PAYMENT === true;

// Check available payment methods
$venueId = (int) ($_SESSION[CustomerSessionKeys::VENUE_ID] ?? 0);
$venue = (new VenueRepository())->findById($venueId);

$allowQris = (bool) ($venue['allow_qris'] ?? true);
$allowCash = (bool) ($venue['allow_cash'] ?? true);
$allowDebit = (bool) ($venue['allow_debit'] ?? false);

$allowedCount = 0;
if ($allowQris) $allowedCount++;
if ($allowCash) $allowedCount++;
if ($allowDebit) $allowedCount++;

$canChangeMethod = $allowedCount > 1;
?>

<!-- Scrollable Content -->
<main class="flex-1 flex flex-col gap-5 px-4 pt-5 pb-32">

    <!-- Order ID Card -->
    <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] px-5 py-5 flex items-center justify-between">
        <div class="flex flex-col gap-1 text-left">
            <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4">
                ID PESANAN
            </span>
            <span class="font-inter text-[#261817] text-xl font-bold leading-7">
                <?php echo $orderNum; ?>
            </span>
        </div>
        <div class="flex flex-col items-center justify-center bg-[#7B0009] rounded-xl px-3 py-2 min-w-[52px]">
            <span class="text-white text-[10px] font-semibold tracking-wider uppercase leading-none">MEJA</span>
            <span class="font-inter text-white text-xl font-black leading-tight"><?php echo $tableNum; ?></span>
        </div>
    </div>

    <!-- Payment Alert Card -->
    <div class="bg-[#7B0009] rounded-2xl p-6 flex flex-col items-center text-center gap-3 shadow-[0_4px_12px_rgba(123,0,9,0.2)]">
        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8V12L15 15" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="9" stroke="white" stroke-width="2"/>
            </svg>
        </div>
        <div class="flex flex-col gap-1">
            <h3 class="text-white text-base font-bold">Menunggu Pembayaran</h3>
            <p class="text-white/80 text-sm">Selesaikan pembayaran sebelum waktu habis</p>
        </div>
        <div class="mt-2 px-6 py-2 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm">
            <span id="timer-waiting" class="text-white font-mono text-xl font-bold tracking-widest" data-countdown-seconds="<?php echo (int) $secs; ?>">00:00</span>
        </div>
    </div>

    <!-- Order Status Section -->
    <div class="bg-white rounded-2xl border border-[#F3F4F6] shadow-[0_1px_2px_rgba(0,0,0,0.05)] px-5 pt-5 pb-6">
        <div class="flex items-center gap-2 mb-5">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 4H15M1 8H10M1 12H7" stroke="#675C5C" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span class="text-[#675C5C] text-xs font-semibold tracking-[0.6px] uppercase leading-4 text-left">
                Status Pesanan
            </span>
        </div>

        <div class="flex flex-col">
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-[#7B0009] border-[#7B0009]">
                        <span class="text-white text-xs font-bold">1</span>
                    </div>
                    <div class="w-px flex-1 bg-[#E5D5D5] my-1 min-h-[20px]"></div>
                </div>
                <div class="pb-5 text-left">
                    <p class="text-[#7B0009] text-sm font-bold leading-5">Menunggu</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Pesanan belum dibayar</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center opacity-50">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2 bg-white border-[#E5D5D5]"></div>
                </div>
                <div class="text-left opacity-50">
                    <p class="text-[#59413E] text-sm font-bold leading-5">Langkah berikutnya</p>
                    <p class="text-[#59413E] text-xs font-normal leading-4 mt-0.5">Setelah dibayar, pesanan dikonfirmasi warung</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Bottom Action Bar -->
<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
    <div class="flex flex-col gap-3 bg-[#FAF9F6] rounded-3xl px-5 py-4 shadow-[0_-4px_30px_rgba(0,0,0,0.08)] border border-[#EFEEEB]">
        <?php 
        $method = $ord['payment_method'] ?? 'kasir';
        $bayarHref = './index.php?page=bayar-' . $method . '&o=' . rawurlencode($tok);
        if ($simulate) { ?>
            <button type="button" id="btnSimulatePay"
                class="w-full py-4 rounded-2xl bg-[#7B0009] text-white font-bold text-base transition-all hover:bg-[#6a0000] active:scale-[0.98]">
                Simulasi bayar (demo)
            </button>
        <?php } else { ?>
            <button type="button" onclick="window.location.href='<?php echo htmlspecialchars($bayarHref, ENT_QUOTES, 'UTF-8'); ?>'"
                class="w-full py-4 rounded-2xl bg-[#7B0009] text-white font-bold text-base transition-all hover:bg-[#6a0000] active:scale-[0.98]">
                Bayar Sekarang
            </button>
        <?php } ?>
        <?php if ($canChangeMethod) { ?>
        <button type="button" onclick="window.location.href='./index.php?page=pilih-pembayaran&o=<?php echo rawurlencode($tok); ?>'"
            class="w-full py-3 rounded-2xl border-2 border-[#7B0009] text-[#7B0009] font-bold text-base transition-all hover:bg-[#7B0009]/5 active:scale-[0.98]">
            Ubah Metode Pembayaran
        </button>
        <?php } else { ?>
        <button type="button" disabled
            class="w-full py-3 rounded-2xl border-2 border-gray-300 text-gray-400 font-bold text-base cursor-not-allowed">
            Ubah Metode Pembayaran
        </button>
        <?php } ?>
    </div>
</div>

<script>
(function () {
  const el = document.getElementById("timer-waiting");
  if (!el) return;
  let seconds = parseInt(el.getAttribute("data-countdown-seconds") || "0", 10);
  const render = () => {
    const m = Math.floor(Math.max(0, seconds) / 60).toString().padStart(2, "0");
    const sec = (Math.max(0, seconds) % 60).toString().padStart(2, "0");
    el.textContent = `${m}:${sec}`;
  };
  render();
  const interval = setInterval(() => {
    if (seconds <= 0) {
      clearInterval(interval);
      el.textContent = "00:00";
      return;
    }
    seconds -= 1;
    render();
  }, 1000);

  const apiRoot = document.body.getAttribute("data-api-root");
  const btn = document.getElementById("btnSimulatePay");
  if (btn && apiRoot) {
    btn.addEventListener("click", async () => {
      btn.disabled = true;
      try {
        const res = await fetch(apiRoot + "/simulate-pay.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
          body: JSON.stringify({ public_token: <?php echo json_encode($tok); ?> }),
        });
        const data = await res.json().catch(() => ({}));
        if (!data.ok) {
          window.ScanteenUi?.showError?.({
            title: "Pembayaran",
            message: data.error || "Gagal memperbarui status pembayaran.",
            detail: data.detail || "",
          });
          btn.disabled = false;
          return;
        }
        window.location.href = <?php echo json_encode($paidHref); ?>;
      } catch (e) {
        window.ScanteenUi?.showError?.({
          title: "Koneksi bermasalah",
          message: "Tidak dapat menghubungi server.",
          detail: e && e.message ? String(e.message) : "",
        });
        btn.disabled = false;
      }
    });
  }
})();
</script>
