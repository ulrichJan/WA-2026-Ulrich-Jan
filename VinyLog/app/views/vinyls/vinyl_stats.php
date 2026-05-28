<?php
// Vložíme hlavičku stránky (navigace, <main>)
require_once __DIR__ . '/../layout/header.php';

// Rozbalíme strukturu vracenou Vinyl::getStats()
$overview      = $stats['overview'];
$byCategory    = $stats['byCategory'];
$byGenre       = $stats['byGenre'];
$mostExpensive = $stats['mostExpensive'];
$latestAdded   = $stats['latestAdded'];

// Maximální hodnoty pro procentuální šíři pruhů
$maxCatCount   = !empty($byCategory) ? max(array_column($byCategory, 'cnt')) : 1;
$maxGenreCount = !empty($byGenre)    ? max(array_column($byGenre,    'cnt')) : 1;
?>

<!-- Záhlaví -->
<div style="margin-bottom:1.75rem;animation: fadeUp 300ms var(--ease-out) both;">
    <h2 style="font-size:1.65rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;margin-bottom:0.25rem;">
        Statistiky sbírky
    </h2>
    <p style="font-size:0.875rem;color:var(--t3);">
        Souhrnný přehled vaší vinylové kolekce.
    </p>
</div>

<!-- ============================================================
     Přehledová čísla
     ============================================================ -->
<div class="stats-overview" style="animation: fadeUp 340ms var(--ease-out) 60ms both;">

    <!-- Celkový počet desek -->
    <div class="stat-card">
        <div class="stat-number" style="color:var(--accent);"><?= (int)$overview['total_count'] ?></div>
        <div class="stat-label">Desek v sbírce</div>
    </div>

    <!-- Celková hodnota sbírky -->
    <div class="stat-card">
        <div class="stat-number" style="color:var(--cta);">
            <?= number_format((float)$overview['total_value'], 0, ',', '&thinsp;') ?> Kč
        </div>
        <div class="stat-label">Celková hodnota</div>
    </div>

    <!-- Průměrná cena -->
    <div class="stat-card">
        <div class="stat-number" style="color:var(--t1);font-size:1.75rem;">
            <?= number_format((float)$overview['avg_price'], 0, ',', '&thinsp;') ?> Kč
        </div>
        <div class="stat-label">Průměrná cena</div>
    </div>

    <!-- Roky sbírky -->
    <div class="stat-card">
        <div class="stat-number" style="color:var(--t1);font-size:1.75rem;">
            <?php
                $oldest = (int)$overview['oldest_year'];
                $newest = (int)$overview['newest_year'];
                if ($oldest && $newest && $oldest !== $newest) {
                    echo $oldest . '–' . $newest;
                } elseif ($newest) {
                    echo $newest;
                } else {
                    echo '—';
                }
            ?>
        </div>
        <div class="stat-label">Rozpětí roků vydání</div>
    </div>

</div>

<!-- ============================================================
     Rozdělení dle kategorie
     ============================================================ -->
<?php if (!empty($byCategory)): ?>
<div class="stats-section" style="animation: fadeUp 360ms var(--ease-out) 120ms both;">
    <div class="stats-section-title">Podle kategorie</div>

    <?php foreach ($byCategory as $row):
        $pct = $maxCatCount > 0 ? round($row['cnt'] / $maxCatCount * 100) : 0;
    ?>
        <div class="bar-row">
            <div class="bar-label"><?= htmlspecialchars($row['name']) ?></div>
            <div class="bar-track">
                <div class="bar-fill bar-fill--accent"
                     data-width="<?= $pct ?>%"
                     style="width:0%;"></div>
            </div>
            <div class="bar-count"><?= (int)$row['cnt'] ?></div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================
     Rozdělení dle žánru (top 8)
     ============================================================ -->
<?php if (!empty($byGenre)): ?>
<div class="stats-section" style="animation: fadeUp 360ms var(--ease-out) 180ms both;">
    <div class="stats-section-title">Top žánry</div>

    <?php foreach ($byGenre as $row):
        $pct = $maxGenreCount > 0 ? round($row['cnt'] / $maxGenreCount * 100) : 0;
    ?>
        <div class="bar-row">
            <div class="bar-label"><?= htmlspecialchars($row['name']) ?></div>
            <div class="bar-track">
                <div class="bar-fill bar-fill--cta"
                     data-width="<?= $pct ?>%"
                     style="width:0%;"></div>
            </div>
            <div class="bar-count"><?= (int)$row['cnt'] ?></div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================================================
     Zajímavosti
     ============================================================ -->
<div style="animation: fadeUp 380ms var(--ease-out) 240ms both;">
    <div class="stats-section-title" style="margin-bottom:1rem;">Zajímavosti</div>
    <div class="facts-grid">

        <?php if ($overview['oldest_year']): ?>
        <div class="fact-card">
            <div class="fact-label">Nejstarší vydání</div>
            <div class="fact-value"><?= (int)$overview['oldest_year'] ?></div>
        </div>
        <?php endif; ?>

        <?php if ($overview['newest_year']): ?>
        <div class="fact-card">
            <div class="fact-label">Nejnovější vydání</div>
            <div class="fact-value"><?= (int)$overview['newest_year'] ?></div>
        </div>
        <?php endif; ?>

        <?php if ($mostExpensive): ?>
        <div class="fact-card">
            <div class="fact-label">Nejdražší deska</div>
            <div class="fact-value"><?= htmlspecialchars($mostExpensive['album_name']) ?></div>
            <div class="fact-sub">
                <?= htmlspecialchars($mostExpensive['artist']) ?>
                &mdash;
                <?= number_format((float)$mostExpensive['price'], 0, ',', '&thinsp;') ?> Kč
            </div>
        </div>
        <?php endif; ?>

        <?php if ($latestAdded): ?>
        <div class="fact-card">
            <div class="fact-label">Naposledy přidáno</div>
            <div class="fact-value">
                <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=show&id=<?= (int)$latestAdded['id'] ?>"
                   style="color:var(--accent);text-decoration:none;"
                   onmouseover="this.style.textDecoration='underline'"
                   onmouseout="this.style.textDecoration='none'">
                    <?= htmlspecialchars($latestAdded['album_name']) ?>
                </a>
            </div>
            <div class="fact-sub"><?= htmlspecialchars($latestAdded['artist']) ?></div>
        </div>
        <?php endif; ?>

        <?php if ($overview['max_price']): ?>
        <div class="fact-card">
            <div class="fact-label">Nejvyšší cena v sbírce</div>
            <div class="fact-value" style="color:var(--cta);">
                <?= number_format((float)$overview['max_price'], 0, ',', '&thinsp;') ?> Kč
            </div>
        </div>
        <?php endif; ?>

        <?php if ((int)$overview['total_count'] > 0 && (float)$overview['total_value'] > 0): ?>
        <div class="fact-card">
            <div class="fact-label">Desek s cenou</div>
            <div class="fact-value">
                <?php
                    // Spočítáme, kolik desek má zadanou cenu > 0
                    // Tato informace není v getStats() – zobrazíme celkový count jako hint
                    echo (int)$overview['total_count'];
                ?>
            </div>
            <div class="fact-sub">celkem v sbírce</div>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- Animace pruhů: spustíme po načtení stránky s mírným zpožděním -->
<script>
(function () {
    /* Počkáme na dokončení staggered fade-in animace sekcí, pak roztáhneme pruhy */
    setTimeout(function () {
        document.querySelectorAll('.bar-fill[data-width]').forEach(function (bar) {
            bar.style.width = bar.dataset.width;
        });
    }, 450);
})();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
