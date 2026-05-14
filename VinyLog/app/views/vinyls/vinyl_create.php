<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-content" style="max-width:680px;margin:0 auto;">

    <div style="margin-bottom:24px;">
        <a class="back-link" href="?action=index">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Zpět na seznam
        </a>
    </div>

    <div class="form-card">
        <div style="margin-bottom:32px;">
            <h2 style="font-size:1.6rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;margin-bottom:6px;">Přidat vinyl</h2>
            <p style="font-size:14px;color:var(--t2);">Vyplňte formulář pro přidání nového vinylu do sbírky.</p>
        </div>

        <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php"
              method="post" enctype="multipart/form-data">

            <div style="display:flex;flex-direction:column;gap:22px;">

                <!-- Album name -->
                <div style="animation: fadeUp 0.4s var(--ease-out) 60ms both;">
                    <label for="album_name" class="form-label">
                        Název alba <span style="color:var(--red);">*</span>
                    </label>
                    <input type="text" id="album_name" name="album_name" required
                           class="input-field" placeholder="např. Dark Side of the Moon">
                </div>

                <!-- Artist -->
                <div style="animation: fadeUp 0.4s var(--ease-out) 100ms both;">
                    <label for="artist" class="form-label">
                        Umělec <span style="color:var(--red);">*</span>
                    </label>
                    <input type="text" id="artist" name="artist" required
                           class="input-field" placeholder="např. Pink Floyd">
                </div>

                <!-- Category + Subcategory -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;animation: fadeUp 0.4s var(--ease-out) 140ms both;">
                    <div>
                        <label for="category" class="form-label">
                            Kategorie <span style="color:var(--red);">*</span>
                        </label>
                        <select id="category" name="category" required class="input-field">
                            <option value="">-- Vyberte --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="subcategory" class="form-label">Podkategorie</label>
                        <select id="subcategory" name="subcategory" class="input-field">
                            <option value="">-- Nejprve vyberte kategorii --</option>
                        </select>
                    </div>
                </div>

                <!-- Year / Genre / Price -->
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;animation: fadeUp 0.4s var(--ease-out) 180ms both;">
                    <div>
                        <label for="release_year" class="form-label">Rok vydání</label>
                        <input type="number" id="release_year" name="release_year"
                               min="1900" max="2100" class="input-field" placeholder="2024">
                    </div>
                    <div>
                        <label for="genre" class="form-label">Žánr</label>
                        <input type="text" id="genre" name="genre"
                               class="input-field" placeholder="Rock">
                    </div>
                    <div>
                        <label for="price" class="form-label">Cena (Kč)</label>
                        <input type="number" id="price" name="price"
                               step="0.01" min="0" class="input-field" placeholder="499">
                    </div>
                </div>

                <!-- File upload -->
                <div style="animation: fadeUp 0.4s var(--ease-out) 220ms both;">
                    <label class="form-label">Obrázky alba</label>
                    <label for="album_cover" class="upload-zone" id="upload-label">
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" style="color:var(--t3);margin-bottom:4px;">
                            <path d="M14 18V8M10 12l4-4 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 20a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span id="file-title" style="font-size:14px;font-weight:600;color:var(--t2);">Klikněte pro výběr souborů</span>
                        <span id="file-info" style="font-size:12px;color:var(--t3);text-align:center;">JPG, PNG, WEBP &mdash; více souborů najednou</span>
                        <input type="file" id="album_cover" name="album_cover[]" multiple accept="image/*" class="hidden" style="display:none;">
                    </label>
                </div>

                <!-- Submit -->
                <div style="padding-top:8px;animation: fadeUp 0.4s var(--ease-out) 260ms both;">
                    <button type="submit" class="btn btn-gold" style="width:100%;padding:13px 24px;font-size:15px;">
                        Přidat vinyl
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    const allSubcategories = <?= json_encode($subcategories ?? []) ?>;

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
            subSelect.appendChild(opt);
        });
    });

    const fileInput  = document.getElementById('album_cover');
    const fileTitle  = document.getElementById('file-title');
    const fileInfo   = document.getElementById('file-info');
    const uploadLabel = document.getElementById('upload-label');

    fileInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (!files || files.length === 0) {
            fileTitle.textContent = 'Klikněte pro výběr souborů';
            fileTitle.style.color = 'var(--t2)';
            fileInfo.textContent  = 'JPG, PNG, WEBP — více souborů najednou';
            uploadLabel.classList.remove('active');
        } else if (files.length === 1) {
            fileTitle.textContent = '✓ ' + files[0].name;
            fileTitle.style.color = 'var(--t1)';
            fileInfo.textContent  = 'Soubor připraven k nahrání';
            uploadLabel.classList.add('active');
        } else {
            fileTitle.textContent = '✓ ' + files.length + ' souborů vybráno';
            fileTitle.style.color = 'var(--t1)';
            fileInfo.textContent  = 'Soubory připraveny k nahrání';
            uploadLabel.classList.add('active');
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
