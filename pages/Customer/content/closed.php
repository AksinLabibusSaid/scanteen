<?php
declare(strict_types=1);

/** @var array $closedStatus */
$reason = $closedStatus['reason'] ?? 'closed';
$message = $closedStatus['message'] ?? 'Maaf, kantin sedang tutup.';
$open = $closedStatus['open'] ?? '';
$close = $closedStatus['close'] ?? '';
?>

<div class="flex flex-col items-center justify-center min-h-[80vh] px-8 text-center">
    <div class="w-24 h-24 rounded-full bg-[#FDE8E4] flex items-center justify-center text-[var(--brand)] mb-8">
        <?php if ($reason === 'maintenance'): ?>
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
        <?php else: ?>
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        <?php endif; ?>
    </div>

    <h1 class="poppins text-2xl font-black text-[var(--text-dark)] mb-4">
        <?= $reason === 'maintenance' ? 'Pemeliharaan Sistem' : 'Kantin Sedang Tutup' ?>
    </h1>

    <p class="text-sm font-medium text-[var(--text-muted)] leading-relaxed mb-8">
        <?php if ($reason === 'maintenance'): ?>
            <?= htmlspecialchars($message) ?>
        <?php elseif ($reason === 'outside_hours'): ?>
            Kantin beroperasi dari jam <strong><?= $open ?></strong> sampai <strong><?= $close ?></strong>. Silakan kembali pada jam operasional.
        <?php else: ?>
            Maaf, kantin tidak beroperasi saat ini.
        <?php endif; ?>
    </p>

    <a href="index.php" class="px-8 py-3 bg-[var(--brand)] text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg">
        Coba Segarkan
    </a>
</div>
