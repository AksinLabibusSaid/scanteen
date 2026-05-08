<!-- Receipt Container -->
<main class="flex-1 flex flex-col items-center px-6 pt-6 pb-20 bg-[#F9FAFB]">
    <div class="w-full max-w-[380px] bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100 flex flex-col">
        
        <!-- Header -->
        <div class="flex flex-col items-center pt-10 pb-6 px-8 text-center">
            <div class="w-16 h-16 mb-4">
                <img src="https://api.builder.io/api/v1/image/assets/TEMP/7c63d6d2f2848f53c163ee7f930ee5fd70edd4d6?width=80" alt="Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-[#800000] text-sm font-bold tracking-[0.1em] uppercase">CulinaryReceipts</h1>
            <p class="text-[#6B7280] text-[10px] font-medium tracking-[0.05em] uppercase mt-1">Smart Canteen Ecosystem</p>
        </div>

        <!-- Dotted Divider -->
        <div class="px-8">
            <div class="border-t-2 border-dashed border-gray-200"></div>
        </div>

        <!-- Customer Info -->
        <div class="px-8 py-6 flex flex-col gap-3">
            <h2 class="text-[#800000] text-[11px] font-bold tracking-wider uppercase">Customer Info</h2>
            <div class="flex justify-between items-baseline">
                <span class="text-[#6B7280] text-[11px] uppercase font-bold">Name</span>
                <span class="text-[#1A1C1A] text-sm font-bold">Jonathan Doe</span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-[#6B7280] text-[11px] uppercase font-bold">Email</span>
                <span class="text-[#1A1C1A] text-sm font-semibold">j.doe@example.com</span>
            </div>
        </div>

        <!-- Order Details -->
        <div class="px-8 py-4 flex flex-col gap-3">
            <h2 class="text-[#800000] text-[11px] font-bold tracking-wider uppercase">Order Details</h2>
            <div class="flex justify-between items-baseline">
                <span class="text-[#6B7280] text-[11px] uppercase font-bold">Order ID</span>
                <span class="text-[#1A1C1A] text-sm font-black">#ORD-772-CNTN</span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-[#6B7280] text-[11px] uppercase font-bold">Date</span>
                <span class="text-[#1A1C1A] text-[13px] font-medium">Oct 24, 2023 12:45 PM</span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-[#6B7280] text-[11px] uppercase font-bold">Location</span>
                <span class="text-[#1A1C1A] text-[13px] font-medium">Meja 12</span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-[#6B7280] text-[11px] uppercase font-bold">Order Type</span>
                <span class="bg-[#FEF2F2] text-[#800000] text-[10px] font-bold px-2 py-0.5 rounded border border-[#FEE2E2] uppercase">Dine-in</span>
            </div>
            <div class="flex justify-between items-baseline">
                <span class="text-[#6B7280] text-[11px] uppercase font-bold">Payment Method</span>
                <span class="text-[#1A1C1A] text-[13px] font-bold uppercase">QRIS</span>
            </div>
        </div>

        <!-- Dotted Divider -->
        <div class="px-8 py-2">
            <div class="border-t-2 border-dashed border-gray-200"></div>
        </div>

        <!-- Ordered Items -->
        <div class="px-8 py-4 flex flex-col gap-5">
            <h2 class="text-[#800000] text-[11px] font-bold tracking-wider uppercase border-b border-gray-100 pb-2">Ordered Items</h2>
            
            <!-- Warung 1 -->
            <div class="flex flex-col gap-4">
                <h3 class="text-[#800000] text-[13px] font-bold">Warung 1</h3>
                
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between font-bold text-sm text-[#1A1C1A]">
                        <span>Soto Babat</span>
                        <span>Rp 25.000</span>
                    </div>
                    <div class="text-[11px] text-[#6B7280] flex flex-col">
                        <span>1x Rp 25.000</span>
                        <span class="italic mt-0.5 text-[#B22B1D]">Extra Sambal Ijo</span>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <div class="flex justify-between font-bold text-sm text-[#1A1C1A]">
                        <span>Wader Goreng</span>
                        <span>Rp 50.000</span>
                    </div>
                    <div class="text-[11px] text-[#6B7280] flex flex-col">
                        <span>2x Rp 25.000</span>
                        <span class="mt-0.5">-</span>
                    </div>
                </div>
            </div>

            <!-- Warung 2 -->
            <div class="flex flex-col gap-4">
                <h3 class="text-[#800000] text-[13px] font-bold">Warung 2</h3>
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between font-bold text-sm text-[#1A1C1A]">
                        <span>Rawon Jumbo</span>
                        <span>Rp 25.000</span>
                    </div>
                    <div class="text-[11px] text-[#6B7280] flex flex-col">
                        <span>1x Rp 25.000</span>
                        <span class="italic mt-0.5 text-[#B22B1D]">Kuah pisah</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dotted Divider -->
        <div class="px-8 py-2">
            <div class="border-t-2 border-dashed border-gray-200"></div>
        </div>

        <!-- Totals -->
        <div class="px-8 py-6 flex flex-col gap-2 bg-[#FAF9F6]/50">
            <div class="flex justify-between text-[13px] text-[#5F5E5B] font-medium">
                <span>Subtotal</span>
                <span>Rp 100.000</span>
            </div>
            <div class="flex justify-between text-[13px] text-[#5F5E5B] font-medium">
                <span>Service Tax (10%)</span>
                <span>Rp 9.000</span>
            </div>
            <div class="flex justify-between text-base text-[#800000] font-black mt-2 pt-2 border-t border-gray-200/50">
                <span class="uppercase tracking-wider">Total</span>
                <span>Rp 109.000</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex flex-col items-center px-8 py-10 text-center gap-6">
            <p class="text-[11px] text-[#6B7280] font-medium">Thank you for dining with us!</p>
            
            <div class="p-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                <img src="https://api.builder.io/api/v1/image/assets/TEMP/a52bb22c7583c816363d4ce1630f1ce1e49ff9f3?width=128" alt="QR Code" class="w-32 h-32 object-contain grayscale contrast-125">
            </div>

            <h3 class="text-[#800000] text-[10px] font-bold tracking-[0.2em] uppercase">Scan to Track Order</h3>
            
            <div class="mt-4 pt-6 border-t border-gray-100 w-full">
                <p class="text-[9px] text-gray-400 font-bold tracking-widest uppercase">Powered by Smart Canteen S.A.</p>
            </div>
        </div>
    </div>

    <!-- Floating Actions (Print/Download) -->
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 flex gap-3 z-30">
        <button onclick="window.print()" class="bg-[#800000] text-white px-6 py-3 rounded-full shadow-lg font-bold flex items-center gap-2 hover:bg-[#6a0000] transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Struk
        </button>
    </div>
</main>

<style>
@media print {
    body * { visibility: hidden; background: white !important; }
    main, main * { visibility: visible; }
    main { position: absolute; left: 0; top: 0; width: 100%; padding: 0 !important; }
    .fixed { display: none !important; }
    header { display: none !important; }
}
</style>
