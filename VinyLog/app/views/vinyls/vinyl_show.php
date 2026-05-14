<?php
if (!isset($vinyl) || !is_array($vinyl)) {
    header('Location: /WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=index');
    exit;
}
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-content">

    <!-- Back link -->
    <div style="margin-bottom:28px;">
        <a class="back-link" href="?action=index">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="transition:transform 180ms var(--ease-out);">
                <path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Zpět na seznam
        </a>
    </div>

    <div class="grid md:grid-cols-2 gap-8 items-start">

        <!-- LEFT: Images -->
        <div style="animation: fadeUp 0.5s var(--ease-out) both;">

            <?php
                require_once __DIR__ . '/../../models/Vinyl.php';
                $images    = json_decode($vinyl['album_cover'], true);
                $mainImage = null;
                if (is_array($images) && count($images) > 0) {
                    $mainImage = $images[0];
                } elseif (!empty($vinyl['album_cover'])) {
                    $mainImage = $vinyl['album_cover'];
                }
                if (is_string($mainImage)) {
                    $mainImage = trim($mainImage);
                    if (strlen($mainImage) > 0 && $mainImage[0] === '[') {
                        $decoded = json_decode($mainImage, true);
                        $mainImage = (is_array($decoded) && count($decoded) > 0) ? $decoded[0] : null;
                    }
                    if ($mainImage !== null) $mainImage = trim($mainImage);
                    if ($mainImage === '' || $mainImage === '[]') $mainImage = null;
                }
                $noImgSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 600'><rect width='100%25' height='100%25' fill='%23181818'/><circle cx='300' cy='300' r='120' fill='none' stroke='%23333' stroke-width='3'/><circle cx='300' cy='300' r='18' fill='%23333'/></svg>";
                if ($mainImage) {
                    if (strpos($mainImage, 'http') === 0) {
                        $mainSrc   = htmlspecialchars($mainImage);
                        $mainExists = true;
                    } else {
                        $info      = Vinyl::getUploadInfo(basename($mainImage));
                        $mainSrc   = htmlspecialchars($info['url']);
                        $mainExists = $info['exists'];
                    }
                } else {
                    $mainSrc    = $noImgSvg;
                    $mainExists = false;
                }
            ?>

            <!-- Main image -->
            <div style="border-radius:18px;overflow:hidden;border:1px solid var(--border);background:var(--s2);">
                <img src="<?= $mainSrc ?>"
                     alt="<?= htmlspecialchars($vinyl['album_name']) ?>"
                     class="show-hero-img"
                     style="width:100%;height:480px;object-fit:cover;display:block;">
            </div>

            <!-- Thumbnail strip -->
            <?php if (is_array($images) && count($images) > 1): ?>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:10px;">
                    <?php foreach ($images as $idx => $img):
                        if ($idx === 0) continue;
                        $img = trim($img);
                        $tSrc = (strpos($img,'http')===0)
                            ? htmlspecialchars($img)
                            : htmlspecialchars(Vinyl::getUploadInfo(basename($img))['url']);
                    ?>
                        <div class="thumb-strip-item">
                            <img src="<?= $tSrc ?>" alt="thumb" style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: Info + Actions -->
        <div style="display:flex;flex-direction:column;gap:16px;">

            <!-- Album title / artist header -->
            <div style="animation: fadeUp 0.5s var(--ease-out) 60ms both;">
                <h2 style="font-size:2rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;line-height:1.2;margin-bottom:6px;">
                    <?= htmlspecialchars($vinyl['album_name']) ?>
                </h2>
                <p style="font-size:1.1rem;color:var(--accent);font-weight:500;">
                    <?= htmlspecialchars($vinyl['artist']) ?>
                </p>
            </div>

            <!-- Info card -->
            <div class="info-card" style="animation: fadeUp 0.5s var(--ease-out) 110ms both;">
                <p style="font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--t3);margin-bottom:6px;">Informace o albu</p>

                <div class="info-row" style="animation-delay:150ms;">
                    <div class="info-label">ID</div>
                    <div class="info-value" style="color:var(--t3);font-family:monospace;">#<?= htmlspecialchars($vinyl['id']) ?></div>
                </div>
                <div class="info-row" style="animation-delay:190ms;">
                    <div class="info-label">Název alba</div>
                    <div class="info-value"><?= htmlspecialchars($vinyl['album_name']) ?></div>
                </div>
                <div class="info-row" style="animation-delay:230ms;">
                    <div class="info-label">Umělec</div>
                    <div class="info-value"><?= htmlspecialchars($vinyl['artist']) ?></div>
                </div>
                <div class="info-row" style="animation-delay:270ms;">
                    <div class="info-label">Rok vydání</div>
                    <div class="info-value">
                        <?php if (!empty($vinyl['release_year'])): ?>
                            <?= htmlspecialchars($vinyl['release_year']) ?>
                        <?php else: ?>
                            <span style="color:var(--t3);font-style:italic;">Nezadáno</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-row" style="animation-delay:310ms;">
                    <div class="info-label">Žánr</div>
                    <div class="info-value">
                        <?php if (!empty($vinyl['genre'])): ?>
                            <?= htmlspecialchars($vinyl['genre']) ?>
                        <?php else: ?>
                            <span style="color:var(--t3);font-style:italic;">Nezadáno</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-row" style="animation-delay:350ms;">
                    <div class="info-label">Kategorie</div>
                    <div class="info-value"><?= htmlspecialchars($vinyl['category_name'] ?? 'Nezařazeno') ?></div>
                </div>
                <?php if (!empty($vinyl['subcategory_name'])): ?>
                <div class="info-row" style="animation-delay:390ms;">
                    <div class="info-label">Podkategorie</div>
                    <div class="info-value"><?= htmlspecialchars($vinyl['subcategory_name']) ?></div>
                </div>
                <?php endif; ?>
                <div class="info-row" style="animation-delay:430ms;">
                    <div class="info-label">Cena</div>
                    <div class="info-value" style="color:var(--accent);font-size:18px;font-weight:700;">
                        <?= !empty($vinyl['price']) ? htmlspecialchars($vinyl['price']) . ' Kč' : '<span style="color:var(--t3);font-style:italic;font-size:15px;font-weight:500;">Nezadáno</span>' ?>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <?php
                $isOwner = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)($vinyl['created_by'] ?? 0);
                $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
            ?>
            <?php if ($isOwner || $isAdmin): ?>
                <div class="info-card" style="animation: fadeUp 0.5s var(--ease-out) 500ms both;">
                    <p style="font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--t3);margin-bottom:16px;">Akce</p>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <a href="?action=edit&id=<?= htmlspecialchars($vinyl['id']) ?>" class="btn btn-outline" style="flex:1;min-width:120px;">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9.5 1.5l3 3-7 7H2.5v-3l7-7z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Upravit<?= (!$isOwner && $isAdmin) ? ' (admin)' : '' ?>
                        </a>
                        <a href="?action=delete&id=<?= htmlspecialchars($vinyl['id']) ?>"
                           onclick="return confirm('Opravdu chcete tento vinyl smazat?')"
                           class="btn btn-danger" style="flex:1;min-width:120px;">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 3.5h10M5 3.5V2h4v1.5M5.5 6v4.5M8.5 6v4.5M3 3.5l.7 8h6.6l.7-8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Smazat<?= (!$isOwner && $isAdmin) ? ' (admin)' : '' ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
