<?php
// Vložíme hlavičku (HTML head, navigaci, otevřený <main>)
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Odkaz zpět na seznam desek -->
<a class="back-link" href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=index">
    ← Zpět na seznam
</a>

<!-- Záhlaví stránky -->
<div style="margin-bottom:1.75rem;animation: fadeUp 300ms var(--ease-out) both;">
    <h2 style="font-size:1.5rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;margin-bottom:0.3rem;">
        Přidat vinyl
    </h2>
    <p style="font-size:0.875rem;color:var(--t3);">Vyplňte formulář pro přidání nového vinylu do sbírky.</p>
</div>

<!-- Karta formuláře -->
<div class="form-card" style="max-width:680px;animation: fadeUp 340ms var(--ease-out) 60ms both;">

    <!-- Formulář pro přidání vinylu.
         enctype="multipart/form-data" je povinný pro nahrávání souborů (obrázků).
         action odkazuje na VinylController.php, který zpracuje POST data v metodě store(). -->
    <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php"
          method="post"
          enctype="multipart/form-data">

        <div class="form-row">

            <!-- Pole: Název alba (povinné) -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 80ms both;">
                <label for="album_name" class="form-label">
                    Název alba <span class="required">*</span>
                </label>
                <input type="text" id="album_name" name="album_name" required
                       class="form-input" placeholder="Název desky">
            </div>

            <!-- Pole: Umělec / kapela (povinné) -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 120ms both;">
                <label for="artist" class="form-label">
                    Umělec <span class="required">*</span>
                </label>
                <input type="text" id="artist" name="artist" required
                       class="form-input" placeholder="Jméno umělce nebo kapely">
            </div>

            <!-- Select: Kategorie (povinné)
                 Hodnoty jsou načteny z DB přes Category::getAllCategories()
                 v controlleru a předány jako $categories do tohoto view. -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 160ms both;">
                <label for="category" class="form-label">
                    Kategorie <span class="required">*</span>
                </label>
                <select id="category" name="category" required class="form-select">
                    <option value="">-- Vyberte kategorii --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Select: Podkategorie (volitelné)
                 Výchozí stav: "Nejprve vyberte kategorii" a prázdný select.
                 JavaScript níže dynamicky naplní tento select po výběru kategorie
                 filtrováním pole allSubcategories dle category_id. -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 200ms both;">
                <label for="subcategory" class="form-label">Podkategorie</label>
                <select id="subcategory" name="subcategory" class="form-select">
                    <option value="">-- Nejprve vyberte kategorii --</option>
                </select>
            </div>

            <!-- Skupina polí: Rok vydání, Žánr, Cena (3 sloupce) -->
            <div class="form-grid-3" style="animation: fadeUp 0.35s var(--ease-out) 240ms both;">
                <div class="form-group">
                    <label for="release_year" class="form-label">Rok vydání</label>
                    <input type="number" id="release_year" name="release_year"
                           min="1900" max="2100" class="form-input" placeholder="1975">
                </div>
                <div class="form-group">
                    <label for="genre" class="form-label">Žánr</label>
                    <input type="text" id="genre" name="genre"
                           class="form-input" placeholder="Rock, Jazz...">
                </div>
                <div class="form-group">
                    <label for="price" class="form-label">Cena (Kč)</label>
                    <input type="number" id="price" name="price"
                           step="0.01" min="0" class="form-input" placeholder="0.00">
                </div>
            </div>

            <!-- Pole pro nahrání obrázků alba
                 Skrytý <input type="file"> je aktivován kliknutím na stylovaný label.
                 Atribut multiple dovoluje vybrat více souborů najednou.
                 JavaScript níže aktualizuje text po výběru souborů. -->
            <div class="form-group" style="animation: fadeUp 0.35s var(--ease-out) 280ms both;">
                <label class="form-label">Obrázky alba</label>
                <label for="album_cover" class="upload-zone">
                    <span class="upload-zone__title" id="file-title">Klikněte pro výběr souborů</span>
                    <span class="upload-zone__hint"  id="file-info">Žádné soubory nebyly vybrány</span>
                    <!-- name="album_cover[]" – hranaté závorky signalizují PHP, že se jedná o pole souborů -->
                    <input type="file" id="album_cover" name="album_cover[]"
                           multiple accept="image/*">
                </label>
            </div>

            <!-- Odesílací tlačítko -->
            <div style="padding-top:0.5rem;animation: fadeUp 0.35s var(--ease-out) 320ms both;">
                <button type="submit" class="btn btn-cta btn-lg btn-full">
                    Přidat vinyl
                </button>
            </div>

        </div><!-- /.form-row -->
    </form>
</div>

<script>
    // =========================================================================
    // Dynamické filtrování podkategorií dle vybrané kategorie
    // =========================================================================

    // Všechny podkategorie jsou vyrendovány PHP do JSON a uloženy v této proměnné.
    // Každý prvek: { id, category_id, name }
    const allSubcategories = <?= json_encode($subcategories ?? []) ?>;

    // Při změně select-boxu kategorií přefiltrujeme podkategorie
    document.getElementById('category').addEventListener('change', function () {
        const categoryId = parseInt(this.value);
        const subSelect  = document.getElementById('subcategory');

        const filtered = allSubcategories.filter(s => s.category_id == categoryId);

        subSelect.innerHTML = '';

        const defaultOption       = document.createElement('option');
        defaultOption.value       = '';
        defaultOption.textContent = filtered.length
            ? '-- Vyberte podkategorii --'
            : '-- Žádné podkategorie --';
        subSelect.appendChild(defaultOption);

        filtered.forEach(function (sub) {
            const opt       = document.createElement('option');
            opt.value       = sub.id;
            opt.textContent = sub.name;
            subSelect.appendChild(opt);
        });
    });

    // =========================================================================
    // Aktualizace textu po výběru obrázků
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
