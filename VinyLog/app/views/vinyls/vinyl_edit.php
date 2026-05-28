<?php
// Vložíme hlavičku stránky
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Odkaz zpět na seznam -->
<a class="back-link"
   href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=index">
    ← Zpět na seznam vinylů
</a>

<!-- Záhlaví stránky – ID záznamu zobrazeno jako malý badge vedle nadpisu -->
<div style="margin-bottom:1.75rem;animation: fadeUp 300ms var(--ease-out) both;">
    <div style="display:flex;align-items:baseline;gap:0.625rem;margin-bottom:0.3rem;">
        <h2 style="font-size:1.5rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;">
            Upravit vinyl
        </h2>
        <span style="font-size:0.72rem;font-weight:600;letter-spacing:0.05em;color:var(--t3);background:var(--bg-elevated);border:1px solid var(--border);border-radius:5px;padding:0.15em 0.55em;">
            #<?= htmlspecialchars($vinyl['id']) ?>
        </span>
    </div>
    <p style="font-size:0.875rem;color:var(--t3);">
        Upravujete: <strong style="color:var(--t2);"><?= htmlspecialchars($vinyl['album_name']) ?></strong>
    </p>
</div>

<!-- Karta formuláře -->
<div class="form-card" style="max-width:680px;animation: fadeUp 340ms var(--ease-out) 60ms both;">

    <!-- Formulář pro úpravu vinylu.
         ?action=update&id=X → VinylController::update() zpracuje POST.
         enctype="multipart/form-data" je nutný pro nahrávání obrázků. -->
    <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=update&id=<?= htmlspecialchars($vinyl['id']) ?>"
          method="post"
          enctype="multipart/form-data">

        <div class="form-row">

            <!-- Pole: Název alba (povinné, předvyplněno stávající hodnotou) -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 80ms both;">
                <label for="album_name" class="form-label">
                    Název alba <span class="required">*</span>
                </label>
                <input type="text" id="album_name" name="album_name"
                       value="<?= htmlspecialchars($vinyl['album_name']) ?>" required
                       class="form-input">
            </div>

            <!-- Pole: Umělec (povinné) -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 160ms both;">
                <label for="artist" class="form-label">
                    Umělec <span class="required">*</span>
                </label>
                <input type="text" id="artist" name="artist"
                       value="<?= htmlspecialchars($vinyl['artist']) ?>" required
                       class="form-input">
            </div>

            <!-- Select: Kategorie (povinné, předvybráno dle aktuální category_id)
                 Porovnáváme (int) vinyl['category_id'] s (int) cat['id'] – zajistí
                 správné párování i při různém datovém typu (string vs int). -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 200ms both;">
                <label for="category" class="form-label">
                    Kategorie <span class="required">*</span>
                </label>
                <select id="category" name="category" required class="form-select">
                    <option value="">-- Vyberte kategorii --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <?php
                                // Označíme aktuálně přiřazenou kategorii jako selected
                                $isSelected = (isset($vinyl['category_id']) && (int)$vinyl['category_id'] === (int)$cat['id'])
                                    ? 'selected' : '';
                            ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $isSelected ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Select: Podkategorie (volitelné, předvybráno pokud vinyl má subcategory_id)
                 Zobrazíme pouze podkategorie patřící do aktuální kategorie vinylu.
                 JavaScript níže přefiltruje select při změně kategorie. -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 240ms both;">
                <label for="subcategory" class="form-label">Podkategorie</label>
                <select id="subcategory" name="subcategory" class="form-select">
                    <option value="">-- Žádná podkategorie --</option>
                    <?php if (!empty($subcategories)): ?>
                        <?php foreach ($subcategories as $sub): ?>
                            <?php
                                // Zobrazíme pouze podkategorie patřící ke stejné kategorii jako vinyl
                                if ((int)$sub['category_id'] !== (int)($vinyl['category_id'] ?? 0)) continue;
                                // Označíme aktuálně přiřazenou podkategorii
                                $isSubSelected = (isset($vinyl['subcategory_id']) && (int)$vinyl['subcategory_id'] === (int)$sub['id'])
                                    ? 'selected' : '';
                            ?>
                            <option value="<?= htmlspecialchars($sub['id']) ?>" <?= $isSubSelected ?>>
                                <?= htmlspecialchars($sub['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Skupina polí: Rok vydání, Žánr, Cena (předvyplněno) -->
            <div class="form-grid-3" style="animation: fadeUp 0.35s var(--ease-out) 280ms both;">
                <div class="form-group">
                    <label for="release_year" class="form-label">Rok vydání</label>
                    <input type="number" id="release_year" name="release_year"
                           min="1900" max="2100" value="<?= htmlspecialchars($vinyl['release_year']) ?>"
                           class="form-input">
                </div>
                <div class="form-group">
                    <label for="genre" class="form-label">Žánr</label>
                    <input type="text" id="genre" name="genre"
                           value="<?= htmlspecialchars($vinyl['genre']) ?>"
                           class="form-input">
                </div>
                <div class="form-group">
                    <label for="price" class="form-label">Cena (Kč)</label>
                    <input type="number" id="price" name="price"
                           step="0.01" min="0" value="<?= htmlspecialchars($vinyl['price']) ?>"
                           class="form-input">
                </div>
            </div>

            <!-- Sekce obrázků: zobrazení stávajících + možnost nahrát nové -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 320ms both;">
                <label class="form-label">Obrázky alba</label>

                <?php
                    // Dekódujeme JSON pole názvů souborů uložené v DB
                    $existingImages = [];
                    if (!empty($vinyl['album_cover'])) {
                        $existingImages = json_decode($vinyl['album_cover'], true);
                        if (!is_array($existingImages)) {
                            $maybe = trim($vinyl['album_cover']);
                            if (strlen($maybe) > 0 && $maybe[0] === '[') {
                                $decoded        = json_decode($maybe, true);
                                $existingImages = is_array($decoded) ? $decoded : [];
                            } else {
                                $existingImages = [$vinyl['album_cover']];
                            }
                        }
                    }
                ?>

                <?php if (!empty($existingImages)): ?>
                    <!-- Náhled stávajících obrázků -->
                    <div style="margin-bottom:0.875rem;padding:1rem;background:var(--bg-elevated);border-radius:10px;border:1px solid var(--border);">
                        <p style="font-size:0.78rem;font-weight:500;color:var(--t3);margin-bottom:0.75rem;letter-spacing:0.04em;text-transform:uppercase;">
                            Existující obrázky
                        </p>
                        <div class="img-preview-grid">
                            <?php foreach ($existingImages as $img): ?>
                                <?php
                                    // Vypočítáme URL obrázku pro zobrazení v <img>
                                    $img    = trim($img);
                                    $srcImg = $img;
                                    $uploadDir   = realpath(__DIR__ . '/../../public/uploads');
                                    $docRoot     = realpath($_SERVER['DOCUMENT_ROOT']);
                                    $projectRoot = realpath(__DIR__ . '/../../');
                                    $uploadUrlBase = '/public/uploads';

                                    if ($docRoot && $projectRoot && strpos($projectRoot, $docRoot) === 0) {
                                        $projectSub    = str_replace('\\', '/', substr($projectRoot, strlen($docRoot)));
                                        $uploadUrlBase = rtrim($projectSub, '/') . '/public/uploads';
                                    } elseif ($uploadDir && $docRoot && strpos($uploadDir, $docRoot) === 0) {
                                        $uploadUrlBase = str_replace('\\', '/', substr($uploadDir, strlen($docRoot)));
                                    }

                                    if (strpos($srcImg, 'http') !== 0 && strpos($srcImg, '/') !== 0) {
                                        $srcImg = rtrim($uploadUrlBase, '/') . '/' . ltrim($srcImg, '/');
                                    }
                                ?>
                                <div class="img-preview-item">
                                    <img src="<?= htmlspecialchars($srcImg) ?>" alt="obrázek">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="margin-top:0.75rem;font-size:0.78rem;color:var(--t3);font-style:italic;">
                            Pokud nyní nahrajete nové soubory, tyto staré budou přepsány.
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Upload pole pro nové obrázky (volitelné při editaci) -->
                <label for="album_cover" class="upload-zone">
                    <span class="upload-zone__title" id="file-title">Klikněte pro výběr souborů</span>
                    <span class="upload-zone__hint"  id="file-info">Žádné soubory nebyly vybrány</span>
                    <input type="file" id="album_cover" name="album_cover[]"
                           multiple accept="image/*">
                </label>
            </div>

            <!-- Odesílací tlačítko -->
            <div style="padding-top:0.5rem;animation: fadeUp 0.35s var(--ease-out) 360ms both;">
                <button type="submit" class="btn btn-cta btn-lg btn-full">
                    Uložit změny
                </button>
            </div>

        </div><!-- /.form-row -->
    </form>
</div>

<script>
    // =========================================================================
    // Dynamické filtrování podkategorií při změně kategorie (edit formulář)
    // =========================================================================

    // Všechny podkategorie jako JSON (vyrendovány PHP na straně serveru)
    const allSubcategories     = <?= json_encode($subcategories ?? []) ?>;
    // ID aktuálně přiřazené podkategorie (pro obnovení výběru po přefiltrování)
    const currentSubcategoryId = <?= (int)($vinyl['subcategory_id'] ?? 0) ?>;

    document.getElementById('category').addEventListener('change', function () {
        const categoryId = parseInt(this.value);
        const subSelect  = document.getElementById('subcategory');

        const filtered = allSubcategories.filter(s => s.category_id == categoryId);

        subSelect.innerHTML = '';
        const defaultOption       = document.createElement('option');
        defaultOption.value       = '';
        defaultOption.textContent = filtered.length ? '-- Vyberte podkategorii --' : '-- Žádné podkategorie --';
        subSelect.appendChild(defaultOption);

        filtered.forEach(function (sub) {
            const opt       = document.createElement('option');
            opt.value       = sub.id;
            opt.textContent = sub.name;
            subSelect.appendChild(opt);
        });
    });

    // =========================================================================
    // Aktualizace textu po výběru nových obrázků
    // =========================================================================
    const fileInput = document.getElementById('album_cover');
    const fileTitle = document.getElementById('file-title');
    const fileInfo  = document.getElementById('file-info');

    fileInput.addEventListener('change', function(event) {
        const files = event.target.files;
        if (!files || files.length === 0) {
            fileTitle.textContent = 'Klikněte pro výběr souborů';
            fileInfo.textContent  = 'Žádné soubory nebyly vybrány';
        } else if (files.length === 1) {
            fileTitle.textContent = 'Soubor připraven';
            fileInfo.textContent  = files[0].name;
        } else {
            fileTitle.textContent = 'Soubory připraveny';
            fileInfo.textContent  = 'Vybráno: ' + files.length + ' souborů';
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
