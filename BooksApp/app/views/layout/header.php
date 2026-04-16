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
    <title>Knihovna - Výuková aplikace</title>
</head>
<body class="bg-[#fdf0d5] text-[#1a1c1e] min-h-screen font-sans flex flex-col">

    <header class="bg-[#fdf0d5] border-b border-[#c1121f]/20 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center">
            <h1 class="text-2xl font-bold tracking-tight text-[#c1121f] uppercase italic">
                Aplikace <span class="text-[#c1121f]">Knihovna</span>
            </h1>
            
            <nav class="mt-4 md:mt-0">
                <ul class="flex items-center space-x-6">
                    <li>
                        <a href="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/BookController.php?action=index" class="hover:text-blue-400 transition-colors font-medium">Seznam knih</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li>
                            <a href="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/BookController.php?action=create" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-md transition-all shadow-inner border border-blue-500">
                                + Přidat knihu
                            </a>
                        </li>
                        <li class="text-slate-400 text-sm">
                            Ahoj, <span class="text-white font-semibold tracking-wide"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        </li>
                        <li>
                            <a href="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/AuthController.php?action=logout" class="text-rose-400 hover:text-white transition-colors text-sm uppercase tracking-wider font-medium">
                                Odhlásit
                            </a>
                        </li>

                    <?php else: ?>
                        <li>
                            <a href="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/AuthController.php?action=login" class="hover:text-blue-400 transition-colors font-medium">Přihlásit</a>
                        </li>
                        <li>
                            <a href="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/AuthController.php?action=register" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-md transition-all shadow-inner border border-slate-600">
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
                            'success' => 'bg-emerald-50 border-emerald-300 text-emerald-700',
                            'error'   => 'bg-[#fdecea] border-[#f1b0aa] text-[#c1121f]',
                            'notice'  => 'bg-amber-50 border-amber-300 text-amber-700',
                        ];
                        $style = $styles[$type] ?? 'bg-white border-slate-200 text-slate-800';
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
