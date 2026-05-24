<?php
// Spuštění session pokud ještě není aktivní.
// session_status() === PHP_SESSION_NONE znamená, že session zatím neběží.
// Kontrola zabraňuje chybě "Session already started" pokud je header.php vložen vícekrát.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="cs" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Fonts: Monoton – použito pro velké logo VinyLog v navigaci -->
    <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
    <!-- Tailwind CSS přes CDN – utility-first CSS framework pro rychlé stylování -->
    <script src="https://cdn.tailwindcss.com"></script>
    <title>VinyLog</title>
</head>
<body class="bg-[#dad7cd] text-[#344e41] min-h-screen font-sans flex flex-col">

    <!-- =====================================================================
         Hlavička aplikace (sticky = přilepena k hornímu okraji při scrollování)
         ===================================================================== -->
    <header class="sticky top-0 z-40 bg-[#dad7cd] border-b border-[#a3b18a]">
        <div class="max-w-7xl mx-auto px-10 py-4 flex items-center justify-start gap-12">

            <!-- Logo VinyLog – odkaz na seznam desek -->
            <div class="flex items-start gap-3">
                <a href="?action=index" class="text-[#344e41] hover:text-[#3a5a40] no-underline">
                    <h1 class="text-5xl leading-none font-normal" style="font-family: 'Monoton', cursive;">VinyLog</h1>
                </a>
            </div>

            <!-- Hlavní navigační menu (skryto na mobilech, zobrazeno od md breakpointu) -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="?action=index"  class="text-xl text-[#344e41] hover:text-[#3a5a40] transition-colors font-medium">Desky</a>
                <a href="?action=create" class="text-xl text-[#344e41] hover:text-[#3a5a40] transition-colors font-medium">Přidat desku</a>
            </nav>

            <!-- Pravá část navigace: info o uživateli + tlačítko přihlášení/odhlášení -->
            <div class="ml-auto flex items-center gap-6">
                <div class="flex items-center gap-4">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Přihlášený uživatel: zobrazíme jeho jméno a případně Admin štítek -->
                        <span class="text-lg text-[#344e41] font-medium">
                            <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                                <!-- Admin badge: zobrazí se pouze uživatelům s is_admin = 1 -->
                                <span class="ml-2 text-xs font-semibold uppercase tracking-wide bg-[#344e41] text-[#dad7cd] px-2 py-0.5 rounded">Admin</span>
                            <?php endif; ?>
                        </span>
                        <!-- Odkaz na odhlášení -->
                        <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=logout"
                           class="text-lg text-[#344e41] hover:text-[#3a5a40]">Odhlásit</a>
                    <?php else: ?>
                        <!-- Nepřihlášený uživatel: odkaz na přihlašovací stránku -->
                        <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=login"
                           class="text-lg text-[#344e41] hover:text-[#3a5a40]">Přihlásit</a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </header>

    <!-- =====================================================================
         Hlavní obsah stránky (flex-grow zajistí, že footer bude vždy dole)
         ===================================================================== -->
    <main class="max-w-7xl mx-auto px-8 py-10 flex-grow">

        <!-- Flash zprávy (notifikace) – zobrazí se po přesměrování z controlleru.
             Zprávy jsou uloženy v $_SESSION['messages'] jako víceúrovňové pole.
             Třída PHP: AuthController / VinylController → addSuccessMessage() atd.
             Po zobrazení jsou okamžitě vymazány (unset). -->
        <?php if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])): ?>
            <div class="space-y-3 mb-6">
                <?php foreach ($_SESSION['messages'] as $type => $messages): ?>
                    <?php
                        // Každý typ zprávy má jiné vizuální zabarvení levého borderu
                        $styles = [
                            'success' => 'bg-white border-l-4 border-[#588157] text-[#1a1c1e]', // zelená = úspěch
                            'error'   => 'bg-white border-l-4 border-[#e06b6b] text-[#1a1c1e]', // červená = chyba
                            'notice'  => 'bg-white border-l-4 border-[#a3b18a] text-[#1a1c1e]', // šedá = info
                        ];
                        $style = $styles[$type] ?? 'bg-white border-l-4 border-[#a3b18a] text-[#1a1c1e]';
                    ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="<?= $style ?> p-4 rounded-lg shadow-sm">
                            <p class="font-medium text-sm"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <?php unset($_SESSION['messages']); // Zprávy zobrazit jen jednou – ihned vymažeme ?>
            </div>
        <?php endif; ?>

<!-- Obsah jednotlivých stránek se vloží sem (vinyl_index.php, vinyl_show.php atd.) -->
