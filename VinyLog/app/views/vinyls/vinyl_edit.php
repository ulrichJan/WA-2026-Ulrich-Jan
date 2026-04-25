<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
        <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a class="text-[#1a1c1e] hover:text-[#1a1c1e] font-medium transition-colors inline-flex items-center gap-2" href="VinylController.php?action=index">
                ← Zpět na seznam vinylů
            </a>
        </div>

        <div class="bg-[#D6CCC2] border border-[#E3D5CA] rounded-xl shadow-sm p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-[#1a1c1e]">Upravit vinyl</h2>
                <p class="text-[#1a1c1e]">Upravujete data pro vinyl: <strong class="text-[#1a1c1e]"><?= htmlspecialchars($vinyl['album_name']) ?></strong></p>
                <p class="text-sm text-[#1a1c1e] mt-2">Změňte požadované údaje a uložte formulář.</p>
            </div>

            <form action="VinylController.php?action=update&id=<?= htmlspecialchars($vinyl['id']) ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                <div class="bg-[#F5EBE0] rounded-lg p-4">
                    <label for="id_display" class="block text-sm font-medium text-[#1a1c1e] mb-2">ID v databázi</label>
                    <input type="text" id="id_display" value="<?= htmlspecialchars($vinyl['id']) ?>" readonly 
                           class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#EDEDE9] text-[#1a1c1e]">
                </div>

                <div class="grid gap-6">
                    <div>
                        <label for="album_name" class="block text-sm font-medium text-[#1a1c1e] mb-2">Název alba <span class="text-[#D5BDAF]">*</span></label>
                        <input type="text" id="album_name" name="album_name" value="<?= htmlspecialchars($vinyl['album_name']) ?>" required 
                               class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                    </div>

                    <div>
                        <label for="artist" class="block text-sm font-medium text-[#1a1c1e] mb-2">Umělec <span class="text-[#D5BDAF]">*</span></label>
                        <input type="text" id="artist" name="artist" value="<?= htmlspecialchars($vinyl['artist']) ?>" required 
                               class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label for="release_year" class="block text-sm font-medium text-[#1a1c1e] mb-2">Rok vydání</label>
                            <input type="number" id="release_year" name="release_year" min="1900" max="2100" value="<?= htmlspecialchars($vinyl['release_year']) ?>" 
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                        </div>
                        <div>
                            <label for="genre" class="block text-sm font-medium text-[#1a1c1e] mb-2">Žánr</label>
                            <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($vinyl['genre']) ?>" 
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-[#1a1c1e] mb-2">Cena (Kč)</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($vinyl['price']) ?>" 
                                   class="w-full p-3 rounded-lg border border-[#E3D5CA] bg-[#F5EBE0] focus:border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20 transition-all duration-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#1a1c1e] mb-3">Obrázky alba</label>

                        <?php
                            // Show existing images (if any)
                            $existingImages = [];
                            if (!empty($vinyl['album_cover'])) {
                                $existingImages = json_decode($vinyl['album_cover'], true);
                                if (!is_array($existingImages)) {
                                    $existingImages = [$vinyl['album_cover']];
                                }
                            }
                        ?>

                        <?php if (!empty($existingImages)): ?>
                            <div class="mb-6 p-4 bg-[#EDEDE9] rounded-lg border border-[#E3D5CA]">
                                <p class="text-sm font-medium text-[#1a1c1e] mb-3">Existující obrázky:</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                                    <?php foreach ($existingImages as $img): ?>
                                        <?php
                                            $srcImg = $img;
                                            if (strpos($srcImg, '/') === false && strpos($srcImg, 'http') === false) {
                                                $srcImg = '/WA-2026-Ulrich-Jan/VinyLog/public/uploads/' . $srcImg;
                                            }
                                        ?>
                                        <div class="aspect-square rounded-lg overflow-hidden border border-[#E3D5CA]">
                                            <img src="<?= htmlspecialchars($srcImg) ?>" alt="obrázek" class="w-full h-full object-cover">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <p class="text-sm text-[#D5BDAF] italic">Upozornění: Pokud nyní nahrajete nové soubory, tyto staré budou přepsány.</p>
                            </div>
                        <?php endif; ?>

                        <div class="w-full">
                            <label for="album_cover" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#E3D5CA] rounded-lg cursor-pointer bg-[#F5EBE0] hover:bg-[#EDEDE9] hover:border-[#D5BDAF] transition-all duration-200">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <span id="file-title" class="text-sm text-[#D5BDAF] font-medium">Klikněte pro výběr souborů</span>
                                    <span id="file-info" class="text-xs text-[#1a1c1e] mt-2 text-center px-4">Žádné soubory nebyly vybrány</span>
                                </div>
                                <input type="file" id="album_cover" name="album_cover[]" multiple accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-[#D5BDAF] hover:bg-[#F5EBE0] text-[#1a1c1e] font-medium py-3 px-6 rounded-lg transition-all duration-200 shadow-sm border border-[#D5BDAF] focus:ring-2 focus:ring-[#D5BDAF]/20">
                        Uložit změny
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    const fileInput = document.getElementById('album_cover');
    const fileTitle = document.getElementById('file-title');
    const fileInfo = document.getElementById('file-info');

    fileInput.addEventListener('change', function(event) {
        const files = event.target.files;

        if (!files || files.length === 0) {
            fileTitle.textContent = 'Klikněte pro výběr souborů';
            fileTitle.className = 'text-sm text-[#D5BDAF] font-medium';
            fileInfo.textContent = 'Žádné soubory nebyly vybrány';
        } else if (files.length === 1) {
            fileTitle.textContent = 'Soubor připraven';
            fileTitle.className = 'text-sm text-[#1a1c1e] font-medium';
            fileInfo.textContent = files[0].name;
        } else {
            fileTitle.textContent = 'Soubory připraveny';
            fileTitle.className = 'text-sm text-[#1a1c1e] font-medium';
            fileInfo.textContent = 'Vybráno celkem: ' + files.length + ' souborů';
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
