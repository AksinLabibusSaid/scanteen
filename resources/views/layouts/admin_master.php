<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin', ENT_QUOTES, 'UTF-8') ?> — Scanteen Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #570000;
            --brand-muted: #F7E9E6;
            --brand-soft: #F5E3DF;
            --text-dark: #261816;
            --text-muted: #5A413D;
            --error-red: #BA1A1A;
            --category-badge: #FDE2DE;
            --card-border: #E9ECEF;
            --row-divider: #F1F3F5;
            --success-green: #16A34A;
            --success-bg: #F0FDF4;
        }

        * { font-family: 'Inter', -apple-system, sans-serif; }
        .poppins { font-family: 'Poppins', sans-serif; }

        .active-nav {
            border-left: 4px solid var(--brand);
            background-color: var(--brand-muted);
        }
        .active-nav span { color: var(--brand); font-weight: 600; }
        .active-nav svg path { fill: var(--brand) !important; }

        .content-area {
            background: #FAF7F6;
        }

        .content-area::-webkit-scrollbar        { width: 6px; }
        .content-area::-webkit-scrollbar-track  { background: #f1f5f9; }
        .content-area::-webkit-scrollbar-thumb  { background: #cbd5e1; border-radius: 3px; }

        .card {
            background: white;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .brand-btn {
            background: var(--brand);
            color: white;
            transition: all 0.2s;
        }
        .brand-btn:hover {
            background: #6b0000;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include SCANTEEN_ROOT . '/resources/views/layouts/admin_sidebar.php'; ?>

        <div class="flex flex-col flex-1 min-w-0">
            <!-- Header -->
            <?php include SCANTEEN_ROOT . '/resources/views/layouts/admin_header.php'; ?>

            <!-- Content Area -->
            <main class="content-area flex-1 overflow-y-auto p-6">
                <?= $content ?? 'No content provided' ?>
            </main>
        </div>
    </div>
</body>
</html>
