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

    <?php
        // Extrahujeme unikátní kategorie z načtených vinylů – bez extra DB dotazu
        $uniqueCategories = [];
        foreach ($vinyls as $v) {
            $cat = $v['category_name'] ?? '';
            if ($cat !== '' && !in_array($cat, $uniqueCategories)) {
                $uniqueCategories[] = $cat;
            }
        }
        sort($uniqueCategories);
    ?>

    <!-- ============================================================
         Filtrační lišta: fulltextové hledání + kategorie + řazení
         ============================================================ -->
    <div class="filter-bar">
        <!-- Textové vyhledávání -->
        <input type="search" id="vl-search" class="filter-input"
               placeholder="Hledat podle názvu nebo umělce…" autocomplete="off">

        <!-- Filtr kategorie -->
        <select id="vl-category" class="filter-select">
            <option value="">Všechny kategorie</option>
            <?php foreach ($uniqueCategories as $cat): ?>
                <option value="<?= htmlspecialchars(mb_strtolower($cat)) ?>">
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Řazení -->
        <select id="vl-sort" class="filter-select">
            <option value="default">Nejnovější</option>
            <option value="name">Název A–Z</option>
            <option value="year-desc">Rok (nejnovější)</option>
            <option value="year-asc">Rok (nejstarší)</option>
            <option value="price-desc">Cena (nejvyšší)</option>
            <option value="price-asc">Cena (nejnižší)</option>
        </select>

        <!-- Počítadlo viditelných karet -->
        <span class="filter-count" id="vl-count"></span>
    </div>

    <!-- Responsivní mřížka karet – sloupce se přizpůsobí dle šířky okna -->
    <div class="cards-grid" id="vl-grid">

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
                        $imgSrc    = htmlspecialchars($mainImage);
                        $imgExists = true;
                    } else {
                        $info      = Vinyl::getUploadInfo(basename($mainImage));
                        $imgSrc    = htmlspecialchars($info['url']);
                        $imgExists = $info['exists'];
                    }
                } else {
                    $imgSrc = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'><rect width='100%25' height='100%25' fill='%231a1510'/><circle cx='200' cy='200' r='160' fill='%23252018'/><circle cx='200' cy='200' r='60' fill='%230f0e0c'/><circle cx='200' cy='200' r='12' fill='%23333'/><text x='50%25' y='73%25' dominant-baseline='middle' text-anchor='middle' fill='%23555' font-family='sans-serif' font-size='18'>No image</text></svg>";
                }

                // Hodnoty pro HTML a data atributy
                $albumName    = htmlspecialchars($vinyl['album_name']);
                $artist       = htmlspecialchars($vinyl['artist']);
                $categoryName = htmlspecialchars($vinyl['category_name'] ?? '');
                $showUrl      = '/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=show&id=' . urlencode($vinyl['id']);
                $editUrl      = '/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=edit&id=' . urlencode($vinyl['id']);
                $deleteUrl    = '/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=delete&id=' . urlencode($vinyl['id']);

                // Pro data atributy filtru – malá písmena, HTML-safe
                $dataName     = htmlspecialchars(mb_strtolower($vinyl['album_name']));
                $dataArtist   = htmlspecialchars(mb_strtolower($vinyl['artist']));
                $dataCategory = htmlspecialchars(mb_strtolower($vinyl['category_name'] ?? ''));
                $dataYear     = (int)($vinyl['release_year'] ?? 0);
                $dataPrice    = round((float)($vinyl['price'] ?? 0), 2);
                $dataId       = (int)$vinyl['id'];

                $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
                $canEdit = $isAdmin;

                $delay = min($i * 60, 600);
            ?>

            <!-- Karta vinylu – data-* atributy umožňují JS filtrování a řazení -->
            <article class="vinyl-card"
                     style="--i:<?= $i ?>; animation-delay:<?= $delay ?>ms;"
                     data-name="<?= $dataName ?>"
                     data-artist="<?= $dataArtist ?>"
                     data-category="<?= $dataCategory ?>"
                     data-year="<?= $dataYear ?>"
                     data-price="<?= $dataPrice ?>"
                     data-id="<?= $dataId ?>">

                <a href="<?= $showUrl ?>" style="display:block; text-decoration:none;">

                    <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
                        <div style="position:absolute;top:6px;left:6px;background:oklch(14% 0.01 52/90%);color:var(--t2);font-size:10px;padding:6px 8px;border-radius:6px;max-width:200px;word-break:break-all;z-index:10;">
                            <div><?= htmlspecialchars($albumName) ?></div>
                            <div style="color:var(--t3);">URL: <?= $imgSrc ?></div>
                            <div>Exists: <?= $imgExists ? 'yes' : 'no' ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="vinyl-card__image">
                        <img src="<?= $imgSrc ?>" alt="<?= $albumName ?>" loading="lazy">
                        <div class="vinyl-card__overlay">
                            <span class="vinyl-card__overlay-pill">Zobrazit</span>
                        </div>
                    </div>

                    <div class="vinyl-card__body">
                        <div class="vinyl-card__name"><?= $albumName ?></div>
                        <div class="vinyl-card__artist"><?= $artist ?></div>
                        <?php if ($categoryName): ?>
                            <span class="vinyl-card__category"><?= $categoryName ?></span>
                        <?php endif; ?>
                    </div>
                </a>

                <?php if ($canEdit): ?>
                    <div class="vinyl-card__actions">
                        <a href="<?= $editUrl ?>" class="btn btn-sm btn-admin-edit">Upravit</a>
                        <a href="<?= $deleteUrl ?>"
                           onclick="return confirm('Opravdu chcete tento vinyl smazat?')"
                           class="btn btn-sm btn-admin-delete">Smazat</a>
                    </div>
                <?php endif; ?>

            </article>

        <?php endforeach; ?>
    </div>

    <!-- Prázdný stav filtru – zobrazí se pokud žádná karta nesplní kritéria -->
    <div id="vl-noresults" style="display:none;" class="empty-state">
        <p>Žádná deska neodpovídá hledání.</p>
        <small>Zkuste jiná klíčová slova nebo filtr.</small>
    </div>

    <script>
    (function () {
        var searchEl   = document.getElementById('vl-search');
        var categoryEl = document.getElementById('vl-category');
        var sortEl     = document.getElementById('vl-sort');
        var grid       = document.getElementById('vl-grid');
        var countEl    = document.getElementById('vl-count');
        var noResults  = document.getElementById('vl-noresults');
        var cards      = Array.from(grid.querySelectorAll('.vinyl-card'));
        var total      = cards.length;

        /* Zobrazíme počáteční stav počítadla */
        if (countEl) countEl.textContent = total + ' desek';

        function applyFilters() {
            var query    = searchEl.value.toLowerCase().trim();
            var category = categoryEl.value.toLowerCase();
            var sort     = sortEl.value;

            /* Filtrování */
            var visible = cards.filter(function (card) {
                var matchSearch = !query ||
                    (card.dataset.name   || '').includes(query) ||
                    (card.dataset.artist || '').includes(query);
                var matchCat = !category ||
                    (card.dataset.category || '') === category;
                return matchSearch && matchCat;
            });

            /* Řazení – výchozí pořadí (ID DESC) zachováno pomocí data-id */
            visible.sort(function (a, b) {
                switch (sort) {
                    case 'name':
                        return (a.dataset.name || '').localeCompare(b.dataset.name || '', 'cs');
                    case 'year-desc':
                        return (parseInt(b.dataset.year) || 0) - (parseInt(a.dataset.year) || 0);
                    case 'year-asc':
                        return (parseInt(a.dataset.year) || 0) - (parseInt(b.dataset.year) || 0);
                    case 'price-desc':
                        return parseFloat(b.dataset.price || 0) - parseFloat(a.dataset.price || 0);
                    case 'price-asc':
                        return parseFloat(a.dataset.price || 0) - parseFloat(b.dataset.price || 0);
                    default: /* newest = desc by data-id */
                        return parseInt(b.dataset.id || 0) - parseInt(a.dataset.id || 0);
                }
            });

            /* Skryjeme všechny karty */
            cards.forEach(function (c) { c.style.display = 'none'; });

            /* Zobrazíme a přeřadíme viditelné */
            visible.forEach(function (c, i) {
                c.style.display = '';
                c.style.animationDelay = Math.min(i * 35, 400) + 'ms';
                grid.appendChild(c);
            });

            /* Prázdný stav filtru */
            noResults.style.display = visible.length === 0 ? '' : 'none';
            grid.style.display      = visible.length === 0 ? 'none' : '';

            /* Počítadlo */
            if (countEl) {
                countEl.textContent = visible.length === total
                    ? total + ' desek'
                    : visible.length + ' z ' + total;
            }
        }

        searchEl.addEventListener('input',   applyFilters);
        categoryEl.addEventListener('change', applyFilters);
        sortEl.addEventListener('change',     applyFilters);
    })();
    </script>

<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
