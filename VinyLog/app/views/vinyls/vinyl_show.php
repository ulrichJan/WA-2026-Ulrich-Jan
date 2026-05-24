<?php
// Vložíme hlavičku stránky (navigace, <main>)
require_once __DIR__ . '/../layout/header.php';
?>
    <div class="max-w-5xl mx-auto">

        <!-- Odkaz zpět na seznam desek -->
        <div class="mb-6">
            <a class="text-[#344e41] hover:text-[#3a5a40] font-medium transition-colors inline-flex items-center gap-2" href="?action=index">
                ← Zpět na seznam
            </a>
        </div>

        <div class="bg-white border border-[#a3b18a] rounded-xl shadow-sm overflow-hidden">
            <div class="p-8">

                <!-- Název alba a základní popis -->
                <div class="mb-6">
                    <h2 class="text-4xl font-semibold text-[#344e41] mb-2"><?= htmlspecialchars($vinyl['album_name']) ?></h2>
                    <p class="text-xl text-[#3a5a40] mb-1"><?= htmlspecialchars($vinyl['artist']) ?></p>
                    <p class="text-base text-[#344e41]">Detail alba a metadata</p>
                </div>

                <!-- Dvousloupcový layout: vlevo obrázky, vpravo metadata a akce -->
                <div class="grid md:grid-cols-2 gap-10 items-start">

                    <!-- =====================================================
                         Levý sloupec: Galerie obrázků alba
                         ===================================================== -->
                    <div class="space-y-4">
                        <?php
                            // Načteme model Vinyl pro použití statické metody getUploadInfo()
                            require_once __DIR__ . '/../../models/Vinyl.php';

                            // Dekódujeme JSON pole názvů obrázků z DB
                            $images    = json_decode($vinyl['album_cover'], true);
                            $mainImage = null;

                            if (is_array($images) && count($images) > 0) {
                                $mainImage = $images[0]; // Hlavní obrázek = první v poli
                            } elseif (!empty($vinyl['album_cover'])) {
                                $mainImage = $vinyl['album_cover']; // Fallback
                            }

                            // Normalizace: ošetříme vnořené JSON řetězce a prázdné hodnoty
                            if (is_string($mainImage)) {
                                $mainImage = trim($mainImage);
                                if (strlen($mainImage) > 0 && $mainImage[0] === '[') {
                                    $decoded   = json_decode($mainImage, true);
                                    $mainImage = (is_array($decoded) && count($decoded) > 0) ? $decoded[0] : null;
                                }
                                if ($mainImage !== null) $mainImage = trim($mainImage);
                                if ($mainImage === '' || $mainImage === '[]') $mainImage = null;
                            }

                            // Výpočet src pro hlavní obrázek
                            if ($mainImage) {
                                if (strpos($mainImage, 'http') === 0) {
                                    // Absolutní URL (externí obrázek)
                                    $mainSrc    = htmlspecialchars($mainImage);
                                    $mainExists = true;
                                } else {
                                    // Lokální soubor – použijeme helper pro správnou URL
                                    $info       = Vinyl::getUploadInfo(basename($mainImage));
                                    $mainSrc    = htmlspecialchars($info['url']);
                                    $mainExists = $info['exists'];
                                }
                            } else {
                                // Placeholder SVG pokud vinyl nemá obrázek
                                $mainSrc    = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 600'><rect width='100%25' height='100%25' fill='%23ffffff'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23344e41' font-family='Arial, sans-serif' font-size='24'>No image</text></svg>";
                                $mainExists = false;
                            }
                        ?>

                        <!-- Hlavní obrázek alba (výška 520px, ořezáno přes object-cover) -->
                        <div class="rounded-2xl overflow-hidden border border-[#e6e6e6]">
                            <img src="<?= $mainSrc ?>"
                                 alt="<?= htmlspecialchars($vinyl['album_name']) ?>"
                                 class="w-full h-[520px] object-cover">
                        </div>

                        <?php if (is_array($images) && count($images) > 1): ?>
                            <!-- Miniaturní galerie: zbývající obrázky (přeskočíme první = hlavní) -->
                            <div class="grid grid-cols-4 gap-3 mt-3">
                                <?php foreach ($images as $idx => $img):
                                    if ($idx === 0) continue; // Přeskočíme hlavní obrázek
                                    $img = trim($img);
                                    if (strpos($img, 'http') === 0) {
                                        $thumb = htmlspecialchars($img);
                                    } else {
                                        $tinfo = Vinyl::getUploadInfo(basename($img));
                                        $thumb = htmlspecialchars($tinfo['url']);
                                    }
                                ?>
                                    <div class="aspect-square rounded-2xl overflow-hidden border border-[#e6e6e6]">
                                        <img src="<?= $thumb ?>" alt="thumb" class="w-full h-full object-cover">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- =====================================================
                         Pravý sloupec: Metadata a akční tlačítka
                         ===================================================== -->
                    <div class="space-y-4">

                        <!-- Tabulka metadat vinylu -->
                        <div class="bg-white rounded-2xl p-6 border border-[#f0f0f0]">
                            <h3 class="text-lg font-medium text-[#344e41] mb-4">Informace o albu</h3>
                            <dl class="space-y-3">

                                <div>
                                    <dt class="text-base font-medium text-[#344e41]">ID</dt>
                                    <dd class="text-[#344e41] mt-1">#<?= htmlspecialchars($vinyl['id']) ?></dd>
                                </div>

                                <div>
                                    <dt class="text-base font-medium text-[#344e41]">Název alba</dt>
                                    <dd class="text-[#344e41] mt-1 font-medium"><?= htmlspecialchars($vinyl['album_name']) ?></dd>
                                </div>

                                <div>
                                    <dt class="text-base font-medium text-[#344e41]">Umělec</dt>
                                    <dd class="text-[#344e41] mt-1"><?= htmlspecialchars($vinyl['artist']) ?></dd>
                                </div>

                                <div>
                                    <dt class="text-base font-medium text-[#344e41]">Rok vydání</dt>
                                    <!-- ?: operátor vrátí 'Nezadáno' pokud je rok prázdný -->
                                    <dd class="text-[#344e41] mt-1"><?= htmlspecialchars($vinyl['release_year']) ?: 'Nezadáno' ?></dd>
                                </div>

                                <div>
                                    <dt class="text-base font-medium text-[#344e41]">Žánr</dt>
                                    <dd class="text-[#344e41] mt-1"><?= htmlspecialchars($vinyl['genre']) ?: 'Nezadáno' ?></dd>
                                </div>

                                <!-- category_name pochází z LEFT JOIN v Vinyl::getById()
                                     ?? 'Nezařazeno' je fallback pokud vinyl nemá kategorii -->
                                <div>
                                    <dt class="text-base font-medium text-[#344e41]">Kategorie</dt>
                                    <dd class="text-[#344e41] mt-1"><?= htmlspecialchars($vinyl['category_name'] ?? 'Nezařazeno') ?></dd>
                                </div>

                                <?php if (!empty($vinyl['subcategory_name'])): ?>
                                    <!-- Podkategorie se zobrazí pouze pokud vinyl má přiřazenou podkategorii -->
                                    <div>
                                        <dt class="text-base font-medium text-[#344e41]">Podkategorie</dt>
                                        <dd class="text-[#344e41] mt-1"><?= htmlspecialchars($vinyl['subcategory_name']) ?></dd>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <dt class="text-base font-medium text-[#344e41]">Cena</dt>
                                    <dd class="text-[#344e41] mt-1 font-medium"><?= htmlspecialchars($vinyl['price']) ?> Kč</dd>
                                </div>

                            </dl>
                        </div>

                        <!-- Akční tlačítka: zobrazí se vlastníkovi záznamu nebo adminu.
                             Admin vidí záznamy ostatních uživatelů s tmavší barvou tlačítek
                             jako vizuální upozornění, že edituje cizí záznam. -->
                        <?php
                            $isOwner = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)($vinyl['created_by'] ?? 0);
                            $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
                        ?>
                        <?php if ($isOwner || $isAdmin): ?>
                            <div class="bg-white rounded-2xl p-6 border border-[#f0f0f0]">
                                <h3 class="text-lg font-medium text-[#344e41] mb-4">Akce</h3>
                                <div class="flex gap-3">
                                    <!-- Tlačítko Upravit: zelené pro vlastníka, tmavé pro admina editujícího cizí záznam -->
                                    <a href="?action=edit&id=<?= htmlspecialchars($vinyl['id']) ?>"
                                       class="<?= $isOwner ? 'bg-[#588157] border-[#588157]' : 'bg-[#344e41] border-[#344e41]' ?>
                                              hover:opacity-80 text-white font-medium px-6 py-3 rounded-2xl transition-all duration-200 border">
                                        Upravit<?= (!$isOwner && $isAdmin) ? ' (admin)' : '' ?>
                                    </a>
                                    <!-- Tlačítko Smazat: šedé pro vlastníka, červené pro admina editujícího cizí záznam -->
                                    <a href="?action=delete&id=<?= htmlspecialchars($vinyl['id']) ?>"
                                       onclick="return confirm('Opravdu chcete tento vinyl smazat?')"
                                       class="<?= $isOwner ? 'bg-[#e6e6e6] text-[#344e41] border-[#f0f0f0]' : 'bg-[#c1121f] text-white border-[#c1121f]' ?>
                                              hover:opacity-80 font-medium px-6 py-3 rounded-2xl transition-all duration-200 border">
                                        Smazat<?= (!$isOwner && $isAdmin) ? ' (admin)' : '' ?>
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
