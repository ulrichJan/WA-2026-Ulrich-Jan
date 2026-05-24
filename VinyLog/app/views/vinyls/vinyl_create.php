<?php
// Vložíme hlavičku (HTML head, navigaci, otevřený <main>)
require_once __DIR__ . '/../layout/header.php';
?>

    <div class="max-w-2xl mx-auto">

        <!-- Odkaz zpět na seznam desek -->
        <div class="mb-6">
            <a class="text-[#344e41] hover:text-[#3a5a40] font-medium transition-colors inline-flex items-center gap-2" href="?action=index">
                ← Zpět na seznam
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-8">
            <div class="mb-8">
                <h2 class="text-3xl font-semibold text-[#344e41]">Přidat vinyl</h2>
                <p class="text-base text-[#344e41]">Vyplňte formulář pro přidání nového vinylu do databáze.</p>
            </div>

            <!-- Formulář pro přidání vinylu.
                 enctype="multipart/form-data" je povinný pro nahrávání souborů (obrázků).
                 action odkazuje na VinylController.php, který zpracuje POST data v metodě store(). -->
            <form action="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php"
                  method="post"
                  enctype="multipart/form-data"
                  class="space-y-6">

                <div class="grid gap-6">

                    <!-- Pole: Název alba (povinné) -->
                    <div>
                        <label for="album_name" class="block text-base font-medium text-[#344e41] mb-2">
                            Název alba <span class="text-[#c1121f]">*</span>
                        </label>
                        <input type="text" id="album_name" name="album_name" required
                               class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-white focus:border-[#588157] focus:ring-2 focus:ring-[#588157]/20 transition-all duration-200">
                    </div>

                    <!-- Pole: Umělec / kapela (povinné) -->
                    <div>
                        <label for="artist" class="block text-base font-medium text-[#344e41] mb-2">
                            Umělec <span class="text-[#c1121f]">*</span>
                        </label>
                        <input type="text" id="artist" name="artist" required
                               class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-white focus:border-[#588157] focus:ring-2 focus:ring-[#588157]/20 transition-all duration-200">
                    </div>

                    <!-- Select: Kategorie (povinné)
                         Hodnoty jsou načteny z DB přes Category::getAllCategories()
                         v controlleru a předány jako $categories do tohoto view. -->
                    <div>
                        <label for="category" class="block text-base font-medium text-[#344e41] mb-2">
                            Kategorie <span class="text-[#c1121f]">*</span>
                        </label>
                        <select id="category" name="category" required
                                class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-white">
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
                    <div>
                        <label for="subcategory" class="block text-base font-medium text-[#344e41] mb-2">
                            Podkategorie
                        </label>
                        <select id="subcategory" name="subcategory"
                                class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-white">
                            <option value="">-- Nejprve vyberte kategorii --</option>
                        </select>
                    </div>

                    <!-- Skupina polí: Rok vydání, Žánr, Cena (3 sloupce na větší obrazovkách) -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label for="release_year" class="block text-base font-medium text-[#344e41] mb-2">Rok vydání</label>
                            <input type="number" id="release_year" name="release_year" min="1900" max="2100"
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-white focus:border-[#588157] focus:ring-2 focus:ring-[#588157]/20 transition-all duration-200">
                        </div>
                        <div>
                            <label for="genre" class="block text-base font-medium text-[#344e41] mb-2">Žánr</label>
                            <input type="text" id="genre" name="genre"
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-white focus:border-[#588157] focus:ring-2 focus:ring-[#588157]/20 transition-all duration-200">
                        </div>
                        <div>
                            <label for="price" class="block text-base font-medium text-[#344e41] mb-2">Cena (Kč)</label>
                            <input type="number" id="price" name="price" step="0.01" min="0"
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-white focus:border-[#588157] focus:ring-2 focus:ring-[#588157]/20 transition-all duration-200">
                        </div>
                    </div>

                    <!-- Pole pro nahrání obrázků alba
                         Skrytý <input type="file"> je aktivován kliknutím na stylovaný <label>.
                         Atribut multiple dovoluje vybrat více souborů najednou.
                         JavaScript níže aktualizuje text po výběru souborů. -->
                    <div>
                        <label class="block text-base font-medium text-[#344e41] mb-3">Obrázky alba</label>
                        <div class="w-full">
                            <label for="album_cover"
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#E3D5CA] rounded-lg cursor-pointer bg-[#F5EBE0] hover:bg-[#EDEDE9] hover:border-[#588157] transition-all duration-200">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <span id="file-title" class="text-base text-[#344e41] font-medium">Klikněte pro výběr souborů</span>
                                    <span id="file-info"  class="text-sm text-[#344e41] mt-2 text-center px-4">Žádné soubory nebyly vybrány</span>
                                </div>
                                <!-- name="album_cover[]" – hranaté závorky signalizují PHP, že se jedná o pole souborů -->
                                <input type="file" id="album_cover" name="album_cover[]" multiple accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Odesílací tlačítko -->
                <div class="pt-6">
                    <button type="submit"
                            class="w-full bg-[#588157] hover:bg-[#3a5a40] text-white font-medium py-3 px-6 rounded-2xl transition-all duration-200 shadow-sm border border-[#588157] focus:ring-2 focus:ring-[#588157]/20">
                        Přidat vinyl
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
    // =========================================================================
    // Dynamické filtrování podkategorií dle vybrané kategorie
    // =========================================================================

    // Všechny podkategorie jsou vyrendeovány PHP do JSON a uloženy v této proměnné.
    // Každý prvek: { id, category_id, name }
    const allSubcategories = <?= json_encode($subcategories ?? []) ?>;

    // Při změně select-boxu kategorií...
    document.getElementById('category').addEventListener('change', function () {
        const categoryId = parseInt(this.value); // ID zvolené kategorie
        const subSelect  = document.getElementById('subcategory');

        // Filtrujeme pole na podkategorie patřící do zvolené kategorie
        const filtered = allSubcategories.filter(s => s.category_id == categoryId);

        // Vyprázdníme stávající obsah select-boxu podkategorií
        subSelect.innerHTML = '';

        // Přidáme výchozí "prázdnou" volbu
        const defaultOption     = document.createElement('option');
        defaultOption.value     = '';
        defaultOption.textContent = filtered.length
            ? '-- Vyberte podkategorii --'
            : '-- Žádné podkategorie --';
        subSelect.appendChild(defaultOption);

        // Dynamicky přidáme nalezené podkategorie jako <option> prvky
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
            // Žádné soubory nevybrány – obnovíme výchozí text
            fileTitle.textContent = 'Klikněte pro výběr souborů';
            fileTitle.className   = 'text-base text-[#344e41] font-medium';
            fileInfo.textContent  = 'Žádné soubory nebyly vybrány';
        } else if (files.length === 1) {
            // Jeden soubor – zobrazíme jeho název
            fileTitle.textContent = 'Soubor připraven';
            fileTitle.className   = 'text-base text-[#344e41] font-medium';
            fileInfo.textContent  = files[0].name;
        } else {
            // Více souborů – zobrazíme počet
            fileTitle.textContent = 'Soubory připraveny';
            fileTitle.className   = 'text-base text-[#344e41] font-medium';
            fileInfo.textContent  = 'Vybráno celkem: ' + files.length + ' souborů';
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
