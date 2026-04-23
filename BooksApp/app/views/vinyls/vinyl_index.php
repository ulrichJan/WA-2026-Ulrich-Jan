<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
        <h2 class="text-3xl font-light tracking-wide text-[#1a1c1e]">Dostupné vinylové desky</h2>
        <a class="bg-[#D5BDAF] hover:bg-[#F5EBE0] text-[#1a1c1e] px-6 py-3 rounded-lg font-medium shadow-sm transition-all duration-200 border border-[#D5BDAF]" href="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/VinylController.php?action=create">+ Přidat nový vinyl</a>
    </div>

    <div class="bg-[#D6CCC2] border border-[#E3D5CA] rounded-xl overflow-hidden shadow-sm">
        <?php if (empty($vinyls)): ?>
            <div class="p-12 text-center text-[#1a1c1e]">
                <p class="text-lg italic">V databázi se zatím nenachází žádné vinylové desky.</p>
                <p class="text-sm mt-2">Začněte přidáním prvního záznamu.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#F5EBE0] text-[#1a1c1e]">
                        <tr>
                            <th class="p-4 font-medium">ID</th>
                            <th class="p-4 font-medium">Název alba</th>
                            <th class="p-4 font-medium">Umělec</th>
                            <th class="p-4 font-medium">Rok vydání</th>
                            <th class="p-4 font-medium">Žánr</th>
                            <th class="p-4 font-medium">Cena</th>
                            <th class="p-4 font-medium">Obrázky</th>
                            <th class="p-4 font-medium">Akce</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3D5CA]">
                        <?php foreach ($vinyls as $vinyl): ?>
                            <tr class="hover:bg-[#F5EBE0] transition-colors duration-150">
                            <td class="p-4 text-[#1a1c1e]"><?= htmlspecialchars($vinyl['id']) ?></td>
                            <td class="p-4 text-[#1a1c1e] font-medium"><?= htmlspecialchars($vinyl['album_name']) ?></td>
                            <td class="p-4 text-[#1a1c1e]"><?= htmlspecialchars($vinyl['artist']) ?></td>
                            <td class="p-4 text-[#1a1c1e]"><?= htmlspecialchars($vinyl['release_year']) ?></td>
                            <td class="p-4 text-[#1a1c1e]"><?= htmlspecialchars($vinyl['genre']) ?></td>
                            <td class="p-4 text-[#1a1c1e]"><?= htmlspecialchars($vinyl['price']) ?> Kč</td>
                                <td class="p-4 text-[#1a1c1e]">
                                    <?php
                                        $images = json_decode($vinyl['album_cover'], true);
                                        if (is_array($images)) {
                                            $count = count($images);
                                        } elseif (!empty($vinyl['album_cover'])) {
                                            $count = 1;
                                        } else {
                                            $count = 0;
                                        }
                                        echo htmlspecialchars($count) . ' obrázek' . ($count !== 1 ? 'ů' : '');
                                    ?>
                                </td>
                                <td class="p-4">
                                    <div class="flex gap-3">
                                        <a href="?action=show&id=<?= htmlspecialchars($vinyl['id']) ?>" class="text-[#1a1c1e] hover:text-[#1a1c1e] font-medium transition-colors">Detail</a>
                                        <?php if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)($vinyl['created_by'] ?? 0)): ?>
                                            <a href="?action=edit&id=<?= htmlspecialchars($vinyl['id']) ?>" class="text-[#1a1c1e] hover:text-[#1a1c1e] font-medium transition-colors">Upravit</a>
                                            <a href="?action=delete&id=<?= htmlspecialchars($vinyl['id']) ?>" onclick="return confirm('Opravdu chcete tento vinyl smazat?')" class="text-[#1a1c1e] hover:text-[#1a1c1e] font-medium transition-colors">Smazat</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
