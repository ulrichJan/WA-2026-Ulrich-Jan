<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
        <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <a class="text-[#1a1c1e] hover:text-[#1a1c1e] font-medium transition-colors inline-flex items-center gap-2" href="VinylController.php?action=index">
                ← Zpět na seznam vinylů
            </a>
        </div>
        <div class="bg-white border border-[#a3b18a] rounded-xl shadow-sm overflow-hidden">
            <div class="p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-[#344e41] mb-2">Detail vinylu</h2>
                    <p class="text-[#344e41]">Prohlédněte si údaje o vinylu <strong class="text-[#344e41]"><?= htmlspecialchars($vinyl['album_name']) ?></strong>.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 items-start">
                    <div class="space-y-4">
                        <?php
                            $images = json_decode($vinyl['album_cover'], true);
                            $mainImage = null;
                            if (is_array($images) && count($images) > 0) {
                                $mainImage = $images[0];
                            } elseif (!empty($vinyl['album_cover'])) {
                                $mainImage = $vinyl['album_cover'];
                            }
                        if ($mainImage) {
                            $mainSrc = $mainImage;
                            if (strpos($mainSrc, '/') === false && strpos($mainSrc, 'http') === false) {
                                $mainSrc = '/WA-2026-Ulrich-Jan/VinyLog/public/uploads/' . $mainSrc;
                            }
                            $mainSrc = htmlspecialchars($mainSrc);
                            } else {
                                $mainSrc = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 600'><rect width='100%25' height='100%25' fill='%23ffffff'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23344e41' font-family='Arial, sans-serif' font-size='24'>No image</text></svg>";
                            }
                        ?>

                        <div class="rounded-lg overflow-hidden border border-[#e6e6e6]">
                            <img src="<?= $mainSrc ?>" alt="<?= htmlspecialchars($vinyl['album_name']) ?>" class="w-full h-80 object-cover">
                        </div>

                        <?php if (is_array($images) && count($images) > 1): ?>
                            <div class="grid grid-cols-4 gap-3 mt-3">
                                <?php foreach ($images as $idx => $img): if ($idx === 0) continue; 
                                    $thumb = $img;
                                    if (strpos($thumb, '/') === false && strpos($thumb, 'http') === false) {
                                        $thumb = '/WA-2026-Ulrich-Jan/VinyLog/public/uploads/' . $thumb;
                                    }
                                    $thumb = htmlspecialchars($thumb);
                                ?>
                                    <div class="aspect-square rounded-lg overflow-hidden border border-[#e6e6e6]"><img src="<?= $thumb ?>" alt="thumb" class="w-full h-full object-cover"></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white rounded-lg p-6 border border-[#f0f0f0]">
                            <h3 class="text-lg font-medium text-[#344e41] mb-4">Informace o albu</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-[#344e41]">ID</dt>
                                    <dd class="text-[#344e41] mt-1">#<?= htmlspecialchars($vinyl['id']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#344e41]">Název alba</dt>
                                    <dd class="text-[#344e41] mt-1 font-medium"><?= htmlspecialchars($vinyl['album_name']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#344e41]">Umělec</dt>
                                    <dd class="text-[#344e41] mt-1"><?= htmlspecialchars($vinyl['artist']) ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#344e41]">Rok vydání</dt>
                                    <dd class="text-[#344e41] mt-1"><?= htmlspecialchars($vinyl['release_year']) ?: 'Nezadáno' ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#344e41]">Žánr</dt>
                                    <dd class="text-[#344e41] mt-1"><?= htmlspecialchars($vinyl['genre']) ?: 'Nezadáno' ?></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-[#344e41]">Cena</dt>
                                    <dd class="text-[#344e41] mt-1 font-medium"><?= htmlspecialchars($vinyl['price']) ?> Kč</dd>
                                </div>
                            </dl>
                        </div>

                        <?php if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)($vinyl['created_by'] ?? 0)): ?>
                            <div class="bg-white rounded-lg p-6 border border-[#f0f0f0]">
                                <h3 class="text-lg font-medium text-[#344e41] mb-4">Akce</h3>
                                <div class="flex gap-3">
                                    <a href="?action=edit&id=<?= htmlspecialchars($vinyl['id']) ?>" 
                                       class="bg-[#588157] hover:bg-[#3a5a40] text-white font-medium px-4 py-2 rounded-lg transition-all duration-200 border border-[#588157]">
                                        Upravit
                                    </a>
                                    <a href="?action=delete&id=<?= htmlspecialchars($vinyl['id']) ?>" 
                                       onclick="return confirm('Opravdu chcete tento vinyl smazat?')" 
                                       class="bg-[#e6e6e6] hover:bg-[#dad7cd] text-[#344e41] font-medium px-4 py-2 rounded-lg transition-all duration-200 border border-[#f0f0f0]">
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