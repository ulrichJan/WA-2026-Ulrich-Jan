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
                <ul class="flex space-x-4 items-center">
                    <li>
                        <a href="BookController.php?action=index" class="text-[#1a1c1e] hover:text-[#c1121f] transition-colors font-medium">Seznam knih</a>
                    </li>
                    <li>
                        <a href="../views/books/book_create.php" class="bg-[#c1121f] hover:bg-[#930f1b] text-[#fdf0d5] px-4 py-2 rounded-md transition-all shadow-sm border border-[#c1121f]">+ Přidat knihu</a>
                    </li>
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
