<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="cs" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>VinyLog - Výuková aplikace</title>
</head>
<body class="bg-[#dad7cd] text-[#344e41] min-h-screen font-sans flex flex-col">

    <header class="bg-[#dad7cd] border-b border-[#a3b18a] shadow-sm">
        <div class="container mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center">
            <h1 class="text-2xl font-bold tracking-tight text-[#1a1c1e] uppercase italic">
                VinyLog
            </h1>
            
            <nav class="mt-4 md:mt-0">
                <ul class="flex items-center space-x-6">
                    <li>
                        <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=index" class="text-[#344e41] hover:text-[#3a5a40] transition-colors font-medium">Seznam vinylů</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li>
                            <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=create" class="bg-[#588157] hover:bg-[#3a5a40] text-white px-4 py-2 rounded-md transition-all shadow-sm border border-[#588157]">
                                + Přidat vinyl
                            </a>
                        </li>
                        <li class="text-[#1a1c1e] text-sm">
                            Ahoj, <span class="text-[#1a1c1e] font-semibold tracking-wide"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        </li>
                        <li>
                            <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=logout" class="text-[#344e41] hover:text-[#3a5a40] transition-colors text-sm uppercase tracking-wider font-medium">
                                Odhlásit
                            </a>
                        </li>

                    <?php else: ?>
                        <li>
                            <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=login" class="text-[#344e41] hover:text-[#3a5a40] transition-colors font-medium">Přihlásit</a>
                        </li>
                        <li>
                            <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=register" class="bg-[#588157] hover:bg-[#3a5a40] text-white px-4 py-2 rounded-md transition-all shadow-sm border border-[#588157]">
                                Registrace
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container mx-auto px-6 pt-8">
        <?php if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])): ?>
            <div class="space-y-3">
                <?php foreach ($_SESSION['messages'] as $type => $messages): ?>
                    <?php 
                        $styles = [
                            'success' => 'bg-[#F5EBE0] border-[#E3D5CA] text-[#1a1c1e]',
                            'error'   => 'bg-[#F5EBE0] border-[#E3D5CA] text-[#1a1c1e]',
                            'notice'  => 'bg-[#F5EBE0] border-[#E3D5CA] text-[#1a1c1e]',
                        ];
                        $style = $styles[$type] ?? 'bg-[#F5EBE0] border-[#E3D5CA] text-[#1a1c1e]';
                    ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="<?= $style ?> border-l-4 p-4 rounded-r-lg shadow-sm">
                            <p class="font-semibold text-sm italic"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <?php unset($_SESSION['messages']); ?>
            </div>
        <?php endif; ?>
    </div>
