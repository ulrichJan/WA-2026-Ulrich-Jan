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

<!-- =====================================================
     Sekce komentářů
     ===================================================== -->
<div style="margin-top:2rem;max-width:860px;animation: fadeUp 380ms var(--ease-out) 280ms both;">

    <!-- Nadpis sekce -->
    <h3 style="font-size:1rem;font-weight:700;color:var(--t1);letter-spacing:-0.01em;margin-bottom:1.125rem;">
        Komentáře
        <span style="font-size:0.78rem;font-weight:500;color:var(--t3);margin-left:0.4rem;">(<?= count($comments) ?>)</span>
    </h3>

    <!-- Formulář pro přidání komentáře – viditelný pouze přihlášeným uživatelům -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="detail-meta" style="margin-bottom:1.125rem;">
            <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=addComment&vinyl_id=<?= (int)$vinyl['id'] ?>"
                  method="post">
                <div style="display:flex;flex-direction:column;gap:0.625rem;">
                    <textarea name="content" required
                              placeholder="Napište komentář k tomuto vinylu..."
                              rows="3"
                              style="background:var(--bg-input);border:1px solid var(--border);border-radius:8px;
                                     padding:0.5rem 0.75rem;color:var(--t1);font-size:0.875rem;
                                     font-family:inherit;outline:none;width:100%;resize:vertical;
                                     min-height:72px;transition:border-color 140ms ease,box-shadow 140ms ease;"
                              onfocus="this.style.borderColor='var(--border-focus)';this.style.boxShadow='0 0 0 3px var(--accent-bg)'"
                              onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'"></textarea>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="btn btn-primary btn-sm">Přidat komentář</button>
                    </div>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Výzva k přihlášení pro nepřihlášené návštěvníky -->
        <div class="detail-meta" style="margin-bottom:1.125rem;text-align:center;padding:1.25rem;">
            <p style="font-size:0.875rem;color:var(--t3);">
                Pro přidání komentáře se
                <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=login"
                   style="color:var(--accent);font-weight:600;">přihlaste</a>.
            </p>
        </div>
    <?php endif; ?>

    <!-- Seznam komentářů -->
    <?php if (empty($comments)): ?>
        <div style="text-align:center;padding:2.5rem 2rem;color:var(--t3);font-size:0.875rem;">
            Zatím žádné komentáře.
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <?php foreach ($comments as $comment):
                // Zobrazíme přezdívku pokud ji uživatel má, jinak username
                $authorName = htmlspecialchars($comment['nickname'] ?: $comment['username'] ?: 'Anonym');
                // Počáteční písmeno pro avatar
                $initial    = mb_strtoupper(mb_substr($comment['nickname'] ?: $comment['username'] ?: '?', 0, 1));
                // Datum ve formátu DD.MM.YYYY HH:MM
                $commentDate = (new DateTime($comment['created_at']))->format('d.m.Y H:i');
                // Kdo může smazat: vlastník komentáře nebo admin
                $isOwnComment    = isset($_SESSION['user_id']) && (int)$comment['user_id'] === (int)$_SESSION['user_id'];
                $canDeleteComment = $isOwnComment || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);
            ?>
                <div class="detail-meta" style="padding:1rem 1.25rem;">
                    <!-- Hlavička komentáře: avatar + autor + datum + smazat -->
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;margin-bottom:0.625rem;">
                        <div style="display:flex;align-items:center;gap:0.625rem;">
                            <!-- Kruhový avatar s iniciálou -->
                            <div style="width:30px;height:30px;border-radius:50%;
                                        background:var(--accent-bg);border:1px solid oklch(62% 0.21 148 / 25%);
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:0.7rem;font-weight:700;color:var(--accent-txt);flex-shrink:0;">
                                <?= $initial ?>
                            </div>
                            <div>
                                <span style="font-size:0.875rem;font-weight:600;color:var(--t1);"><?= $authorName ?></span>
                                <span style="font-size:0.72rem;color:var(--t3);margin-left:0.45rem;"><?= $commentDate ?></span>
                            </div>
                        </div>
                        <?php if ($canDeleteComment): ?>
                            <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=deleteComment&id=<?= (int)$comment['id'] ?>&vinyl_id=<?= (int)$vinyl['id'] ?>"
                               onclick="return confirm('Smazat tento komentář?')"
                               style="font-size:0.75rem;color:var(--t3);transition:color 140ms ease;white-space:nowrap;"
                               onmouseover="this.style.color='var(--danger)'"
                               onmouseout="this.style.color='var(--t3)'">
                                × smazat
                            </a>
                        <?php endif; ?>
                    </div>
                    <!-- Text komentáře -->
                    <p style="font-size:0.875rem;color:var(--t2);line-height:1.6;white-space:pre-wrap;"><?= htmlspecialchars($comment['content']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
