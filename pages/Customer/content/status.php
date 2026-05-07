  <!-- Main content -->
  <main class="px-5 py-6 flex flex-col gap-12 pb-44">
    <!-- Order card -->
    <div class="rounded-card border border-[#D0D0D0] bg-white shadow-card overflow-hidden">
      <!-- Order ID row -->
      <div class="flex items-center justify-between px-5 py-5 border-b border-border-divider bg-white/50">
        <div class="flex flex-col gap-0.5">
          <span class="order-id">ID PESANAN</span>
          <span class="order-number">#ORD-1012-0004</span>
        </div>
        <div class="flex flex-col items-center justify-center w-10 h-10 rounded-xl bg-maroon">
          <span class="text-[8px] font-bold uppercase text-white/80 leading-none">MEJA</span>
          <span class="text-base font-bold text-white leading-none mt-0.5">12</span>
        </div>
      </div>

      <!-- Content -->
      <div class="flex flex-col gap-8 px-5 py-5">
        <!-- Payment alert banner -->
        <div class="rounded-2xl bg-maroon px-6 py-6 flex flex-col items-center gap-2">
          <!-- Clipboard icon -->
          <svg width="36" height="44" viewBox="0 0 36 44" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M28 42C25.2333 42 22.875 41.025 20.925 39.075C18.975 37.125 18 34.7667 18 32C18 29.2333 18.975 26.875 20.925 24.925C22.875 22.975 25.2333 22 28 22C30.7667 22 33.125 22.975 35.075 24.925C37.025 26.875 38 29.2333 38 32C38 34.7667 37.025 37.125 35.075 39.075C33.125 41.025 30.7667 42 28 42ZM31.35 36.75L32.75 35.35L29 31.6V26H27V32.4L31.35 36.75ZM4 40C2.9 40 1.95833 39.6083 1.175 38.825C0.391667 38.0417 0 37.1 0 36V8C0 6.9 0.391667 5.95833 1.175 5.175C1.95833 4.39167 2.9 4 4 4H12.35C12.7167 2.83333 13.4333 1.875 14.5 1.125C15.5667 0.375 16.7333 0 18 0C19.3333 0 20.525 0.375 21.575 1.125C22.625 1.875 23.3333 2.83333 23.7 4H32C33.1 4 34.0417 4.39167 34.825 5.175C35.6083 5.95833 36 6.9 36 8V20.5C35.4 20.0667 34.7667 19.7 34.1 19.4C33.4333 19.1 32.7333 18.8333 32 18.6V8H28V14H8V8H4V36H14.6C14.8333 36.7333 15.1 37.4333 15.4 38.1C15.7 38.7667 16.0667 39.4 16.5 40H4ZM18 8C18.5667 8 19.0417 7.80833 19.425 7.425C19.8083 7.04167 20 6.56667 20 6C20 5.43333 19.8083 4.95833 19.425 4.575C19.0417 4.19167 18.5667 4 18 4C17.4333 4 16.9583 4.19167 16.575 4.575C16.1917 4.95833 16 5.43333 16 6C16 6.56667 16.1917 7.04167 16.575 7.425C16.9583 7.80833 17.4333 8 18 8Z" fill="white"/>
          </svg>
          <p class="text-base text-white font-normal mt-1">Menunggu Pembayaran</p>
          <p class="text-base text-white font-normal">
            Selesaikan pembayaran dalam
          </p>
          <div class="countdown">
            <span class="countdown-text" id="timer" data-countdown-seconds="1785">29:45</span>
          </div>
        </div>

        <!-- Order status timeline -->
        <div class="flex flex-col gap-5">
          <div class="flex items-center gap-2">
            <svg width="12" height="9" viewBox="0 0 12 9" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2.07083 8.79375L0 6.72292L0.816667 5.90625L2.05625 7.14583L4.53542 4.66667L5.35208 5.49792L2.07083 8.79375ZM2.07083 4.12708L0 2.05625L0.816667 1.23958L2.05625 2.47917L4.53542 0L5.35208 0.83125L2.07083 4.12708ZM6.41667 7.62708V6.46042H11.6667V7.62708H6.41667ZM6.41667 2.96042V1.79375H11.6667V2.96042H6.41667Z" fill="#5F5E5B"/>
            </svg>
            <span class="text-[11px] font-bold uppercase tracking-[1.1px] text-text-muted">STATUS PESANAN</span>
          </div>

          <div class="flex flex-col gap-6">
            <!-- Step 1 -->
            <div class="relative flex items-start gap-4">
              <div class="timeline-connector" style="height: calc(100% + 8px);"></div>
              <div class="step-circle">
                <span class="text-[10px] font-bold text-maroon leading-none">1</span>
              </div>
              <div class="flex flex-col">
                <span class="step-label">Menunggu</span>
                <span class="step-desc">Pesanan belum dibayar</span>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="relative flex items-start gap-4">
              <div class="timeline-connector" style="height: calc(100% + 8px);"></div>
              <div class="step-circle"></div>
              <div class="flex flex-col">
                <span class="step-label">Konfirmasi</span>
                <span class="step-desc">Pesanan terkirim</span>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="relative flex items-start gap-4">
              <div class="timeline-connector" style="height: calc(100% + 8px);"></div>
              <div class="step-circle"></div>
              <div class="flex flex-col">
                <span class="step-label">Proses</span>
                <span class="step-desc">Pesanan sedang disiapkan</span>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="relative flex items-start gap-4">
              <div class="timeline-connector" style="height: calc(100% + 8px);"></div>
              <div class="step-circle"></div>
              <div class="flex flex-col">
                <span class="step-label">Siap</span>
                <span class="step-desc">Pesanan sudah siap diantarkan</span>
              </div>
            </div>

            <!-- Step 5 -->
            <div class="flex items-start gap-4">
              <div class="step-circle"></div>
              <div class="flex flex-col">
                <span class="step-label">Selesai</span>
                <span class="step-desc">Pesanan telah sampai</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Detail menu -->
        <div class="flex flex-col gap-4">
          <span class="order-id">DETAIL MENU</span>

          <!-- Warung 1 - First items -->
          <div class="rounded-card border border-border-light bg-white overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-4 border-b border-border-divider">
              <svg width="15" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.2852 6.0375V12C14.2852 12.4125 14.1383 12.7656 13.8446 13.0594C13.5508 13.3531 13.1977 13.5 12.7852 13.5H2.2852C1.8727 13.5 1.51958 13.3531 1.22583 13.0594C0.932079 12.7656 0.785204 12.4125 0.785204 12V6.0375C0.497704 5.775 0.275829 5.4375 0.119579 5.025C-0.0366709 4.6125 -0.0397959 4.1625 0.110204 3.675L0.897704 1.125C0.997704 0.8 1.17583 0.53125 1.43208 0.31875C1.68833 0.10625 1.9852 0 2.3227 0H12.7477C13.0852 0 13.379 0.103125 13.629 0.309375C13.879 0.515625 14.0602 0.7875 14.1727 1.125L14.9602 3.675C15.1102 4.1625 15.1071 4.60625 14.9508 5.00625C14.7946 5.40625 14.5727 5.75 14.2852 6.0375ZM9.1852 5.25C9.5227 5.25 9.77895 5.13437 9.95395 4.90312C10.129 4.67188 10.1977 4.4125 10.1602 4.125L9.7477 1.5H8.2852V4.275C8.2852 4.5375 8.3727 4.76562 8.5477 4.95937C8.7227 5.15312 8.9352 5.25 9.1852 5.25ZM5.8102 5.25C6.0977 5.25 6.33208 5.15312 6.51333 4.95937C6.69458 4.76562 6.7852 4.5375 6.7852 4.275V1.5H5.3227L4.9102 4.125C4.8602 4.425 4.92583 4.6875 5.10708 4.9125C5.28833 5.1375 5.5227 5.25 5.8102 5.25ZM2.4727 5.25C2.6977 5.25 2.89458 5.16875 3.06333 5.00625C3.23208 4.84375 3.3352 4.6375 3.3727 4.3875L3.7852 1.5H2.3227L1.5727 4.0125C1.4977 4.2625 1.53833 4.53125 1.69458 4.81875C1.85083 5.10625 2.1102 5.25 2.4727 5.25ZM12.5977 5.25C12.9602 5.25 13.2227 5.10625 13.3852 4.81875C13.5477 4.53125 13.5852 4.2625 13.4977 4.0125L12.7102 1.5H11.2852L11.6977 4.3875C11.7352 4.6375 11.8383 4.84375 12.0071 5.00625C12.1758 5.16875 12.3727 5.25 12.5977 5.25ZM2.2852 12H12.7852V6.7125C12.7227 6.7375 12.6821 6.75 12.6633 6.75C12.6446 6.75 12.6227 6.75 12.5977 6.75C12.2602 6.75 11.9633 6.69375 11.7071 6.58125C11.4508 6.46875 11.1977 6.2875 10.9477 6.0375C10.7227 6.2625 10.4665 6.4375 10.179 6.5625C9.89145 6.6875 9.5852 6.75 9.2602 6.75C8.9227 6.75 8.60708 6.6875 8.31333 6.5625C8.01958 6.4375 7.7602 6.2625 7.5352 6.0375C7.3227 6.2625 7.07583 6.4375 6.79458 6.5625C6.51333 6.6875 6.2102 6.75 5.8852 6.75C5.5227 6.75 5.19458 6.6875 4.90083 6.5625C4.60708 6.4375 4.3477 6.2625 4.1227 6.0375C3.8602 6.3 3.60083 6.48438 3.34458 6.59062C3.08833 6.69687 2.7977 6.75 2.4727 6.75C2.4477 6.75 2.41958 6.75 2.38833 6.75C2.35708 6.75 2.3227 6.7375 2.2852 6.7125V12Z" fill="#800000"/>
              </svg>
              <span class="text-base font-bold text-text-heading flex-1">Warung 1</span>
              <span class="status-badge">PENDING</span>
            </div>
            <div class="flex flex-col gap-6 px-6 py-6">
              <!-- Item 1: Soto Babat -->
              <div class="flex flex-col gap-1">
                <div class="flex items-start gap-1">
                  <span class="item-qty w-7 shrink-0">1x</span>
                  <div class="flex flex-col gap-1">
                    <span class="item-name">Soto Babat</span>
                    <span class="item-price">Rp 12.000 / porsi</span>
                  </div>
                </div>
                <div class="ml-7">
                  <span class="item-note">Tambah sambal extra</span>
                </div>
              </div>

              <!-- Item 2: Wader Goreng -->
              <div class="flex flex-col gap-1">
                <div class="flex items-start gap-1">
                  <span class="item-qty w-7 shrink-0">2x</span>
                  <div class="flex flex-col gap-1">
                    <span class="item-name">Wader Goreng</span>
                    <span class="item-price">Rp 12.000 / porsi</span>
                  </div>
                </div>
                <div class="ml-7">
                  <span class="item-note">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Warung 1 - Second section (Rawon) -->
          <div class="rounded-card border border-border-light bg-white overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-4 border-b border-border-divider">
              <svg width="15" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.2852 6.0375V12C14.2852 12.4125 14.1383 12.7656 13.8446 13.0594C13.5508 13.3531 13.1977 13.5 12.7852 13.5H2.2852C1.8727 13.5 1.51958 13.3531 1.22583 13.0594C0.932079 12.7656 0.785204 12.4125 0.785204 12V6.0375C0.497704 5.775 0.275829 5.4375 0.119579 5.025C-0.0366709 4.6125 -0.0397959 4.1625 0.110204 3.675L0.897704 1.125C0.997704 0.8 1.17583 0.53125 1.43208 0.31875C1.68833 0.10625 1.9852 0 2.3227 0H12.7477C13.0852 0 13.379 0.103125 13.629 0.309375C13.879 0.515625 14.0602 0.7875 14.1727 1.125L14.9602 3.675C15.1102 4.1625 15.1071 4.60625 14.9508 5.00625C14.7946 5.40625 14.5727 5.75 14.2852 6.0375ZM9.1852 5.25C9.5227 5.25 9.77895 5.13437 9.95395 4.90312C10.129 4.67188 10.1977 4.4125 10.1602 4.125L9.7477 1.5H8.2852V4.275C8.2852 4.5375 8.3727 4.76562 8.5477 4.95937C8.7227 5.15312 8.9352 5.25 9.1852 5.25ZM5.8102 5.25C6.0977 5.25 6.33208 5.15312 6.51333 4.95937C6.69458 4.76562 6.7852 4.5375 6.7852 4.275V1.5H5.3227L4.9102 4.125C4.8602 4.425 4.92583 4.6875 5.10708 4.9125C5.28833 5.1375 5.5227 5.25 5.8102 5.25ZM2.4727 5.25C2.6977 5.25 2.89458 5.16875 3.06333 5.00625C3.23208 4.84375 3.3352 4.6375 3.3727 4.3875L3.7852 1.5H2.3227L1.5727 4.0125C1.4977 4.2625 1.53833 4.53125 1.69458 4.81875C1.85083 5.10625 2.1102 5.25 2.4727 5.25ZM12.5977 5.25C12.9602 5.25 13.2227 5.10625 13.3852 4.81875C13.5477 4.53125 13.5852 4.2625 13.4977 4.0125L12.7102 1.5H11.2852L11.6977 4.3875C11.7352 4.6375 11.8383 4.84375 12.0071 5.00625C12.1758 5.16875 12.3727 5.25 12.5977 5.25ZM2.2852 12H12.7852V6.7125C12.7227 6.7375 12.6821 6.75 12.6633 6.75C12.6446 6.75 12.6227 6.75 12.5977 6.75C12.2602 6.75 11.9633 6.69375 11.7071 6.58125C11.4508 6.46875 11.1977 6.2875 10.9477 6.0375C10.7227 6.2625 10.4665 6.4375 10.179 6.5625C9.89145 6.6875 9.5852 6.75 9.2602 6.75C8.9227 6.75 8.60708 6.6875 8.31333 6.5625C8.01958 6.4375 7.7602 6.2625 7.5352 6.0375C7.3227 6.2625 7.07583 6.4375 6.79458 6.5625C6.51333 6.6875 6.2102 6.75 5.8852 6.75C5.5227 6.75 5.19458 6.6875 4.90083 6.5625C4.60708 6.4375 4.3477 6.2625 4.1227 6.0375C3.8602 6.3 3.60083 6.48438 3.34458 6.59062C3.08833 6.69687 2.7977 6.75 2.4727 6.75C2.4477 6.75 2.41958 6.75 2.38833 6.75C2.35708 6.75 2.3227 6.7375 2.2852 6.7125V12Z" fill="#800000"/>
              </svg>
              <span class="text-base font-bold text-text-heading flex-1">Warung 1</span>
              <span class="status-badge">PENDING</span>
            </div>
            <div class="flex flex-col gap-6 px-6 py-6">
              <!-- Item: Rawon Jumbo -->
              <div class="flex flex-col gap-1">
                <div class="flex items-start gap-1">
                  <span class="item-qty w-7 shrink-0">1x</span>
                  <div class="flex flex-col gap-1">
                    <span class="item-name">Rawon Jumbo</span>
                    <span class="item-price">Rp 12.000 / porsi</span>
                  </div>
                </div>
                <div class="ml-7">
                  <span class="item-note">Kuah pisah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total -->
        <div class="flex items-center justify-between pt-4 border-t border-border-divider">
          <span class="text-sm font-medium text-text-dark">Total Pembayaran</span>
          <span class="text-lg font-bold text-text-dark">Rp 100.000</span>
        </div>
      </div>
    </div>
  </main>

  <!-- Bottom action buttons -->
  <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
    <div class="flex flex-col gap-3 bg-[#FAF9F6] rounded-3xl px-5 py-4 shadow-[0_-4px_30px_rgba(0,0,0,0.08)] border border-[#EFEEEB]">
      <button type="button" onclick="window.location.href='./index.php?page=pembayaran'"
        class="w-full py-4 rounded-2xl bg-[#800000] text-white font-bold text-base transition-all hover:bg-[#6a0000] active:scale-[0.98]">
        Bayar Sekarang
      </button>
      <button type="button"
        class="w-full py-3 rounded-2xl border-2 border-[#800000] text-[#800000] font-bold text-base transition-all hover:bg-[#800000]/5 active:scale-[0.98]">
        Ubah Metode Pembayaran
      </button>
    </div>
  </div>
