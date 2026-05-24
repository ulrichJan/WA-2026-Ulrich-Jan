<?php
// Vložíme hlavičku stránky
require_once __DIR__ . '/../layout/header.php';
?>

    <div class="max-w-2xl mx-auto">

        <!-- Odkaz zpět na seznam -->
        <div class="mb-6">
            <a class="text-[#1a1c1e] hover:text-[#1a1c1e] font-medium transition-colors inline-flex items-center gap-2"
               href="VinylController.php?action=index">
                ← Zpět na seznam vinylů
            </a>
        </div>

        <div class="bg-[#D6CCC2] border border-[#E3D5CA] rounded-xl shadow-sm p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-[#1a1c1e]">Upravit vinyl</h2>
                <p class="text-[#1a1c1e]">Upravujete data pro vinyl:
                    <strong class="text-[#1a1c1e]"><?= htmlspecialchars($vinyl['album_name']) ?></strong>
                </p>
                <p class="text-sm text-[#1a1c1e] mt-2">Změňte požadované údaje a uložte formulář.</p>
            </div>

            <!-- Formulář pro úpravu vinylu.
                 ?action=update&id=X → VinylController::update() zpracuje POST.
                 enctype="multipart/form-data" je nutný pro nahrávání obrázků. -->
            <form action="VinylController.php?action=update&id=<?= htmlspecialchars($vinyl['id']) ?>"
                  method="post"
                  enctype="multipart/form-data"
                  class="space-y-6">

                <!-- Zobrazení ID záznamu (read-only) – uživatel ho nemůže měnit -->
                <div class="bg-[#F5EBE0] rounded-lg p-4">
                    <label for="id_display" class="block text-sm font-medium text-[#1a1c1e] mb-2">ID v databázi</label>
                    <input type="text" id="id_display" value="<?= htmlspecialchars($vinyl['id']) ?>" readonly
                           class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#EDEDE9] text-[#1a1c1e]">
                </div>

                <div class="grid gap-6">

                    <!-- Pole: Název alba (povinné, předvyplněno stávající hodnotou) -->
                    <div>
                        <label for="album_name" class="block text-sm font-medium text-[#1a1c1e] mb-2">
                            Název alba <span class="text-[#D5BDAF]">*</span>
                        </label>
                        <input type="text" id="album_name" name="album_name"
                               value="<?= htmlspecialchars($vinyl['album_name']) ?>" required
                               class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                    </div>

                    <!-- Pole: Umělec (povinné) -->
                    <div>
                        <label for="artist" class="block text-sm font-medium text-[#1a1c1e] mb-2">
                            Umělec <span class="text-[#D5BDAF]">*</span>
                        </label>
                        <input type="text" id="artist" name="artist"
                               value="<?= htmlspecialchars($vinyl['artist']) ?>" required
                               class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                    </div>

                    <!-- Select: Kategorie (povinné, předvybráno dle aktuální category_id)
                         Porovnáváme (int) vinyl['category_id'] s (int) cat['id'] – zajistí
                         správné párování i při různém datovém typu (string vs int). -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-[#1a1c1e] mb-2">
                            Kategorie <span class="text-[#D5BDAF]">*</span>
                        </label>
                        <select id="category" name="category" required
                                class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0]">
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
                    <div>
                        <label for="subcategory" class="block text-sm font-medium text-[#1a1c1e] mb-2">
                            Podkategorie
                        </label>
                        <select id="subcategory" name="subcategory"
                                class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0]">
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
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label for="release_year" class="block text-sm font-medium text-[#1a1c1e] mb-2">Rok vydání</label>
                            <input type="number" id="release_year" name="release_year"
                                   min="1900" max="2100" value="<?= htmlspecialchars($vinyl['release_year']) ?>"
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                        </div>
                        <div>
                            <label for="genre" class="block text-sm font-medium text-[#1a1c1e] mb-2">Žánr</label>
                            <input type="text" id="genre" name="genre"
                                   value="<?= htmlspecialchars($vinyl['genre']) ?>"
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-[#1a1c1e] mb-2">Cena (Kč)</label>
                            <input type="number" id="price" name="price"
                                   step="0.01" min="0" value="<?= htmlspecialchars($vinyl['price']) ?>"
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                        </div>
                    </div>

                    <!-- Sekce obrázků: zobrazení stávajících + možnost nahrát nové -->
                    <div>
                        <label class="block text-sm font-medium text-[#1a1c1e] mb-3">Obrázky alba</label>

                        <?php
                            // Dekódujeme JSON pole názvů souborů uložené v DB
                            $existingImages = [];
                            if (!empty($vinyl['album_cover'])) {
                                $existingImages = json_decode($vinyl['album_cover'], true);
                                if (!is_array($existingImages)) {
                                    // Fallback: pokud album_cover není validní JSON
                                    $maybe = trim($vinyl['album_cover']);
                                    if (strlen($maybe) > 0 && $maybe[0] === '[') {
                                        $decoded = json_decode($maybe, true);
                                        $existingImages = is_array($decoded) ? $decoded : [];
                                    } else {
                                        $existingImages = [$vinyl['album_cover']];
                                    }
                                }
                            }
                        ?>

                        <?php if (!empty($existingImages)): ?>
                            <!-- Náhled stávajících obrázků -->
                            <div class="mb-6 p-4 bg-[#EDEDE9] rounded-lg border border-[#E3D5CA]">
                                <p class="text-sm font-medium text-[#1a1c1e] mb-3">Existující obrázky:</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                                    <?php foreach ($existingImages as $img): ?>
                                        <?php
                                            // Vypočítáme URL obrázku pro zobrazení v <img>
                                            $img    = trim($img);
                                            $srcImg = $img;
                                            $uploadDir  = realpath(__DIR__ . '/../../public/uploads');
                                            $docRoot    = realpath($_SERVER['DOCUMENT_ROOT']);
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
                                        <div class="aspect-square rounded-lg overflow-hidden border border-[#E3D5CA]">
                                            <img src="<?= htmlspecialchars($srcImg) ?>" alt="obrázek" class="w-full h-full object-cover">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <p class="text-sm text-[#D5BDAF] italic">
                                    Upozornění: Pokud nyní nahrajete nové soubory, tyto staré budou přepsány.
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- Upload pole pro nové obrázky (volitelné při editaci) -->
                        <div class="w-full">
                            <label for="album_cover"
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#E3D5CA] rounded-lg cursor-pointer bg-[#F5EBE0] hover:bg-[#EDEDE9] hover:border-[#D5BDAF] transition-all duration-200">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <span id="file-title" class="text-sm text-[#D5BDAF] font-medium">Klikněte pro výběr souborů</span>
                                    <span id="file-info"  class="text-xs text-[#1a1c1e] mt-2 text-center px-4">Žádné soubory nebyly vybrány</span>
                                </div>
                                <input type="file" id="album_cover" name="album_cover[]" multiple accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Odesílací tlačítko -->
                <div class="pt-6">
                    <button type="submit"
                            class="w-full bg-[#D5BDAF] hover:bg-[#F5EBE0] text-[#1a1c1e] font-medium py-3 px-6 rounded-lg transition-all duration-200 shadow-sm border border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20">
                        Uložit změny
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
    // =========================================================================
    // Dynamické filtrování podkategorií při změně kategorie (edit formulář)
    // =========================================================================

    // Všechny podkategorie jako JSON (vyrendeovány PHP na straně serveru)
    const allSubcategories      = <?= json_encode($subcategories ?? []) ?>;
    // ID aktuálně přiřazené podkategorie (pro obnovení výběru po přefiltrování)
    const currentSubcategoryId  = <?= (int)($vinyl['subcategory_id'] ?? 0) ?>;

    document.getElementById('category').addEventListener('change', function () {
        const categoryId = parseInt(this.value);
        const subSelect  = document.getElementById('subcategory');

        // Filtrujeme podkategorie dle zvolené kategorie
        const filtered = allSubcategories.filter(s => s.category_id == categoryId);

        // Přestavíme obsah select-boxu
        subSelect.innerHTML = '';
        const defaultOption      = document.createElement('option');
        defaultOption.value      = '';
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
            fileTitle.className   = 'text-sm text-[#D5BDAF] font-medium';
            fileInfo.textContent  = 'Žádné soubory nebyly vybrány';
        } else if (files.length === 1) {
            fileTitle.textContent = 'Soubor připraven';
            fileTitle.className   = 'text-sm text-[#1a1c1e] font-medium';
            fileInfo.textContent  = files[0].name;
        } else {
            fileTitle.textContent = 'Soubory připraveny';
            fileTitle.className   = 'text-sm text-[#1a1c1e] font-medium';
            fileInfo.textContent  = 'Vybráno celkem: ' + files.length + ' souborů';
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
