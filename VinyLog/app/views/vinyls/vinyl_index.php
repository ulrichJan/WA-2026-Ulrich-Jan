<?php
// Vložíme hlavičku stránky (HTML <head>, <header> s navigací, otevřený <main>)
require_once __DIR__ . '/../layout/header.php';
?>

    <!-- Nadpis stránky a tlačítko pro přidání nové desky -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
        <h2 class="text-4xl font-semibold tracking-tight text-[#344e41]">Dostupné vinylové desky</h2>
        <a class="bg-[#588157] hover:bg-[#3a5a40] text-white text-lg px-8 py-4 rounded-2xl font-medium shadow transition-all duration-200 border border-[#588157]"
           href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=create">
            + Přidat desku
        </a>
    </div>

    <div>
        <?php if (empty($vinyls)): ?>
            <!-- Prázdný stav: databáze neobsahuje žádné záznamy -->
            <div class="p-12 text-center text-[#344e41]">
                <p class="text-lg italic">V databázi se zatím nenachází žádné vinylové desky.</p>
                <p class="text-sm mt-2">Začněte přidáním prvního záznamu.</p>
            </div>

        <?php else: ?>
            <!-- Responsivní mřížka karet: 1 sloupec → 2 → 3 → 4 dle šířky okna -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">

                <?php
                    // Načteme model Vinyl pro použití statické pomocné metody getUploadInfo()
                    require_once __DIR__ . '/../../models/Vinyl.php';

                    // Iterujeme přes pole $vinyls předané z VinylControlleru::index()
                    foreach ($vinyls as $vinyl):
                ?>
                    <?php
                        // ---------------------------------------------------------
                        // Příprava URL obrázku pro tuto kartu
                        // album_cover je v DB uložen jako JSON pole názvů souborů
                        // ---------------------------------------------------------

                        // Dekódujeme JSON řetězec na PHP pole
                        $images    = json_decode($vinyl['album_cover'], true);
                        $mainImage = null;

                        if (is_array($images) && count($images) > 0) {
                            // Použijeme první obrázek jako hlavní (thumbnail)
                            $mainImage = $images[0];
                        } elseif (!empty($vinyl['album_cover'])) {
                            // Fallback: album_cover nebylo JSON polem, použijeme hodnotu přímo
                            $mainImage = $vinyl['album_cover'];
                        }

                        // Normalizace: ošetříme hodnoty jako "[]" nebo JSON string uvnitř stringu
                        if (is_string($mainImage)) {
                            $mainImage = trim($mainImage);
                            if (strlen($mainImage) > 0 && $mainImage[0] === '[') {
                                // Vnořený JSON string – dekódujeme znovu
                                $decoded = json_decode($mainImage, true);
                                $mainImage = (is_array($decoded) && count($decoded) > 0) ? $decoded[0] : null;
                            }
                            if ($mainImage !== null) $mainImage = trim($mainImage);
                            if ($mainImage === '' || $mainImage === '[]') $mainImage = null;
                        }

                        // Výpočet src URL pro <img> tag
                        $imgExists = false;
                        if ($mainImage) {
                            if (strpos($mainImage, 'http') === 0) {
                                // Absolutní URL (externí odkaz) – použijeme přímo
                                $imgSrc    = htmlspecialchars($mainImage);
                                $imgExists = true;
                            } else {
                                // Lokální soubor – použijeme helper pro výpočet správné URL
                                $info      = Vinyl::getUploadInfo(basename($mainImage));
                                $imgSrc    = htmlspecialchars($info['url']);
                                $imgExists = $info['exists'];
                            }
                        } else {
                            // Placeholder SVG pokud vinyl nemá obrázek
                            $imgSrc = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 400'><rect width='100%25' height='100%25' fill='%23ffffff'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23344e41' font-family='Arial, sans-serif' font-size='24'>No image</text></svg>";
                        }

                        // Příprava hodnot pro zobrazení v kartě
                        $albumName    = htmlspecialchars($vinyl['album_name']);
                        $artist       = htmlspecialchars($vinyl['artist']);
                        // category_name pochází z LEFT JOIN v Vinyl::getAll() – může být null
                        $categoryName = htmlspecialchars($vinyl['category_name'] ?? 'Nezařazeno');
                        $showUrl      = '?action=show&id=' . urlencode($vinyl['id']);

                        // Zjistíme, zda přihlášený uživatel může záznám editovat/mazat
                        $isOwner = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)($vinyl['created_by'] ?? 0);
                        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
                        $canEdit = $isOwner || $isAdmin; // vlastník NEBO admin
                    ?>

                    <!-- Karta vinylu
                         Hover efekt: jemný border highlight + lehký tón pozadí odpovídající
                         barvám stránky (bez scale/zoom transformace pro klidnější vizuál).
                         "group" třída umožňuje reagovat na hover celé karty uvnitř potomků. -->
                    <article class="bg-white rounded-2xl border border-[#e8e3dc] hover:border-[#a3b18a] hover:bg-[#f7f5f2] shadow-sm hover:shadow-md group transition-all duration-200 overflow-hidden">

                        <!-- Odkaz na detail vinylu (celá horní část karty je klikatelná) -->
                        <a href="<?= $showUrl ?>" class="block relative no-underline">

                            <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
                                <!-- Debug panel: zobrazí URL a existenci souboru při ?debug=1 -->
                                <div class="absolute top-2 left-2 bg-white/90 text-xs text-[#344e41] p-2 rounded shadow max-w-xs overflow-hidden break-words z-50">
                                    <div class="font-medium"><?= htmlspecialchars($albumName) ?></div>
                                    <div class="text-xs">URL: <?= $imgSrc ?></div>
                                    <div class="text-xs">Exists: <?= $imgExists ? 'yes' : 'no' ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Obrázek alba – výška 64 (256px), object-cover zachová poměr stran -->
                            <div class="w-full h-64 bg-gray-100 overflow-hidden rounded-t-2xl">
                                <img src="<?= $imgSrc ?>" alt="<?= $albumName ?>" class="w-full h-full object-cover">
                            </div>

                            <!-- Textové informace o vinylu pod obrázkem -->
                            <div class="p-4">
                                <h3 class="text-xl font-semibold text-[#344e41] truncate"><?= $albumName ?></h3>
                                <p class="text-base text-[#3a5a40] mt-1 truncate"><?= $artist ?></p>
                                <!-- Kategorie z LEFT JOIN – zelená barva odlišuje od jména umělce -->
                                <p class="text-sm text-[#588157] mt-1 truncate"><?= $categoryName ?></p>

                                <?php if ($canEdit): ?>
                                    <!-- Tlačítka Upravit / Smazat – vidí pouze vlastník nebo admin.
                                         Odlišené barevně: vlastník = standardní barvy, admin = tmavší (vizuální hint) -->
                                    <div class="flex gap-2 mt-3">
                                        <a href="?action=edit&id=<?= urlencode($vinyl['id']) ?>"
                                           class="text-xs font-medium px-3 py-1 rounded-lg border
                                                  <?= $isOwner ? 'bg-[#588157] text-white border-[#588157]' : 'bg-[#344e41] text-white border-[#344e41]' ?>
                                                  hover:opacity-80 transition-opacity">
                                            Upravit<?= (!$isOwner && $isAdmin) ? ' (admin)' : '' ?>
                                        </a>
                                        <a href="?action=delete&id=<?= urlencode($vinyl['id']) ?>"
                                           onclick="return confirm('Opravdu chcete tento vinyl smazat?')"
                                           class="text-xs font-medium px-3 py-1 rounded-lg border
                                                  <?= $isOwner ? 'bg-[#e6e6e6] text-[#344e41] border-[#e6e6e6]' : 'bg-[#c1121f] text-white border-[#c1121f]' ?>
                                                  hover:opacity-80 transition-opacity">
                                            Smazat<?= (!$isOwner && $isAdmin) ? ' (admin)' : '' ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- "Zobrazit" badge – objeví se nad kartou při hoveru (group-hover) -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-[rgba(88,129,87,0.04)] pointer-events-none">
                                <span class="px-3 py-1 bg-[#588157] text-white rounded-full text-sm font-medium">Zobrazit</span>
                            </div>

                        </a>
                    </article>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
