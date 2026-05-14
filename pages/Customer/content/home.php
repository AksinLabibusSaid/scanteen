<?php
declare(strict_types=1);

use App\Customer\OrderUi;

if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === basename(__FILE__)) {
    header('Location: ../index.php?page=home');
    exit;
}

$bannerOrder = $activeOrderForBanner ?? null;
?>

<!-- Table Banner -->
            <section class="mx-4 mt-4 rounded-2xl overflow-hidden shadow-lg">
                <div class="relative h-[214px]" style="background: url('https://api.builder.io/api/v1/image/assets/TEMP/8395f3e1d198f26b28eedeb8ed8ce22cbb79f650?width=796') lightgray 0px -92.51px / 100% 186.458% no-repeat;">
                    <div class="absolute inset-0" style="background: linear-gradient(90deg, rgba(128,0,0,0.95) 0%, rgba(128,0,0,0.80) 50%, rgba(128,0,0,0.00) 100%); width: calc(100% - 40px);"></div>
                    <div class="absolute left-8 top-7">
                        <div class="inline-flex px-3 py-1 rounded-lg" style="background-color: rgba(255, 255, 255, 0.2);">
                            <span class="text-white text-base">Selamat Datang</span>
                        </div>
                    </div>
                    <div class="absolute left-8 top-[71px]">
                        <span class="text-white text-[30px] font-normal leading-9">Kamu di Meja <?php echo htmlspecialchars($customerContext->tableNumber, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="absolute left-8 top-[107px] max-w-[294px]">
                        <p class="text-[#FFD4CD] text-base leading-6">Nikmati kemudahan memesan makanan favoritmu langsung dari meja ini.</p>
                    </div>
                </div>
            </section>

            <!-- Active Order Status -->
            <?php if ($bannerOrder !== null) {
                $bst = (string) $bannerOrder['status'];
                $detailPage = $bst === 'pending_payment' ? 'status-belum-bayar' : 'status-sudah-bayar';
                $detailHref = './index.php?page=' . $detailPage . '&o=' . rawurlencode((string) $bannerOrder['public_token']);
                ?>
            <div class="mx-4 mt-4 flex items-center justify-between px-4 py-4 rounded-2xl border shadow-sm" style="border-color: #E2BFB9; background-color: rgba(255, 240, 238, 0.5);">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-[39px] h-12 rounded-xl flex-shrink-0" style="background-color: #FFDAD4;">
                        <svg width="21" height="24" viewBox="0 0 21 24" fill="none">
                            <path d="M3.5 23.3333C2.52778 23.3333 1.70139 22.9931 1.02083 22.3125C0.340278 21.6319 0 20.8056 0 19.8333V16.3333H3.5V0L5.25 1.75L7 0L8.75 1.75L10.5 0L12.25 1.75L14 0L15.75 1.75L17.5 0L19.25 1.75L21 0V19.8333C21 20.8056 20.6597 21.6319 19.9792 22.3125C19.2986 22.9931 18.4722 23.3333 17.5 23.3333H3.5ZM17.5 21H14V18.6667H16.3333V3.5H5.83333V16.3333H14V18.6667H2.33333V19.8333C2.33333 20.1639 2.44514 20.441 2.66875 20.6646C2.89236 20.8882 3.16944 21 3.5 21H14ZM7 8.16667V5.83333H14V8.16667H7ZM7 11.6667V9.33333H14V11.6667H7ZM16.3333 8.16667C16.0028 8.16667 15.7257 8.05486 15.5021 7.83125C15.2785 7.60764 15.1667 7.33056 15.1667 7C15.1667 6.66944 15.2785 6.39236 15.5021 6.16875C15.7257 5.94514 16.0028 5.83333 16.3333 5.83333C16.6639 5.83333 16.941 5.94514 17.1646 6.16875C17.3882 6.39236 17.5 6.66944 17.5 7C17.5 7.33056 17.3882 7.60764 17.1646 7.83125C16.941 8.05486 16.6639 8.16667 16.3333 8.16667ZM16.3333 11.6667C16.0028 11.6667 15.7257 11.5549 15.5021 11.3313C15.2785 11.1076 15.1667 10.8306 15.1667 10.5C15.1667 10.1694 15.2785 9.89236 15.5021 9.66875C15.7257 9.44514 16.0028 9.33333 16.3333 9.33333C16.6639 9.33333 16.941 9.44514 17.1646 9.66875C17.3882 9.89236 17.5 10.1694 17.5 10.5C17.5 10.8306 17.3882 11.1076 17.1646 11.3313C16.941 11.5549 16.6639 11.6667 16.3333 11.6667ZM2.33333 21C2.33333 21 2.33333 20.8882 2.33333 20.6646C2.33333 20.441 2.33333 20.1639 2.33333 19.8333V18.6667V21Z" fill="#570000"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[#261816] text-base">Order <?php echo htmlspecialchars((string) $bannerOrder['order_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background-color: #B30B0B;"></span>
                            <span class="text-[#261816] text-base font-medium"><?php echo htmlspecialchars(OrderUi::statusLabel($bst), ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                </div>
                <a href="<?php echo htmlspecialchars($detailHref, ENT_QUOTES, 'UTF-8'); ?>" class="px-6 py-2 rounded-xl border bg-white inline-block" style="color: #570000; border-color: #8E706C;">
                    Lihat
                </a>
            </div>
            <?php } ?>

            <!-- Search Bar -->
            <div class="mx-4 mt-4">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px]" viewBox="0 0 18 18" fill="none">
                        <path d="M16.6 18L10.3 11.7C9.8 12.1 9.225 12.4167 8.575 12.65C7.925 12.8833 7.23333 13 6.5 13C4.68333 13 3.14583 12.3708 1.8875 11.1125C0.629167 9.85417 0 8.31667 0 6.5C0 4.68333 0.629167 3.14583 1.8875 1.8875C3.14583 0.629167 4.68333 0 6.5 0C8.31667 0 9.85417 0.629167 11.1125 1.8875C12.3708 3.14583 13 4.68333 13 6.5C13 7.23333 12.8833 7.925 12.65 8.575C12.4167 9.225 12.1 9.8 11.7 10.3L18 16.6L16.6 18ZM6.5 11C7.75 11 8.8125 10.5625 9.6875 9.6875C10.5625 8.8125 11 7.75 11 6.5C11 5.25 10.5625 4.1875 9.6875 3.3125C8.8125 2.4375 7.75 2 6.5 2C5.25 2 4.1875 2.4375 3.3125 3.3125C2.4375 4.1875 2 5.25 2 6.5C2 7.75 2.4375 8.8125 3.3125 9.6875C4.1875 10.5625 5.25 11 6.5 11Z" fill="#9CA3AF"/>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Cari menu favoritmu..." class="w-full h-12 pl-12 pr-4 rounded-xl border border-gray-200 bg-white text-base text-gray-500 placeholder-gray-400 focus:outline-none" style="color: #6B7280;">
                </div>
            </div>

            <!-- Stall Filter Tabs -->
            <div class="mx-4 mt-4">
                <p class="text-gray-500 text-base mb-3">Pilih Warung</p>
                <div class="overflow-x-auto scrollbar-hide -mx-0">
                    <div class="flex gap-3 pb-2" id="warungTabs">
                        <!-- Tabs will be generated by JS -->
                    </div>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="mx-4 mt-3 border-b border-gray-200">
                <div class="flex gap-8 overflow-x-auto scrollbar-hide" id="categoryTabs">
                    <!-- Tabs will be generated by JS -->
                </div>
            </div>

            <!-- Menu Grid -->
            <div class="mx-4 mt-4 grid grid-cols-2 gap-4" id="menuGrid">
                <!-- Menu items will be generated by JS -->
            </div>

            <!-- Page Numbers -->
            <div class="flex justify-center mt-6 mb-4 gap-3 pb-24">
                <button class="w-[25px] h-[25px] rounded-[5px] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] font-kanit font-medium text-[17px] flex items-center justify-center" style="color: #8B2424;">1</button>
                <button class="text-[17px] font-kanit font-medium text-black">2</button>
            </div>

            <!-- Floating Cart Bar -->
            <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] px-4 pb-4 z-50 hidden" id="cartBar">
                <div class="flex items-center justify-between px-6 py-4 rounded-2xl bg-maroon shadow-[0_10px_30px_rgba(128,0,0,0.3)]">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <svg width="26" height="23" viewBox="0 0 26 23" fill="none">
                                <path d="M5.26503 22.1667C4.74003 22.1667 4.27336 22.0063 3.86503 21.6854C3.45669 21.3646 3.17475 20.9514 3.01919 20.4458L0.0441919 9.65417C-0.0530303 9.28472 0.0101641 8.94444 0.233775 8.63333C0.457386 8.32222 0.763636 8.16667 1.15253 8.16667H6.69419L11.8275 0.525C11.9247 0.369444 12.0609 0.243056 12.2359 0.145833C12.4109 0.0486111 12.5956 0 12.79 0C12.9845 0 13.1692 0.0486111 13.3442 0.145833C13.5192 0.243056 13.6553 0.369444 13.7525 0.525L18.8859 8.16667H24.4859C24.8747 8.16667 25.181 8.32222 25.4046 8.63333C25.6282 8.94444 25.6914 9.28472 25.5942 9.65417L22.6192 20.4458C22.4636 20.9514 22.1817 21.3646 21.7734 21.6854C21.365 22.0063 20.8984 22.1667 20.3734 22.1667H5.26503ZM5.23586 19.8333H20.4025L22.9692 10.5H2.66919L5.23586 19.8333ZM12.8192 17.5C13.4609 17.5 14.0102 17.2715 14.4671 16.8146C14.9241 16.3576 15.1525 15.8083 15.1525 15.1667C15.1525 14.525 14.9241 13.9757 14.4671 13.5188C14.0102 13.0618 13.4609 12.8333 12.8192 12.8333C12.1775 12.8333 11.6282 13.0618 11.1713 13.5188C10.7143 13.9757 10.4859 14.525 10.4859 15.1667C10.4859 15.8083 10.7143 16.3576 11.1713 16.8146C11.6282 17.2715 12.1775 17.5 12.8192 17.5ZM9.52336 8.16667H16.0859L12.79 3.26667L9.52336 8.16667Z" fill="white"/>
                            </svg>
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-white text-maroon text-[10px] font-bold flex items-center justify-center leading-none" id="cartCount">0</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold tracking-[1.4px] uppercase" style="opacity: 0.8;" id="cartItemsLabel">0 ITEMS</p>
                            <p class="text-white text-lg font-bold leading-7" id="cartTotal">Rp 0</p>
                        </div>
                    </div>
                    <button class="flex items-center gap-2 px-6 py-3 rounded-xl" style="background-color: rgba(255, 240, 238, 0.3);" onclick="window.location.href='./index.php?page=keranjang'">
                        <span class="text-base font-bold text-white">Check out</span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M12.175 9H0V7H12.175L6.575 1.4L8 0L16 8L8 16L6.575 14.6L12.175 9Z" fill="white"/>
                        </svg>
                    </button>
                </div>
            </div>

