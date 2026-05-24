<?php
// Vložíme hlavičku stránky (HTML <head>, <header> s navigací, otevřený <main>)
require_once __DIR__ . '/../layout/header.php';
?>

<!-- ============================================================
     Záhlaví stránky: nadpis + tlačítko přidat
     ============================================================ -->
<div class="page-header">
    <h2 style="font-size:1.65rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;">
        Vinylová sbírka
    </h2>
    <a class="btn btn-cta btn-lg"
       href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=create">
        + Přidat desku
    </a>
</div>

<?php if (empty($vinyls)): ?>
    <!-- Prázdný stav: databáze neobsahuje žádné záznamy -->
    <div class="empty-state">
        <p>V databázi se zatím nenachází žádné vinylové desky.</p>
        <small>Začněte přidáním prvního záznamu.</small>
    </div>

<?php else: ?>
    <!-- Responsivní mřížka karet – sloupce se přizpůsobí dle šířky okna -->
    <div class="cards-grid">

        <?php
            // Načteme model Vinyl pro použití statické pomocné metody getUploadInfo()
            require_once __DIR__ . '/../../models/Vinyl.php';

            // Iterujeme přes pole $vinyls předané z VinylControlleru::index()
            foreach ($vinyls as $i => $vinyl):
        ?>
            <?php
                // ---------------------------------------------------------
                // Příprava URL obrázku pro tuto kartu
                // album_cover je v DB uložen jako JSON pole názvů souborů
                // ---------------------------------------------------------
                $images    = json_decode($vinyl['album_cover'], true);
                $mainImage = null;

                if (is_array($images) && count($images) > 0) {
                    $mainImage = $images[0];
                } elseif (!empty($vinyl['album_cover'])) {
                    $mainImage = $vinyl['album_cover'];
                }

                // Normalizace: ošetříme hodnoty jako "[]" nebo JSON string uvnitř stringu
                if (is_string($mainImage)) {
                    $mainImage = trim($mainImage);
                    if (strlen($mainImage) > 0 && $mainImage[0] === '[') {
                        $decoded   = json_decode($mainImage, true);
                        $mainImage = (is_array($decoded) && count($decoded) > 0) ? $decoded[0] : null;
                    }
                    if ($mainImage !== null) $mainImage = trim($mainImage);
                    if ($mainImage === '' || $mainImage === '[]') $mainImage = null;
                }

                // Výpočet src URL pro <img> tag
                $imgExists = false;
                if ($mainImage) {
                    if (strpos($mainImage, 'http') === 0) {
                        // Absolutní URL (externí odkaz)
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
                    $imgSrc = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'><rect width='100%25' height='100%25' fill='%231a1510'/><circle cx='200' cy='200' r='160' fill='%23252018'/><circle cx='200' cy='200' r='60' fill='%230f0e0c'/><circle cx='200' cy='200' r='12' fill='%23333'/><text x='50%25' y='73%25' dominant-baseline='middle' text-anchor='middle' fill='%23555' font-family='sans-serif' font-size='18'>No image</text></svg>";
                }

                // Příprava hodnot pro zobrazení v kartě
                $albumName    = htmlspecialchars($vinyl['album_name']);
                $artist       = htmlspecialchars($vinyl['artist']);
                // category_name pochází z LEFT JOIN v Vinyl::getAll() – může být null
                $categoryName = htmlspecialchars($vinyl['category_name'] ?? '');
                $showUrl      = '/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=show&id=' . urlencode($vinyl['id']);
                $editUrl      = '/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=edit&id=' . urlencode($vinyl['id']);
                $deleteUrl    = '/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=delete&id=' . urlencode($vinyl['id']);

                // Zjistíme, zda přihlášený uživatel může záznam editovat/mazat
                $isOwner = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)($vinyl['created_by'] ?? 0);
                $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
                $canEdit = $isOwner || $isAdmin;

                // Zpoždění animace: každá karta nastupuje s offsetem 60ms
                $delay = min($i * 60, 600); // cap na 600ms
            ?>

            <!-- Karta vinylu – staggered cardIn animace přes CSS custom property --i -->
            <article class="vinyl-card" style="--i:<?= $i ?>; animation-delay:<?= $delay ?>ms;">

                <!-- Horní část = odkaz na detail vinylu -->
                <a href="<?= $showUrl ?>" style="display:block; text-decoration:none;">

                    <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
                        <!-- Debug panel: zobrazí URL a existenci souboru při ?debug=1 -->
                        <div style="position:absolute;top:6px;left:6px;background:oklch(14% 0.01 52/90%);color:var(--t2);font-size:10px;padding:6px 8px;border-radius:6px;max-width:200px;word-break:break-all;z-index:10;">
                            <div><?= htmlspecialchars($albumName) ?></div>
                            <div style="color:var(--t3);">URL: <?= $imgSrc ?></div>
                            <div>Exists: <?= $imgExists ? 'yes' : 'no' ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Obrázek alba se square aspect-ratio a hover zoom -->
                    <div class="vinyl-card__image">
                        <img src="<?= $imgSrc ?>" alt="<?= $albumName ?>" loading="lazy">
                        <!-- Gradient overlay s "Zobrazit" pilulkou – viditelné při hoveru -->
                        <div class="vinyl-card__overlay">
                            <span class="vinyl-card__overlay-pill">Zobrazit</span>
                        </div>
                    </div>

                    <!-- Textové informace pod obrázkem -->
                    <div class="vinyl-card__body">
                        <div class="vinyl-card__name"><?= $albumName ?></div>
                        <div class="vinyl-card__artist"><?= $artist ?></div>
                        <?php if ($categoryName): ?>
                            <!-- Kategorie jako malý badge z LEFT JOIN dat -->
                            <span class="vinyl-card__category"><?= $categoryName ?></span>
                        <?php endif; ?>
                    </div>
                </a>

                <?php if ($canEdit): ?>
                    <!-- Akční tlačítka – zobrazí se vlastníkovi nebo adminovi -->
                    <div class="vinyl-card__actions">
                        <a href="<?= $editUrl ?>"
                           class="btn btn-sm <?= $isOwner ? 'btn-primary' : 'btn-admin-edit' ?>">
                            Upravit<?= (!$isOwner && $isAdmin) ? ' (A)' : '' ?>
                        </a>
                        <a href="<?= $deleteUrl ?>"
                           onclick="return confirm('Opravdu chcete tento vinyl smazat?')"
                           class="btn btn-sm <?= $isOwner ? 'btn-ghost' : 'btn-admin-delete' ?>">
                            Smazat<?= (!$isOwner && $isAdmin) ? ' (A)' : '' ?>
                        </a>
                    </div>
                <?php endif; ?>

            </article>

        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
