<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
    <div class="bg-white border border-[#f6e6da] rounded-xl overflow-hidden shadow-lg p-8">
        <a class="text-[#c1121f] font-semibold" href="BookController.php?action=index">&larr; Zpět na seznam knih</a>
        <h2 class="text-2xl font-semibold mt-4 text-[#6b291f]">Detail knihy</h2>
        <p class="text-[#6b291f]">Prohlédněte si údaje o knize <strong><?= htmlspecialchars($book['title']) ?></strong>.</p>

        <div class="grid md:grid-cols-2 gap-6 mt-6">
            <div class="p-4 rounded-md">
                <dl>
                    <dt class="font-semibold text-[#6b291f]">ID</dt>
                    <dd class="text-[#6b291f] mb-3"><?= htmlspecialchars($book['id']) ?></dd>

                    <dt class="font-semibold text-[#6b291f]">Název</dt>
                    <dd class="text-[#6b291f] mb-3"><?= htmlspecialchars($book['title']) ?></dd>

                    <dt class="font-semibold text-[#6b291f]">Autor</dt>
                    <dd class="text-[#6b291f] mb-3"><?= htmlspecialchars($book['author']) ?></dd>

                    <dt class="font-semibold text-[#6b291f]">ISBN</dt>
                    <dd class="text-[#6b291f] mb-3"><?= htmlspecialchars($book['isbn']) ?></dd>

                    <dt class="font-semibold text-[#6b291f]">Datum vydání</dt>
                    <dd class="text-[#6b291f] mb-3"><?= htmlspecialchars($book['published_date']) ?></dd>

                    <dt class="font-semibold text-[#6b291f]">Cena</dt>
                    <dd class="text-[#6b291f]"><?= htmlspecialchars($book['price']) ?></dd>
                </dl>
            </div>

            <div class="p-4 rounded-md">
                <dl>
                    <dt class="font-semibold text-[#6b291f]">Popis</dt>
                    <dd class="text-[#6b291f] mb-4"><?= nl2br(htmlspecialchars($book['description'])) ?></dd>

                    <dt class="font-semibold text-[#6b291f]">Obrázky</dt>
                    <dd class="images-list">
                        <?php
                            $images = json_decode($book['images'], true);
                            if (is_array($images) && count($images) > 0):
                                foreach ($images as $image):
                                    $img = $image;
                                    // If image looks like a path/URL use as-is, otherwise assume filename and prefix with /uploads/
                                    if (strpos($img, '/') === false && strpos($img, 'http') === false) {
                                        $img = '/uploads/' . $img;
                                    }
                                    $src = htmlspecialchars($img);
                                    echo '<div class="mb-3"><img src="' . $src . '" alt="obrázek" style="max-width:220px;max-height:160px;border-radius:8px;object-fit:cover;"></div>';
                                endforeach;
                            elseif (!empty($book['images'])):
                                $img = $book['images'];
                                if (strpos($img, '/') === false && strpos($img, 'http') === false) {
                                    $img = '/uploads/' . $img;
                                }
                                $src = htmlspecialchars($img);
                                echo '<div class="mb-3"><img src="' . $src . '" alt="obrázek" style="max-width:220px;max-height:160px;border-radius:8px;object-fit:cover;"></div>';
                            else:
                                echo '<div class="text-[#6b291f] italic">Žádné obrázky</div>';
                            endif;
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>