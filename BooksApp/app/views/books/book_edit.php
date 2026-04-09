<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
    <div class="bg-white border border-[#f6e6da] rounded-xl overflow-hidden shadow-lg p-8">
        <p><a class="text-[#c1121f] font-semibold" href="BookController.php?action=index">&larr; Zpět na seznam knih</a></p>
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-[#6b291f]">Upravit knihu (ID záznamu: <?= htmlspecialchars($book['id']) ?>)</h2>
            <p class="text-[#6b291f]">Upravujete data pro knihu: <strong><?= htmlspecialchars($book['title']) ?></strong></p>
            <p class="text-sm text-[#6b291f]">Změňte požadované údaje a uložte formulář.</p>
        </div>

        <form action="BookController.php?action=update&id=<?= htmlspecialchars($book['id']) ?>" method="post" enctype="multipart/form-data" class="grid gap-4">
            <div>
                <label for="id_display" class="block text-sm text-[#6b291f]">ID v databázi</label>
                <input type="text" id="id_display" value="<?= htmlspecialchars($book['id']) ?>" readonly class="w-full p-3 rounded-md border border-[#f0ded5]">
            </div>

            <div>
                <label for="title" class="text-sm font-medium">Název knihy <span class="text-[#c1121f]">*</span></label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required class="w-full p-3 rounded-md border border-[#f0ded5]">
            </div>

            <div>
                <label for="author" class="text-sm font-medium">Autor <span class="text-[#c1121f]">*</span></label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" required class="w-full p-3 rounded-md border border-[#f0ded5]">
            </div>

            <div>
                <label for="isbn" class="text-sm font-medium">ISBN</label>
                <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" class="w-full p-3 rounded-md border border-[#f0ded5]">
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="published_date" class="text-sm font-medium">Datum vydání</label>
                    <input type="date" id="published_date" name="published_date" value="<?= htmlspecialchars($book['published_date']) ?>" class="w-full p-3 rounded-md border border-[#f0ded5]">
                </div>
                <div>
                    <label for="price" class="text-sm font-medium">Cena knihy</label>
                    <input type="number" id="price" name="price" step="0.5" value="<?= htmlspecialchars($book['price']) ?>" class="w-full p-3 rounded-md border border-[#f0ded5]">
                </div>
            </div>

            <div>
                <label for="description" class="text-sm font-medium">Popis</label>
                <textarea id="description" name="description" rows="5" class="w-full p-3 rounded-md border border-[#f0ded5]"><?= htmlspecialchars($book['description']) ?></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-[#6b291f] mb-2 uppercase tracking-wider">Obrázky knihy</label>

                <?php
                    // Show existing images (if any)
                    $existingImages = [];
                    if (!empty($book['images'])) {
                        $existingImages = json_decode($book['images'], true);
                        if (!is_array($existingImages)) {
                            $existingImages = [$book['images']];
                        }
                    }
                ?>

                <?php if (!empty($existingImages)): ?>
                    <div class="mb-4">
                        <p class="text-sm font-medium text-[#6b291f] mb-2">Existující obrázky:</p>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <?php foreach ($existingImages as $img): ?>
                                <?php
                                    $srcImg = $img;
                                    if (strpos($srcImg, '/') === false && strpos($srcImg, 'http') === false) {
                                        $srcImg = '/uploads/' . $srcImg;
                                    }
                                ?>
                                <span class="px-2 py-1 text-xs bg-[#f6e6da] text-[#6b291f] rounded-md"><?= htmlspecialchars(basename($img)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="mb-4 grid grid-cols-3 gap-3">
                            <?php foreach ($existingImages as $img): ?>
                                <?php
                                    $srcImg = $img;
                                    if (strpos($srcImg, '/') === false && strpos($srcImg, 'http') === false) {
                                        $srcImg = '/uploads/' . $srcImg;
                                    }
                                ?>
                                <div class="border rounded-md overflow-hidden">
                                    <img src="<?= htmlspecialchars($srcImg) ?>" alt="obrazek" style="width:100%;height:120px;object-fit:cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-sm text-[#c1121f] italic">Upozornění: Pokud nyní nahrajete nové soubory, tyto staré budou přepsány.</p>
                    </div>
                <?php endif; ?>

                <div class="w-full">
                    <label for="images" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed rounded-lg cursor-pointer bg-white/5 hover:bg-white/10 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-3 pb-2">
                            <span id="file-title" class="text-sm text-[#6b291f] font-semibold">Klikni pro výběr souborů</span>
                            <span id="file-info" class="text-xs text-[#6b291f] mt-1 text-center px-4">Žádné soubory nebyly vybrány</span>
                        </div>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" class="bg-[#c1121f] hover:bg-[#930f1b] text-[#fdf0d5] px-4 py-2 rounded-md font-bold">Uložit změny do DB</button>
            </div>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('images');
        const fileTitle = document.getElementById('file-title');
        const fileInfo = document.getElementById('file-info');

        fileInput.addEventListener('change', function(event) {
            const files = event.target.files;

            if (!files || files.length === 0) {
                fileTitle.textContent = 'Klikněte pro výběr souborů';
                fileTitle.className = 'text-sm text-[#6b291f] font-semibold';
                fileInfo.textContent = 'Žádné soubory nebyly vybrány';
            } else if (files.length === 1) {
                fileTitle.textContent = 'Soubor připraven';
                fileTitle.className = 'text-sm text-[#c1121f] font-bold';
                fileInfo.textContent = files[0].name;
            } else {
                fileTitle.textContent = 'Soubory připraveny';
                fileTitle.className = 'text-sm text-[#c1121f] font-bold';
                fileInfo.textContent = 'Vybráno celkem: ' + files.length + ' souborů';
            }
        });
    </script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
