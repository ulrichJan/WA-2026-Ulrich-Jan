<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-content">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div>
            <h2 class="text-3xl font-semibold" style="color:var(--t1);letter-spacing:-0.02em;">Vinylové desky</h2>
            <p style="font-size:14px;color:var(--t2);margin-top:4px;">Sbírka <?= count($vinyls ?? []) ?> alb</p>
        </div>
        <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=create"
           class="btn btn-gold">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1v12M1 7h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Přidat desku
        </a>
    </div>

    <?php if (empty($vinyls)): ?>
        <div class="flex flex-col items-center justify-center py-32 gap-4" style="animation: fadeUp 0.5s var(--ease-out) both;">
            <div style="width:72px;height:72px;border-radius:50%;background:radial-gradient(circle,#111 0%,#111 22%,#2a2a2a 23%,#2a2a2a 38%,#181818 39%,#181818 47%,var(--accent) 48%,var(--accent) 52%,#181818 53%,#181818 100%);opacity:0.4;"></div>
            <p style="font-size:16px;color:var(--t2);">Zatím žádné desky</p>
            <p style="font-size:13px;color:var(--t3);">Začněte přidáním prvního záznamu.</p>
        </div>

    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php
                require_once __DIR__ . '/../../models/Vinyl.php';
                $cardIdx = 0;
                foreach ($vinyls as $vinyl):
                    $delayMs = $cardIdx * 55;
                    $cardIdx++;

                    /* --- Image resolution --- */
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
                            $dec = json_decode($mainImage, true);
                            $mainImage = (is_array($dec) && count($dec) > 0) ? $dec[0] : null;
                        }
                        if ($mainImage !== null) $mainImage = trim($mainImage);
                        if ($mainImage === '' || $mainImage === '[]') $mainImage = null;
                    }
                    $imgSrc = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'><rect width='100%25' height='100%25' fill='%23181818'/><circle cx='200' cy='200' r='80' fill='none' stroke='%23333' stroke-width='2'/><circle cx='200' cy='200' r='12' fill='%23333'/></svg>";
                    if ($mainImage) {
                        if (strpos($mainImage, 'http') === 0) {
                            $imgSrc = htmlspecialchars($mainImage);
                        } else {
                            $info   = Vinyl::getUploadInfo(basename($mainImage));
                            $imgSrc = htmlspecialchars($info['url']);
                        }
                    }

                    $albumName    = htmlspecialchars($vinyl['album_name']);
                    $artist       = htmlspecialchars($vinyl['artist']);
                    $categoryName = htmlspecialchars($vinyl['category_name'] ?? 'Nezařazeno');
                    $showUrl      = '?action=show&id=' . urlencode($vinyl['id']);
                    $isOwner      = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)($vinyl['created_by'] ?? 0);
                    $isAdmin      = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
                    $canEdit      = $isOwner || $isAdmin;
            ?>

            <article class="vinyl-card card-stagger" style="animation-delay:<?= $delayMs ?>ms;">
                <a href="<?= $showUrl ?>" style="display:block;text-decoration:none;">

                    <!-- Image area -->
                    <div class="vinyl-img-wrap" style="position:relative;height:240px;background:var(--s3);">
                        <img src="<?= $imgSrc ?>" alt="<?= $albumName ?>" class="vinyl-img-inner">

                        <!-- Vinyl disc peek -->
                        <div class="vinyl-disc">
                            <div class="vinyl-disc-inner"></div>
                        </div>

                        <!-- Hover overlay with CTA -->
                        <div class="card-overlay">
                            <span class="card-cta">Zobrazit &rarr;</span>
                        </div>
                    </div>

                    <!-- Text content -->
                    <div style="padding:16px 18px 14px;">
                        <h3 style="font-size:15px;font-weight:600;color:var(--t1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px;"><?= $albumName ?></h3>
                        <p style="font-size:13px;color:var(--t2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:6px;"><?= $artist ?></p>
                        <span style="font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--accent);opacity:0.8;"><?= $categoryName ?></span>
                    </div>
                </a>

                <?php if ($canEdit): ?>
                    <div style="padding:0 18px 16px;display:flex;gap:8px;">
                        <a href="?action=edit&id=<?= urlencode($vinyl['id']) ?>" class="card-action card-action-edit">
                            Upravit<?= (!$isOwner && $isAdmin) ? ' (admin)' : '' ?>
                        </a>
                        <a href="?action=delete&id=<?= urlencode($vinyl['id']) ?>"
                           onclick="return confirm('Opravdu chcete tento vinyl smazat?')"
                           class="card-action card-action-delete">
                            Smazat<?= (!$isOwner && $isAdmin) ? ' (admin)' : '' ?>
                        </a>
                    </div>
                <?php endif; ?>
            </article>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
