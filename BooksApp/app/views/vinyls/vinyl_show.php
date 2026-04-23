<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
        <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a class="text-[#1a1c1e] hover:text-[#1a1c1e] font-medium transition-colors inline-flex items-center gap-2" href="VinylController.php?action=index">
                ← Zpět na seznam vinylů
            </a>
        </div>

        <div class="bg-[#D6CCC2] border border-[#E3D5CA] rounded-xl shadow-sm overflow-hidden">
            <div class="p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-[#1a1c1e] mb-2">Detail vinylu</h2>
                    <p class="text-[#1a1c1e]">Prohlédněte si údaje o vinylu <strong class="text-[#1a1c1e]"><?= htmlspecialchars($vinyl['album_name']) ?></strong>.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="bg-[#F5EBE0] rounded-lg p-6">
                            <h3 class="text-lg font-medium text-[#1a1c1e] mb-4">Informace o albu</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-[#1a1c1e]">ID</dt>
                                    <dd class="text-[#1a1c1e] mt-1">#<?= htmlspecialchars($vinyl['id']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#1a1c1e]">Název alba</dt>
                                    <dd class="text-[#1a1c1e] mt-1 font-medium"><?= htmlspecialchars($vinyl['album_name']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#1a1c1e]">Umělec</dt>
                                    <dd class="text-[#1a1c1e] mt-1"><?= htmlspecialchars($vinyl['artist']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#1a1c1e]">Rok vydání</dt>
                                    <dd class="text-[#1a1c1e] mt-1"><?= htmlspecialchars($vinyl['release_year']) ?: 'Nezadáno' ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#1a1c1e]">Žánr</dt>
                                    <dd class="text-[#1a1c1e] mt-1"><?= htmlspecialchars($vinyl['genre']) ?: 'Nezadáno' ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#1a1c1e]">Cena</dt>
                                    <dd class="text-[#1a1c1e] mt-1 font-medium"><?= htmlspecialchars($vinyl['price']) ?> Kč</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-[#F5EBE0] rounded-lg p-6">
                            <h3 class="text-lg font-medium text-[#1a1c1e] mb-4">Obrázky alba</h3>
                            <div class="images-list">
                                <?php
                                    $images = json_decode($vinyl['album_cover'], true);
                                    if (is_array($images) && count($images) > 0):
                                        echo '<div class="grid grid-cols-2 gap-3">';
                                        foreach ($images as $image):
                                            $img = $image;
                                            if (strpos($img, '/') === false && strpos($img, 'http') === false) {
                                                $img = '/uploads/' . $img;
                                            }
                                            $src = htmlspecialchars($img);
                                            echo '<div class="aspect-square rounded-lg overflow-hidden border border-[#E3D5CA]"><img src="' . $src . '" alt="obrázek" class="w-full h-full object-cover"></div>';
                                        endforeach;
                                        echo '</div>';
                                    elseif (!empty($vinyl['album_cover'])):
                                        $img = $vinyl['album_cover'];
                                        if (strpos($img, '/') === false && strpos($img, 'http') === false) {
                                            $img = '/uploads/' . $img;
                                        }
                                        $src = htmlspecialchars($img);
                                        echo '<div class="aspect-square rounded-lg overflow-hidden border border-[#E3D5CA] max-w-xs"><img src="' . $src . '" alt="obrázek" class="w-full h-full object-cover"></div>';
                                    else:
                                        echo '<div class="text-[#1a1c1e] italic">Žádné obrázky nejsou k dispozici</div>';
                                    endif;
                                ?>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)($vinyl['created_by'] ?? 0)): ?>
                            <div class="bg-[#F5EBE0] rounded-lg p-6">
                                <h3 class="text-lg font-medium text-[#1a1c1e] mb-4">Akce</h3>
                                <div class="flex gap-3">
                                    <a href="?action=edit&id=<?= htmlspecialchars($vinyl['id']) ?>" 
                                       class="bg-[#D5BDAF] hover:bg-[#EDEDE9] text-[#1a1c1e] font-medium px-4 py-2 rounded-lg transition-all duration-200 border border-[#D5BDAF]">
                                        Upravit
                                    </a>
                                    <a href="?action=delete&id=<?= htmlspecialchars($vinyl['id']) ?>" 
                                       onclick="return confirm('Opravdu chcete tento vinyl smazat?')" 
                                       class="bg-[#EDEDE9] hover:bg-[#D6CCC2] text-[#1a1c1e] font-medium px-4 py-2 rounded-lg transition-all duration-200 border border-[#E3D5CA]">
                                        Smazat
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>