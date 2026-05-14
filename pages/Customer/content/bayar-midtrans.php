<?php
declare(strict_types=1);

use App\Customer\OrderUi;
use App\Support\Money;

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=bayar-midtrans');
    exit;
}

$ord = $customerOrder ?? null;
$tok = $ord !== null ? (string) $ord['public_token'] : '';
$secs = $ord !== null ? OrderUi::countdownSeconds($ord['payment_deadline_at'] !== null ? (string) $ord['payment_deadline_at'] : null) : 900;
$totalFmt = Money::formatIdr((float) ($ord['total'] ?? 0));
$orderNum = htmlspecialchars((string) ($ord['order_number'] ?? ''), ENT_QUOTES, 'UTF-8');
$statusHref = './index.php?page=status-belum-bayar&o=' . rawurlencode($tok);
$cfgPay = require dirname(__DIR__, 3) . '/config/payment.php';
$hasMidtransKeys = trim((string) ($cfgPay['midtrans_server_key'] ?? '')) !== ''
    && trim((string) ($cfgPay['midtrans_client_key'] ?? '')) !== '';
?>

<main class="flex-1 flex flex-col gap-6 px-4 pt-5 pb-32">
    <div class="flex items-center justify-center gap-2 px-4 py-2 bg-[#800000] rounded-lg">
        <span class="text-sm text-white font-semibold">
            Selesaikan pembayaran dalam
            <span id="countdown-midtrans" class="font-bold underline ml-1" data-countdown-seconds="<?php echo (int) $secs; ?>">00:00</span>
        </span>
    </div>

    <div class="bg-white rounded-2xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col gap-4 p-6">
        <div class="flex justify-between items-start pt-2">
            <div class="flex flex-col gap-1">
                <span class="text-[#675C5C] text-xs font-semibold uppercase">ORDER</span>
                <span class="text-[#261817] text-base font-bold"><?php echo $orderNum !== '' ? $orderNum : '-'; ?></span>
            </div>
            <div class="flex flex-col gap-1 items-end">
                <span class="text-[#675C5C] text-xs font-semibold uppercase">TOTAL</span>
                <span class="text-[#7B0009] text-base font-semibold"><?php echo htmlspecialchars($totalFmt, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <p class="text-sm text-[#59413E] leading-relaxed">
            <?php if ($hasMidtransKeys): ?>
                Kartu kredit/debit, e-wallet, dan metode lain lewat <strong>Midtrans Snap</strong>.
            <?php else: ?>
                <strong>Midtrans belum dikonfigurasi</strong> (kunci server &amp; client). Untuk uji alur, gunakan tombol simulasi bayar di bawah jika mode demo aktif, atau isi <code class="text-xs bg-stone-100 px-1 rounded">MIDTRANS_SERVER_KEY</code> dan <code class="text-xs bg-stone-100 px-1 rounded">MIDTRANS_CLIENT_KEY</code> di environment server.
            <?php endif; ?>
        </p>

        <?php if ($hasMidtransKeys): ?>
            <button type="button" id="btnMidtransPay" class="w-full py-4 rounded-xl bg-[#7B0009] text-white text-base font-bold shadow-md active:opacity-90">
                Bayar sekarang
            </button>
        <?php endif; ?>

        <?php if (defined('SCANTEEN_CUSTOMER_SIMULATE_PAYMENT') && SCANTEEN_CUSTOMER_SIMULATE_PAYMENT === true): ?>
            <button type="button" id="btnSimulateMidtrans" class="w-full py-3 rounded-xl border-2 border-[#800000] text-[#800000] text-sm font-bold">
                Simulasikan pembayaran berhasil (demo)
            </button>
        <?php endif; ?>

        <a href="<?php echo htmlspecialchars($statusHref, ENT_QUOTES, 'UTF-8'); ?>" class="block text-center text-sm text-[#675C5C] font-medium py-2">
            Lihat status pesanan
        </a>
    </div>
</main>

<?php if ($hasMidtransKeys): ?>
<?php
$sandboxSnap = (bool) ($cfgPay['midtrans_sandbox'] ?? true);
$snapScriptSrc = $sandboxSnap
    ? 'https://app.sandbox.midtrans.com/snap/snap.js'
    : 'https://app.midtrans.com/snap/snap.js';
?>
<script src="<?php echo htmlspecialchars($snapScriptSrc, ENT_QUOTES, 'UTF-8'); ?>" data-client-key="<?php echo htmlspecialchars(trim((string) $cfgPay['midtrans_client_key']), ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function () {
  const apiRoot = document.body.getAttribute('data-api-root');
  const btn = document.getElementById('btnMidtransPay');
  if (!btn || !apiRoot) return;
  btn.addEventListener('click', async function () {
    btn.disabled = true;
    try {
      const res = await fetch(apiRoot + '/midtrans-snap-token.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: '{}',
      });
      const data = await res.json();
      if (!data.ok) {
        const show = window.ScanteenUi && window.ScanteenUi.showError
          ? window.ScanteenUi.showError.bind(window.ScanteenUi)
          : function (o) { alert(o.message); };
        show({ title: 'Midtrans', message: data.error || 'Gagal', detail: data.detail || '' });
        btn.disabled = false;
        return;
      }
      if (typeof snap === 'undefined' || !data.snap_token) {
        alert('Snap tidak tersedia.');
        btn.disabled = false;
        return;
      }
      snap.pay(data.snap_token, {
        onSuccess: function () {
          window.location.href = './index.php?page=status-sudah-bayar&o=<?php echo rawurlencode($tok); ?>';
        },
        onPending: function () {
          window.location.href = '<?php echo htmlspecialchars($statusHref, ENT_QUOTES, 'UTF-8'); ?>';
        },
        onError: function () {
          window.location.href = '<?php echo htmlspecialchars($statusHref, ENT_QUOTES, 'UTF-8'); ?>';
        },
        onClose: function () {
          btn.disabled = false;
        },
      });
    } catch (e) {
      alert('Koneksi gagal');
      btn.disabled = false;
    }
  });
})();
</script>
<?php endif; ?>

<?php if (defined('SCANTEEN_CUSTOMER_SIMULATE_PAYMENT') && SCANTEEN_CUSTOMER_SIMULATE_PAYMENT === true): ?>
<script>
document.getElementById('btnSimulateMidtrans')?.addEventListener('click', async function () {
  const apiRoot = document.body.getAttribute('data-api-root');
  if (!apiRoot) return;
  this.disabled = true;
  const res = await fetch(apiRoot + '/simulate-pay.php', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' }, body: '{}' });
  const data = await res.json();
  if (data.ok) {
    window.location.href = './index.php?page=status-sudah-bayar&o=<?php echo rawurlencode($tok); ?>';
  } else {
    alert(data.error || 'Gagal');
    this.disabled = false;
  }
});
</script>
<?php endif; ?>
