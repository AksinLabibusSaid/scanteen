<main class="flex-1 flex flex-col gap-6 px-6 pt-6 pb-48">
        
        <!-- Detail Customer Section -->
        <section class="flex flex-col gap-3">
          <h2 class="text-[#570000] text-2xl font-semibold leading-[1.4]">
            Detail Customer
          </h2>
          <div class="bg-white rounded-3xl shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] px-6 py-[23px] flex flex-col gap-[15px]">
            <div class="flex flex-col gap-[4.39px]">
              <label class="text-[#5F5E5B] text-xs font-medium leading-[1.2]">
                Nama Lengkap
              </label>
              <div class="flex items-start px-4 py-[14px] rounded-lg border border-[#E7E5E4] bg-[#faf9f6] overflow-hidden">
                <span class="text-[#6B7280] text-base font-normal">
                  Masukkan nama Anda
                </span>
              </div>
            </div>
            <div class="flex flex-col gap-[4.39px]">
              <label class="text-[#5F5E5B] text-xs font-medium leading-[1.2]">
                Kirim struk ke email:
              </label>
              <div class="flex items-start px-4 py-[14px] rounded-lg border border-[#E7E5E4] bg-[#faf9f6] overflow-hidden">
                <span class="text-[#6B7280] text-base font-normal">
                  name@email.com
                </span>
              </div>
            </div>
          </div>
        </section>

        <!-- Dining Option Section -->
        <section class="flex flex-col gap-3">
          <h2 class="text-[#570000] text-2xl font-semibold leading-[1.4]">
            Dining Option
          </h2>
          <div class="bg-white rounded-3xl shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] p-2 flex gap-1">
            <button id="dineInBtn" class="dine-in-btn flex-1 flex items-center justify-center gap-3 h-11 rounded-[20px] transition-colors font-semibold text-sm tracking-[0.7px] bg-[#800000] text-white">
              <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.5 16.6667V9.04167C1.79167 8.84722 1.19792 8.45833 0.71875 7.875C0.239583 7.29167 0 6.61111 0 5.83333V0H1.66667V5.83333H2.5V0H4.16667V5.83333H5V0H6.66667V5.83333C6.66667 6.61111 6.42708 7.29167 5.94792 7.875C5.46875 8.45833 4.875 8.84722 4.16667 9.04167V16.6667H2.5ZM10.8333 16.6667V10H8.33333V4.16667C8.33333 3.01389 8.73958 2.03125 9.55208 1.21875C10.3646 0.40625 11.3472 0 12.5 0V16.6667H10.8333Z" fill="white"/>
              </svg>
              Dine In
            </button>
            <button id="takeAwayBtn" class="take-away-btn flex-1 flex items-center justify-center gap-3 h-11 rounded-[20px] transition-colors font-semibold text-sm tracking-[0.7px] bg-transparent text-[#5F5E5B]">
              <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.66667 16.6667C1.20833 16.6667 0.815972 16.5035 0.489583 16.1771C0.163194 15.8507 0 15.4583 0 15V5C0 4.54167 0.163194 4.14931 0.489583 3.82292C0.815972 3.49653 1.20833 3.33333 1.66667 3.33333H3.33333C3.33333 2.41667 3.65972 1.63194 4.3125 0.979167C4.96528 0.326389 5.75 0 6.66667 0C7.58333 0 8.36806 0.326389 9.02083 0.979167C9.67361 1.63194 10 2.41667 10 3.33333H11.6667C12.125 3.33333 12.5174 3.49653 12.8438 3.82292C13.1701 4.14931 13.3333 4.54167 13.3333 5V15C13.3333 15.4583 13.1701 15.8507 12.8438 16.1771C12.5174 16.5035 12.125 16.6667 11.6667 16.6667H1.66667ZM1.66667 15H11.6667V5H10V6.66667C10 6.90278 9.92014 7.10069 9.76042 7.26042C9.60069 7.42014 9.40278 7.5 9.16667 7.5C8.93056 7.5 8.73264 7.42014 8.57292 7.26042C8.41319 7.10069 8.33333 6.90278 8.33333 6.66667V5H5V6.66667C5 6.90278 4.92014 7.10069 4.76042 7.26042C4.60069 7.42014 4.40278 7.5 4.16667 7.5C3.93056 7.5 3.73264 7.42014 3.57292 7.26042C3.41319 7.10069 3.33333 6.90278 3.33333 6.66667V5H1.66667V15ZM5 3.33333H8.33333C8.33333 2.875 8.17014 2.48264 7.84375 2.15625C7.51736 1.82986 7.125 1.66667 6.66667 1.66667C6.20833 1.66667 5.81597 1.82986 5.48958 2.15625C5.16319 2.48264 5 2.875 5 3.33333Z" fill="currentColor"/>
              </svg>
              Take Away
            </button>
          </div>
        </section>

        <!-- Order Summary Section -->
        <section class="flex flex-col gap-3">
          <h2 class="text-[#570000] text-2xl font-semibold leading-[1.4]">
            Ringkasan Pesanan
          </h2>
          
          <!-- Warung 1 -->
          <div class="warung-card">
            <div class="warung-header">
              <svg width="16" height="14" viewBox="0 0 16 14" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.2852 6.0375V12C14.2852 12.4125 14.1383 12.7656 13.8446 13.0594C13.5508 13.3531 13.1977 13.5 12.7852 13.5H2.2852C1.8727 13.5 1.51958 13.3531 1.22583 13.0594C0.932079 12.7656 0.785204 12.4125 0.785204 12V6.0375C0.497704 5.775 0.275829 5.4375 0.119579 5.025C-0.0366709 4.6125 -0.0397959 4.1625 0.110204 3.675L0.897704 1.125C0.997704 0.8 1.17583 0.53125 1.43208 0.31875C1.68833 0.10625 1.9852 0 2.3227 0H12.7477C13.0852 0 13.379 0.103125 13.629 0.309375C13.879 0.515625 14.0602 0.7875 14.1727 1.125L14.9602 3.675C15.1102 4.1625 15.1071 4.60625 14.9508 5.00625C14.7946 5.40625 14.5727 5.75 14.2852 6.0375ZM9.1852 5.25C9.5227 5.25 9.77895 5.13437 9.95395 4.90312C10.129 4.67188 10.1977 4.4125 10.1602 4.125L9.7477 1.5H8.2852V4.275C8.2852 4.5375 8.3727 4.76562 8.5477 4.95937C8.7227 5.15312 8.9352 5.25 9.1852 5.25ZM5.8102 5.25C6.0977 5.25 6.33208 5.15312 6.51333 4.95937C6.69458 4.76562 6.7852 4.5375 6.7852 4.275V1.5H5.3227L4.9102 4.125C4.8602 4.425 4.92583 4.6875 5.10708 4.9125C5.28833 5.1375 5.5227 5.25 5.8102 5.25ZM2.4727 5.25C2.6977 5.25 2.89458 5.16875 3.06333 5.00625C3.23208 4.84375 3.3352 4.6375 3.3727 4.3875L3.7852 1.5H2.3227L1.5727 4.0125C1.4977 4.2625 1.53833 4.53125 1.69458 4.81875C1.85083 5.10625 2.1102 5.25 2.4727 5.25ZM12.5977 5.25C12.9602 5.25 13.2227 5.10625 13.3852 4.81875C13.5477 4.53125 13.5852 4.2625 13.4977 4.0125L12.7102 1.5H11.2852L11.6977 4.3875C11.7352 4.6375 11.8383 4.84375 12.0071 5.00625C12.1758 5.16875 12.3727 5.25 12.5977 5.25Z" fill="#1A1C1A"/>
              </svg>
              <span class="warung-name">Warung 1</span>
            </div>

            <div class="warung-content">
              <div class="cart-item">
                <div class="item-row">
                  <div class="item-details">
                    <div class="item-header">
                      <span class="item-quantity">1</span>
                      <span class="item-name">Soto Babat</span>
                    </div>
                    <div class="item-note">tambah sambal extra</div>
                    <div class="flex items-center justify-between gap-3">
                      <div class="item-price">Rp 25.000</div>
                      <div class="stepper">
                        <button type="button" class="stepper-btn" onclick="decrementQuantity(this)" aria-label="Kurangi">-</button>
                        <span class="stepper-value">1</span>
                        <button type="button" class="stepper-btn" onclick="incrementQuantity(this)" aria-label="Tambah">+</button>
                      </div>
                    </div>
                  </div>
                  <button type="button" class="delete-btn" onclick="deleteItem(this)" aria-label="Hapus">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <path d="M3 6h18" stroke="#800000" stroke-width="2" stroke-linecap="round"/>
                      <path d="M8 6V4h8v2" stroke="#800000" stroke-width="2" stroke-linejoin="round"/>
                      <path d="M7 6l1 16h8l1-16" stroke="#800000" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </div>
              </div>

              <div class="item-divider"></div>

              <div class="cart-item">
                <div class="item-row">
                  <div class="item-details">
                    <div class="item-header">
                      <span class="item-quantity">2</span>
                      <span class="item-name">Wader Goreng</span>
                    </div>
                    <div class="item-note">-</div>
                    <div class="flex items-center justify-between gap-3">
                      <div class="item-price">Rp 25.000</div>
                      <div class="stepper">
                        <button type="button" class="stepper-btn" onclick="decrementQuantity(this)" aria-label="Kurangi">-</button>
                        <span class="stepper-value">2</span>
                        <button type="button" class="stepper-btn" onclick="incrementQuantity(this)" aria-label="Tambah">+</button>
                      </div>
                    </div>
                  </div>
                  <button type="button" class="delete-btn" onclick="deleteItem(this)" aria-label="Hapus">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <path d="M3 6h18" stroke="#800000" stroke-width="2" stroke-linecap="round"/>
                      <path d="M8 6V4h8v2" stroke="#800000" stroke-width="2" stroke-linejoin="round"/>
                      <path d="M7 6l1 16h8l1-16" stroke="#800000" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="warung-footer">
              <span class="footer-label">Subtotal</span>
              <span class="footer-amount">Rp 75.000</span>
            </div>
          </div>

          <!-- Warung 2 -->
          <div class="warung-card">
            <div class="warung-header">
              <svg width="16" height="14" viewBox="0 0 16 14" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.2852 6.0375V12C14.2852 12.4125 14.1383 12.7656 13.8446 13.0594C13.5508 13.3531 13.1977 13.5 12.7852 13.5H2.2852C1.8727 13.5 1.51958 13.3531 1.22583 13.0594C0.932079 12.7656 0.785204 12.4125 0.785204 12V6.0375C0.497704 5.775 0.275829 5.4375 0.119579 5.025C-0.0366709 4.6125 -0.0397959 4.1625 0.110204 3.675L0.897704 1.125C0.997704 0.8 1.17583 0.53125 1.43208 0.31875C1.68833 0.10625 1.9852 0 2.3227 0H12.7477C13.0852 0 13.379 0.103125 13.629 0.309375C13.879 0.515625 14.0602 0.7875 14.1727 1.125L14.9602 3.675C15.1102 4.1625 15.1071 4.60625 14.9508 5.00625C14.7946 5.40625 14.5727 5.75 14.2852 6.0375ZM9.1852 5.25C9.5227 5.25 9.77895 5.13437 9.95395 4.90312C10.129 4.67188 10.1977 4.4125 10.1602 4.125L9.7477 1.5H8.2852V4.275C8.2852 4.5375 8.3727 4.76562 8.5477 4.95937C8.7227 5.15312 8.9352 5.25 9.1852 5.25ZM5.8102 5.25C6.0977 5.25 6.33208 5.15312 6.51333 4.95937C6.69458 4.76562 6.7852 4.5375 6.7852 4.275V1.5H5.3227L4.9102 4.125C4.8602 4.425 4.92583 4.6875 5.10708 4.9125C5.28833 5.1375 5.5227 5.25 5.8102 5.25ZM12.5977 5.25C12.9602 5.25 13.2227 5.10625 13.3852 4.81875C13.5477 4.53125 13.5852 4.2625 13.4977 4.0125L12.7102 1.5H11.2852L11.6977 4.3875C11.7352 4.6375 11.8383 4.84375 12.0071 5.00625C12.1758 5.16875 12.3727 5.25 12.5977 5.25Z" fill="#1A1C1A"/>
              </svg>
              <span class="warung-name">Warung 2</span>
            </div>

            <div class="warung-content">
              <div class="cart-item">
                <div class="item-row">
                  <div class="item-details">
                    <div class="item-header">
                      <span class="item-quantity">1</span>
                      <span class="item-name">Soto Babat</span>
                    </div>
                    <div class="item-note">Kuah pisah</div>
                    <div class="flex items-center justify-between gap-3">
                      <div class="item-price">Rp 25.000</div>
                      <div class="stepper">
                        <button type="button" class="stepper-btn" onclick="decrementQuantity(this)" aria-label="Kurangi">-</button>
                        <span class="stepper-value">1</span>
                        <button type="button" class="stepper-btn" onclick="incrementQuantity(this)" aria-label="Tambah">+</button>
                      </div>
                    </div>
                  </div>
                  <button type="button" class="delete-btn" onclick="deleteItem(this)" aria-label="Hapus">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <path d="M3 6h18" stroke="#800000" stroke-width="2" stroke-linecap="round"/>
                      <path d="M8 6V4h8v2" stroke="#800000" stroke-width="2" stroke-linejoin="round"/>
                      <path d="M7 6l1 16h8l1-16" stroke="#800000" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="warung-footer">
              <span class="footer-label">Subtotal</span>
              <span class="footer-amount">Rp 25.000</span>
            </div>
          </div>
        </section>

        <!-- Payment Method Section -->
        <section class="flex flex-col gap-3">
          <h2 class="text-[#800000] text-2xl font-semibold leading-[1.4]">
            Metode Pembayaran
          </h2>
          <div class="flex flex-col gap-3">
            <!-- QRIS -->
            <button id="qrisBtn" class="payment-btn flex items-center justify-between p-6 rounded-3xl bg-white shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] border border-[#F5F5F4] opacity-70 transition-all text-left hover:opacity-100">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#E5E2DD] flex items-center justify-center flex-shrink-0">
                  <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 18V16H12V18H10ZM8 16V11H10V16H8ZM16 13V9H18V13H16ZM14 9V7H16V9H14ZM2 11V9H4V11H2ZM0 9V7H2V9H0ZM9 2V0H11V2H9ZM1.5 4.5H4.5V1.5H1.5V4.5ZM0 6V0H6V6H0ZM1.5 16.5H4.5V13.5H1.5V16.5ZM0 18V12H6V18H0ZM13.5 4.5H16.5V1.5H13.5V4.5ZM12 6V0H18V6H12ZM14 18V15H12V13H16V16H18V18H14ZM10 11V9H14V11H10ZM6 11V9H4V7H10V9H8V11H6ZM7 6V2H9V4H11V6H7ZM2.25 3.75V2.25H3.75V3.75H2.25ZM2.25 15.75V14.25H3.75V15.75H2.25ZM14.25 3.75V2.25H15.75V3.75H14.25Z" fill="#5F5E5B"/>
                  </svg>
                </div>
                <div class="flex flex-col gap-0.5">
                  <span class="text-[#1A1C1A] text-sm font-semibold leading-[1.2] tracking-[0.7px]">
                    QRIS
                  </span>
                  <span class="text-[#5F5E5B] text-xs font-normal leading-[1.6]">
                    Konfirmasi instan
                  </span>
                </div>
              </div>
              <div class="w-6 h-6 rounded-full border-2 border-[#E7E5E4] flex items-center justify-center flex-shrink-0 transition-colors" id="qrisRadio">
              </div>
            </button>

            <!-- Bayar di Kasir -->
            <button id="kasirBtn" class="payment-btn flex items-center justify-between p-6 rounded-3xl bg-white shadow-[0_4px_20px_0_rgba(0,0,0,0.04)] border-2 border-[#800000] transition-all text-left">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#800000] flex items-center justify-center flex-shrink-0">
                  <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 9C12.1667 9 11.4583 8.70833 10.875 8.125C10.2917 7.54167 10 6.83333 10 6C10 5.16667 10.2917 4.45833 10.875 3.875C11.4583 3.29167 12.1667 3 13 3C13.8333 3 14.5417 3.29167 15.125 3.875C15.7083 4.45833 16 5.16667 16 6C16 6.83333 15.7083 7.54167 15.125 8.125C14.5417 8.70833 13.8333 9 13 9ZM6 12C5.45 12 4.97917 11.8042 4.5875 11.4125C4.19583 11.0208 4 10.55 4 10V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H20C20.55 0 21.0208 0.195833 21.4125 0.5875C21.8042 0.979167 22 1.45 22 2V10C22 10.55 21.8042 11.0208 21.4125 11.4125C21.0208 11.8042 20.55 12 20 12H6ZM8 10H18C18 9.45 18.1958 8.97917 18.5875 8.5875C18.9792 8.19583 19.45 8 20 8V4C19.45 4 18.9792 3.80417 18.5875 3.4125C18.1958 3.02083 18 2.55 18 2H8C8 2.55 7.80417 3.02083 7.4125 3.4125C7.02083 3.80417 6.55 4 6 4V8C6.55 8 7.02083 8.19583 7.4125 8.5875C7.80417 8.97917 8 9.45 8 10ZM19 16H2C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V3H2V14H19V16Z" fill="white"/>
                  </svg>
                </div>
                <div class="flex flex-col gap-0.5">
                  <span class="text-[#1A1C1A] text-sm font-semibold leading-[1.2] tracking-[0.7px]">
                    Bayar di Kasir
                  </span>
                  <span class="text-[#5F5E5B] text-xs font-normal leading-[1.6]">
                    Bayar di kasir
                  </span>
                </div>
              </div>
              <div class="w-6 h-6 rounded-full border-2 border-[#800000] flex items-center justify-center flex-shrink-0 transition-colors" id="kasirRadio">
                <div class="w-3 h-3 rounded-full bg-[#800000]"></div>
              </div>
            </button>
          </div>
        </section>
