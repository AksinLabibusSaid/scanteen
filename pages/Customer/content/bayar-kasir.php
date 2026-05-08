  <!-- Countdown Banner -->
  <div class="customer-timer-bar flex items-center justify-center gap-2 px-5 py-3">
    <!-- Timer icon -->
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="12" cy="13" r="8" stroke="white" stroke-width="2"/>
      <path d="M12 9v4l3 3" stroke="white" stroke-width="2" stroke-linecap="round"/>
      <path d="M9 2h6" stroke="white" stroke-width="2" stroke-linecap="round"/>
      <path d="M12 2v2" stroke="white" stroke-width="2" stroke-linecap="round"/>
    </svg>
    <span class="text-sm text-white font-normal">
      Selesaikan pembayaran dalam
      <span class="font-bold underline tracking-wide" id="timer" data-countdown-seconds="899">14:59</span>
    </span>
  </div>

  <!-- Main content -->
  <main class="px-5 py-6 flex flex-col gap-5 pb-36">

    <!-- Order info card -->
    <div class="bg-white rounded-3xl border border-[#D0D0D0] shadow-[0_1px_2px_rgba(0,0,0,0.05)] overflow-hidden">

      <!-- Order ID + Total Bayar row -->
      <div class="flex items-start justify-between px-5 py-5 border-b border-border-divider">
        <div class="flex flex-col gap-0.5">
          <span class="text-[10px] font-bold uppercase tracking-[1px] text-text-muted">ORDER ID</span>
          <span class="text-lg font-bold tracking-[-0.45px] text-text-title">#ORD-1012-0004</span>
        </div>
        <div class="flex flex-col gap-0.5 items-end">
          <span class="text-[10px] font-bold uppercase tracking-[1px] text-text-muted">TOTAL BAYAR</span>
          <span class="text-lg font-bold tracking-[-0.45px] text-maroon">Rp 100.000</span>
        </div>
      </div>

      <!-- Order code section -->
      <div class="px-5 py-6 flex flex-col gap-0">
        <div class="customer-code-box flex flex-col items-center gap-2 px-6 py-6">
          <span class="text-[10px] font-bold uppercase tracking-[1.5px] text-text-muted">KODE PESANAN</span>
          <span class="text-3xl font-black tracking-[6px] text-text-title mt-1"># O R D - 1 0 1 2 - 0 0 0 4</span>
          <p class="text-sm text-text-muted mt-1">Tunjukkan kode ini ke kasir</p>
        </div>
      </div>

    </div>

    <!-- Payment steps card -->
    <div class="bg-white rounded-3xl border border-[#D0D0D0] shadow-[0_1px_2px_rgba(0,0,0,0.05)] px-5 py-6 flex flex-col gap-5">

      <!-- Section heading -->
      <div class="flex items-center gap-2">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="3" y="3" width="8" height="8" rx="1.5" stroke="#1A1C1A" stroke-width="1.8"/>
          <rect x="3" y="13" width="8" height="8" rx="1.5" stroke="#1A1C1A" stroke-width="1.8"/>
          <path d="M15 5h6M15 9h4M15 15h6M15 19h4" stroke="#1A1C1A" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        <span class="text-base font-bold text-text-dark">Langkah Pembayaran</span>
      </div>

      <!-- Steps list -->
      <div class="flex flex-col gap-4">

        <!-- Step 1 -->
        <div class="flex items-start gap-3">
          <div class="customer-step-num shrink-0">1</div>
          <p class="text-sm text-text-dark leading-5 pt-0.5">
            Tunjukkan <span class="text-maroon font-semibold">Kode Pesanan</span> atau <span class="text-maroon font-semibold">QR</span> ke kasir
          </p>
        </div>

        <!-- Step 2 -->
        <div class="flex items-start gap-3">
          <div class="customer-step-num shrink-0">2</div>
          <p class="text-sm text-text-dark leading-5 pt-0.5">
            Lakukan pembayaran tunai atau debit
          </p>
        </div>

        <!-- Step 3 -->
        <div class="flex items-start gap-3">
          <div class="customer-step-num shrink-0">3</div>
          <p class="text-sm text-text-dark leading-5 pt-0.5">
            Tunggu kasir memvalidasi pembayaran
          </p>
        </div>

        <!-- Step 4 -->
        <div class="flex items-start gap-3">
          <div class="customer-step-num shrink-0">4</div>
          <p class="text-sm text-text-dark leading-5 pt-0.5">
            Pesanan akan diteruskan ke dapur
          </p>
        </div>

        <!-- Step 5 -->
        <div class="flex items-start gap-3">
          <div class="customer-step-num shrink-0">5</div>
          <p class="text-sm text-text-dark leading-5 pt-0.5">
            Pantau status di halaman <a href="index-single.html" class="underline-link">Lacak Pesanan</a>
          </p>
        </div>

      </div>
    </div>

  </main>

  <!-- Bottom action buttons -->
  <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
    <div class="flex items-center gap-3 bg-[#FAF9F6] rounded-3xl px-5 py-4 shadow-[0_-4px_30px_rgba(0,0,0,0.08)] border border-[#EFEEEB]">
      <button class="customer-btn-primary flex-1 !py-4 rounded-2xl" type="button" onclick="window.location.href='./index.php?page=status-belum-bayar'">
        Status Pesanan
      </button>
      <button class="customer-btn-icon h-[56px] w-[56px] min-w-[56px] rounded-2xl" type="button" onclick="window.location.href='./index.php?page=struk'" aria-label="Download" title="Download">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 16l-5-5h3V4h4v7h3l-5 5z" fill="#800000"/>
          <path d="M5 18h14v2H5v-2z" fill="#800000"/>
        </svg>
      </button>
    </div>
  </div>
