<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
    <div class="bg-white border border-[#f6e6da] rounded-xl overflow-hidden shadow-lg p-8">
        <h2 class="text-2xl font-semibold mb-2 text-[#6b291f]">Přidat knihu</h2>
        <p class="text-[#6b291f] mb-6">Vyplňte formulář pro přidání nové knihy do databáze.</p>

        <form action="../../controllers/BookController.php" method="post" enctype="multipart/form-data" class="grid gap-4">
            <div class="field">
                <label for="title" class="text-sm font-medium">Název knihy <span class="text-[#c1121f]">*</span></label>
                <input type="text" id="title" name="title" required class="w-full p-3 rounded-md border border-[#f0ded5]">
            </div>

            <div class="field">
                <label for="author" class="text-sm font-medium">Autor <span class="text-[#c1121f]">*</span></label>
                <input type="text" id="author" name="author" required class="w-full p-3 rounded-md border border-[#f0ded5]">
            </div>

            <div class="field">
                <label for="isbn" class="text-sm font-medium">ISBN <span class="text-[#c1121f]">*</span></label>
                <input type="text" id="isbn" name="isbn" required class="w-full p-3 rounded-md border border-[#f0ded5]">
            </div>

            <div class="field grid md:grid-cols-2 gap-4">
                <div>
                    <label for="published_date" class="text-sm font-medium">Datum vydání</label>
                    <input type="date" id="published_date" name="published_date" class="w-full p-3 rounded-md border border-[#f0ded5]">
                </div>
                <div>
                    <label for="price" class="text-sm font-medium">Cena knihy</label>
                    <input type="number" id="price" name="price" step="0.5" class="w-full p-3 rounded-md border border-[#f0ded5]">
                </div>
            </div>

            <div class="field">
                <label for="description" class="text-sm font-medium">Popis</label>
                <textarea id="description" name="description" rows="5" class="w-full p-3 rounded-md border border-[#f0ded5]"></textarea>
            </div>

            <div class="field md:col-span-2">
                <label class="block text-xs font-semibold text-[#6b291f] mb-2 uppercase tracking-wider">Obrázky knihy</label>
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
                <button type="submit" class="bg-[#c1121f] hover:bg-[#930f1b] text-[#fdf0d5] px-4 py-2 rounded-md font-bold">Přidat knihu</button>
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