</main>

<!-- Bottom CTA -->
<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] z-20 px-4 pb-4">
  <div class="flex flex-col gap-4 bg-[#800000] rounded-3xl px-6 py-5 shadow-[0_-4px_30px_rgba(128,0,0,0.20)]">

    <!-- Total row -->
    <div class="flex items-center justify-between">
      <div class="flex flex-col gap-0.5">
        <span class="text-white/70 text-xs font-medium tracking-[2px] uppercase">TOTAL PEMBAYARAN</span>
        <span class="text-white text-2xl font-bold leading-tight">Rp 100.000</span>
      </div>
      <div class="flex items-center gap-1.5 bg-white/20 rounded-xl px-3 py-2">
        <svg width="14" height="15" viewBox="0 0 14 15" fill="none" aria-hidden="true">
          <path d="M2.25 15C1.625 15 1.09375 14.7812 0.65625 14.3438C0.21875 13.9062 0 13.375 0 12.75V10.5H2.25V0L3.375 1.125L4.5 0L5.625 1.125L6.75 0L7.875 1.125L9 0L10.125 1.125L11.25 0L12.375 1.125L13.5 0V12.75C13.5 13.375 13.2812 13.9062 12.8438 14.3438C12.4062 14.7812 11.875 15 11.25 15H2.25Z" fill="white" opacity="0.9"/>
        </svg>
        <span class="text-white text-sm font-semibold">2 Toko</span>
      </div>
    </div>

    <!-- CTA Button -->
    <button type="button" onclick="window.location.href='./index.php?page=pilih-pembayaran'"
      class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-white shadow-[0_4px_6px_-1px_rgba(0,0,0,0.10)] transition-all hover:opacity-90 active:scale-[0.98]">
      <span class="text-[#800000] text-base font-bold leading-6">Buat Pesanan</span>
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
        <path d="M12.175 9H0V7H12.175L6.575 1.4L8 0L16 8L8 16L6.575 14.6L12.175 9Z" fill="#800000"/>
      </svg>
    </button>

  </div>
</div>
