<?php
// Vložíme hlavičku stránky (navigace, <main>)
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Odkaz zpět na seznam desek -->
<a class="back-link" href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=index">
    ← Zpět na seznam
</a>

<div class="detail-wrapper" style="animation: fadeUp 340ms var(--ease-out) both;">
    <div style="padding: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; align-items: start;">

        <!-- =====================================================
             Levý sloupec: Galerie obrázků alba
             ===================================================== -->
        <div>
            <?php
                // Načteme model Vinyl pro použití statické metody getUploadInfo()
                require_once __DIR__ . '/../../models/Vinyl.php';

                // Dekódujeme JSON pole názvů obrázků z DB
                $images    = json_decode($vinyl['album_cover'], true);
                $mainImage = null;

                if (is_array($images) && count($images) > 0) {
                    $mainImage = $images[0]; // Hlavní obrázek = první v poli
                } elseif (!empty($vinyl['album_cover'])) {
                    $mainImage = $vinyl['album_cover']; // Fallback
                }

                // Normalizace: ošetříme vnořené JSON řetězce a prázdné hodnoty
                if (is_string($mainImage)) {
                    $mainImage = trim($mainImage);
                    if (strlen($mainImage) > 0 && $mainImage[0] === '[') {
                        $decoded   = json_decode($mainImage, true);
                        $mainImage = (is_array($decoded) && count($decoded) > 0) ? $decoded[0] : null;
                    }
                    if ($mainImage !== null) $mainImage = trim($mainImage);
                    if ($mainImage === '' || $mainImage === '[]') $mainImage = null;
                }

                // Výpočet src pro hlavní obrázek
                if ($mainImage) {
                    if (strpos($mainImage, 'http') === 0) {
                        $mainSrc    = htmlspecialchars($mainImage);
                        $mainExists = true;
                    } else {
                        $info       = Vinyl::getUploadInfo(basename($mainImage));
                        $mainSrc    = htmlspecialchars($info['url']);
                        $mainExists = $info['exists'];
                    }
                } else {
                    // Placeholder SVG pokud vinyl nemá obrázek
                    $mainSrc    = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 500'><rect width='100%25' height='100%25' fill='%231a1510'/><circle cx='250' cy='250' r='200' fill='%23252018'/><circle cx='250' cy='250' r='75' fill='%230f0e0c'/><circle cx='250' cy='250' r='15' fill='%23333'/><text x='50%25' y='75%25' dominant-baseline='middle' text-anchor='middle' fill='%23555' font-family='sans-serif' font-size='22'>No image</text></svg>";
                    $mainExists = false;
                }
            ?>

            <!-- Hlavní obrázek alba -->
            <div class="detail-image-box">
                <img src="<?= $mainSrc ?>"
                     alt="<?= htmlspecialchars($vinyl['album_name']) ?>">
            </div>

            <?php if (is_array($images) && count($images) > 1): ?>
                <!-- Miniaturní galerie: zbývající obrázky (přeskočíme první = hlavní) -->
                <div class="detail-thumbs">
                    <?php foreach ($images as $idx => $img):
                        if ($idx === 0) continue; // Přeskočíme hlavní obrázek
                        $img = trim($img);
                        if (strpos($img, 'http') === 0) {
                            $thumb = htmlspecialchars($img);
                        } else {
                            $tinfo = Vinyl::getUploadInfo(basename($img));
                            $thumb = htmlspecialchars($tinfo['url']);
                        }
                    ?>
                        <div class="detail-thumb">
                            <img src="<?= $thumb ?>" alt="thumb">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- =====================================================
             Pravý sloupec: Metadata + Nadpis + Akce
             ===================================================== -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            <!-- Nadpis alba a umělce nad metadata tabulkou -->
            <div style="animation: fadeUp 280ms var(--ease-out) 80ms both;">
                <h2 style="font-size:1.75rem;font-weight:700;color:var(--t1);letter-spacing:-0.025em;line-height:1.2;">
                    <?= htmlspecialchars($vinyl['album_name']) ?>
                </h2>
                <p style="font-size:1.1rem;color:var(--t2);margin-top:0.3rem;">
                    <?= htmlspecialchars($vinyl['artist']) ?>
                </p>
            </div>

            <!-- Tabulka metadat vinylu -->
            <div class="detail-meta" style="animation: fadeUp 280ms var(--ease-out) 140ms both;">
                <div class="meta-table">

                    <div class="meta-row">
                        <span class="meta-key">ID</span>
                        <span class="meta-val" style="color:var(--t3);">#<?= htmlspecialchars($vinyl['id']) ?></span>
                    </div>

                    <div class="meta-row">
                        <span class="meta-key">Rok vydání</span>
                        <span class="meta-val"><?= htmlspecialchars($vinyl['release_year']) ?: '—' ?></span>
                    </div>

                    <div class="meta-row">
                        <span class="meta-key">Žánr</span>
                        <span class="meta-val"><?= htmlspecialchars($vinyl['genre']) ?: '—' ?></span>
                    </div>

                    <div class="meta-row">
                        <span class="meta-key">Kategorie</span>
                        <span class="meta-val">
                            <?php if (!empty($vinyl['category_name'])): ?>
                                <span style="display:inline-block;background:var(--accent-bg);color:var(--accent-txt);font-size:0.78rem;font-weight:500;padding:0.2em 0.6em;border-radius:4px;">
                                    <?= htmlspecialchars($vinyl['category_name']) ?>
                                </span>
                            <?php else: ?>
                                <span style="color:var(--t3);">Nezařazeno</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if (!empty($vinyl['subcategory_name'])): ?>
                        <!-- Podkategorie se zobrazí pouze pokud vinyl má přiřazenou podkategorii -->
                        <div class="meta-row">
                            <span class="meta-key">Podkategorie</span>
                            <span class="meta-val"><?= htmlspecialchars($vinyl['subcategory_name']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="meta-row">
                        <span class="meta-key">Cena</span>
                        <span class="meta-val" style="font-weight:600;color:var(--cta);">
                            <?= htmlspecialchars($vinyl['price']) ?> Kč
                        </span>
                    </div>

                </div>
            </div>

            <!-- Akční tlačítka: zobrazí se vlastníkovi nebo adminovi.
                 Admin vidí záznamy ostatních uživatelů s odlišnými styly tlačítek. -->
            <?php
                // Pouze admin může editovat/mazat záznamy – řadoví uživatelé ne
                $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
            ?>
            <?php if ($isAdmin): ?>
                <div class="actions-box" style="animation: fadeUp 280ms var(--ease-out) 200ms both;">
                    <div style="font-size:0.75rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--t3);margin-bottom:0.875rem;">
                        Akce
                    </div>
                    <div style="display:flex;gap:0.75rem;">
                        <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=edit&id=<?= htmlspecialchars($vinyl['id']) ?>"
                           class="btn btn-admin-edit">
                            Upravit
                        </a>
                        <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=delete&id=<?= htmlspecialchars($vinyl['id']) ?>"
                           onclick="return confirm('Opravdu chcete tento vinyl smazat?')"
                           class="btn btn-admin-delete">
                            Smazat
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Jednoduchý responsivní override pro mobilní layout -->
<style>
@media (max-width: 680px) {
    .detail-wrapper > div { grid-template-columns: 1fr !important; }
}
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
