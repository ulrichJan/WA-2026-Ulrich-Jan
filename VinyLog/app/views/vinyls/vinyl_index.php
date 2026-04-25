<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
        <h2 class="text-3xl font-light tracking-wide text-[#1a1c1e]">Dostupné vinylové desky</h2>
        <a class="bg-[#D5BDAF] hover:bg-[#F5EBE0] text-[#1a1c1e] px-6 py-3 rounded-lg font-medium shadow-sm transition-all duration-200 border border-[#D5BDAF]" href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=create">+ Přidat nový vinyl</a>
    </div>

    <div>
        <?php if (empty($vinyls)): ?>
            <div class="p-12 text-center text-[#344e41]">
                <p class="text-lg italic">V databázi se zatím nenachází žádné vinylové desky.</p>
                <p class="text-sm mt-2">Začněte přidáním prvního záznamu.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($vinyls as $vinyl): ?>
                    <?php
                        $images = json_decode($vinyl['album_cover'], true);
                        $mainImage = null;
                        if (is_array($images) && count($images) > 0) {
                            $mainImage = $images[0];
                        } elseif (!empty($vinyl['album_cover'])) {
                            $mainImage = $vinyl['album_cover'];
                        }
                        if ($mainImage) {
                            $imgSrc = $mainImage;
                            if (strpos($imgSrc, '/') === false && strpos($imgSrc, 'http') === false) {
                                $imgSrc = '/WA-2026-Ulrich-Jan/VinyLog/public/uploads/' . $imgSrc;
                            }
                            $imgSrc = htmlspecialchars($imgSrc);
                        } else {
                            $imgSrc = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 400'><rect width='100%25' height='100%25' fill='%23ffffff'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23344e41' font-family='Arial, sans-serif' font-size='24'>No image</text></svg>";
                        }
                        $albumName = htmlspecialchars($vinyl['album_name']);
                        $artist = htmlspecialchars($vinyl['artist']);
                        $showUrl = '?action=show&id=' . urlencode($vinyl['id']);
                    ?>

                    <article class="bg-white rounded-2xl shadow-sm hover:shadow-lg transform hover:scale-105 transition duration-200 overflow-hidden">
                        <a href="<?= $showUrl ?>" class="block group relative">
                            <div class="w-full h-56 md:h-48 lg:h-56 bg-gray-100 overflow-hidden">
                                <img src="<?= $imgSrc ?>" alt="<?= $albumName ?>" class="w-full h-full object-cover">
                            </div>

                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-[#344e41] truncate"><?= $albumName ?></h3>
                                <p class="text-sm text-[#3a5a40] mt-1 truncate"><?= $artist ?></p>
                            </div>

                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-[rgba(88,129,87,0.10)]">
                                <span class="px-4 py-2 bg-[#588157] text-white rounded-full text-sm font-medium">View detail</span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
