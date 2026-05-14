<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-content" style="max-width:680px;margin:0 auto;">

    <div style="margin-bottom:24px;">
        <a class="back-link" href="VinylController.php?action=index">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Zpět na seznam vinylů
        </a>
    </div>

    <div class="form-card">
        <div style="margin-bottom:32px;">
            <h2 style="font-size:1.6rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;margin-bottom:6px;">Upravit vinyl</h2>
            <p style="font-size:14px;color:var(--t2);">
                Upravujete: <span style="color:var(--accent);font-weight:600;"><?= htmlspecialchars($vinyl['album_name']) ?></span>
            </p>
        </div>

        <form action="VinylController.php?action=update&id=<?= htmlspecialchars($vinyl['id']) ?>"
              method="post" enctype="multipart/form-data">

            <div style="display:flex;flex-direction:column;gap:22px;">

                <!-- ID (readonly) -->
                <div style="animation: fadeUp 0.4s var(--ease-out) 40ms both;">
                    <label class="form-label">ID v databázi</label>
                    <input type="text" value="#<?= htmlspecialchars($vinyl['id']) ?>" readonly class="input-readonly">
                </div>

                <!-- Album name -->
                <div style="animation: fadeUp 0.4s var(--ease-out) 80ms both;">
                    <label for="album_name" class="form-label">
                        Název alba <span style="color:var(--red);">*</span>
                    </label>
                    <input type="text" id="album_name" name="album_name"
                           value="<?= htmlspecialchars($vinyl['album_name']) ?>" required class="input-field">
                </div>

                <!-- Artist -->
                <div style="animation: fadeUp 0.4s var(--ease-out) 120ms both;">
                    <label for="artist" class="form-label">
                        Umělec <span style="color:var(--red);">*</span>
                    </label>
                    <input type="text" id="artist" name="artist"
                           value="<?= htmlspecialchars($vinyl['artist']) ?>" required class="input-field">
                </div>

                <!-- Category + Subcategory -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;animation: fadeUp 0.4s var(--ease-out) 160ms both;">
                    <div>
                        <label for="category" class="form-label">
                            Kategorie <span style="color:var(--red);">*</span>
                        </label>
                        <select id="category" name="category" required class="input-field">
                            <option value="">-- Vyberte --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat):
                                    $sel = (isset($vinyl['category_id']) && (int)$vinyl['category_id'] === (int)$cat['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $sel ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="subcategory" class="form-label">Podkategorie</label>
                        <select id="subcategory" name="subcategory" class="input-field">
                            <option value="">-- Žádná --</option>
                            <?php if (!empty($subcategories)): ?>
                                <?php foreach ($subcategories as $sub):
                                    if ((int)$sub['category_id'] !== (int)($vinyl['category_id'] ?? 0)) continue;
                                    $sel = (isset($vinyl['subcategory_id']) && (int)$vinyl['subcategory_id'] === (int)$sub['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($sub['id']) ?>" <?= $sel ?>><?= htmlspecialchars($sub['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Year / Genre / Price -->
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;animation: fadeUp 0.4s var(--ease-out) 200ms both;">
                    <div>
                        <label for="release_year" class="form-label">Rok vydání</label>
                        <input type="number" id="release_year" name="release_year"
                               min="1900" max="2100" value="<?= htmlspecialchars($vinyl['release_year']) ?>" class="input-field">
                    </div>
                    <div>
                        <label for="genre" class="form-label">Žánr</label>
                        <input type="text" id="genre" name="genre"
                               value="<?= htmlspecialchars($vinyl['genre']) ?>" class="input-field">
                    </div>
                    <div>
                        <label for="price" class="form-label">Cena (Kč)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0"
                               value="<?= htmlspecialchars($vinyl['price']) ?>" class="input-field">
                    </div>
                </div>

                <!-- Existing images -->
                <?php
                    $existingImages = [];
                    if (!empty($vinyl['album_cover'])) {
                        $existingImages = json_decode($vinyl['album_cover'], true);
                        if (!is_array($existingImages)) {
                            $maybe = trim($vinyl['album_cover']);
                            if (strlen($maybe) > 0 && $maybe[0] === '[') {
                                $dec = json_decode($maybe, true);
                                $existingImages = is_array($dec) ? $dec : [];
                            } else {
                                $existingImages = [$vinyl['album_cover']];
                            }
                        }
                    }
                ?>
                <?php if (!empty($existingImages)): ?>
                    <div style="animation: fadeUp 0.4s var(--ease-out) 240ms both;">
                        <label class="form-label">Existující obrázky</label>
                        <div style="background:var(--s2);border:1px solid var(--border);border-radius:12px;padding:16px;">
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:12px;">
                                <?php foreach ($existingImages as $img):
                                    $img = trim($img);
                                    $uploadDir  = realpath(__DIR__ . '/../../public/uploads');
                                    $docRoot    = realpath($_SERVER['DOCUMENT_ROOT']);
                                    $projectRoot = realpath(__DIR__ . '/../../');
                                    $uploadUrlBase = '/public/uploads';
                                    if ($docRoot && $projectRoot && strpos($projectRoot, $docRoot) === 0) {
                                        $sub = str_replace('\\','/', substr($projectRoot, strlen($docRoot)));
                                        $sub = rtrim($sub, '/');
                                        $uploadUrlBase = ($sub === '') ? '/public/uploads' : $sub . '/public/uploads';
                                    }
                                    $srcImg = (strpos($img,'http')!==0 && strpos($img,'/')!==0)
                                        ? rtrim($uploadUrlBase,'/').'/'.ltrim($img,'/')
                                        : $img;
                                ?>
                                    <div class="img-thumb">
                                        <img src="<?= htmlspecialchars($srcImg) ?>" alt="obrázek">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <p style="font-size:12px;color:var(--t3);font-style:italic;">Nahráním nových souborů přepíšete stávající obrázky.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- File upload -->
                <div style="animation: fadeUp 0.4s var(--ease-out) 280ms both;">
                    <label class="form-label">Nahrát nové obrázky</label>
                    <label for="album_cover" class="upload-zone" id="upload-label">
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" style="color:var(--t3);margin-bottom:4px;">
                            <path d="M14 18V8M10 12l4-4 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 20a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span id="file-title" style="font-size:14px;font-weight:600;color:var(--t2);">Klikněte pro výběr souborů</span>
                        <span id="file-info" style="font-size:12px;color:var(--t3);text-align:center;">Ponechte prázdné pro zachování stávajících obrázků</span>
                        <input type="file" id="album_cover" name="album_cover[]" multiple accept="image/*" style="display:none;">
                    </label>
                </div>

                <!-- Submit -->
                <div style="padding-top:8px;animation: fadeUp 0.4s var(--ease-out) 320ms both;">
                    <button type="submit" class="btn btn-gold" style="width:100%;padding:13px 24px;font-size:15px;">
                        Uložit změny
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    const allSubcategories    = <?= json_encode($subcategories ?? []) ?>;
    const currentSubcategoryId = <?= (int)($vinyl['subcategory_id'] ?? 0) ?>;

    document.getElementById('category').addEventListener('change', function () {
        const categoryId = parseInt(this.value);
        const subSelect  = document.getElementById('subcategory');
        const filtered   = allSubcategories.filter(s => s.category_id == categoryId);

        subSelect.innerHTML = '';
        const def = document.createElement('option');
        def.value = '';
        def.textContent = filtered.length ? '-- Vyberte podkategorii --' : '-- Žádné podkategorie --';
        subSelect.appendChild(def);

        filtered.forEach(sub => {
            const opt = document.createElement('option');
            opt.value = sub.id;
            opt.textContent = sub.name;
            if (sub.id == currentSubcategoryId) opt.selected = true;
            subSelect.appendChild(opt);
        });
    });

    const fileInput   = document.getElementById('album_cover');
    const fileTitle   = document.getElementById('file-title');
    const fileInfo    = document.getElementById('file-info');
    const uploadLabel = document.getElementById('upload-label');

    fileInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (!files || files.length === 0) {
            fileTitle.textContent = 'Klikněte pro výběr souborů';
            fileTitle.style.color = 'var(--t2)';
            fileInfo.textContent  = 'Ponechte prázdné pro zachování stávajících obrázků';
            uploadLabel.classList.remove('active');
        } else if (files.length === 1) {
            fileTitle.textContent = '✓ ' + files[0].name;
            fileTitle.style.color = 'var(--t1)';
            fileInfo.textContent  = 'Stávající obrázky budou přepsány';
            uploadLabel.classList.add('active');
        } else {
            fileTitle.textContent = '✓ ' + files.length + ' souborů vybráno';
            fileTitle.style.color = 'var(--t1)';
            fileInfo.textContent  = 'Stávající obrázky budou přepsány';
            uploadLabel.classList.add('active');
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
