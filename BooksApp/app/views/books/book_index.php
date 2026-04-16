<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main class="container mx-auto px-6 pb-10 pt-6 flex-grow">
    <div class="flex justify-between items-end mb-6">
    <h2 class="text-3xl font-light tracking-widest text-[#6b291f] uppercase">Dostupné knihy</h2>
    <a class="bg-[#c1121f] hover:bg-[#930f1b] text-[#fdf0d5] px-4 py-2 rounded-md font-bold shadow-sm" href="/WA-2026-Ulrich-Jan/BooksApp/app/controllers/BookController.php?action=create">Přidat novou knihu</a>
  </div>

    <div class="bg-white border border-[#f6e6da] rounded-xl overflow-hidden shadow-lg p-6">
      <?php if (empty($books)): ?>
          <div class="p-10 text-center text-[#6b291f] italic">
              V databázi se zatím nenachází žádné knihy.
          </div>
      <?php else: ?>
          <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse bg-white rounded-md overflow-hidden">
                <thead class="bg-[#fdf0d5] text-[#6b291f]">
                  <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Název</th>
                    <th class="p-3">Autor</th>
                    <th class="p-3">ISBN</th>
                    <th class="p-3">Datum vydání</th>
                    <th class="p-3">Cena</th>
                    <th class="p-3">Popis</th>
                    <th class="p-3">Obrázky</th>
                    <th class="p-3">Akce</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($books as $book): ?>
                    <tr class="border-t">
                      <td class="p-3 align-top"><?= htmlspecialchars($book['id']) ?></td>
                      <td class="p-3 align-top"><?= htmlspecialchars($book['title']) ?></td>
                      <td class="p-3 align-top"><?= htmlspecialchars($book['author']) ?></td>
                      <td class="p-3 align-top"><?= htmlspecialchars($book['isbn']) ?></td>
                      <td class="p-3 align-top"><?= htmlspecialchars($book['published_date']) ?></td>
                      <td class="p-3 align-top"><?= htmlspecialchars($book['price']) ?></td>
                      <td class="p-3 align-top"><?= nl2br(htmlspecialchars($book['description'])) ?></td>
                      <td class="p-3 align-top">
                        <?php
                          $images = json_decode($book['images'], true);
                          if (is_array($images)) {
                              $count = count($images);
                          } elseif (!empty($book['images'])) {
                              $count = 1;
                          } else {
                              $count = 0;
                          }
                          echo htmlspecialchars($count);
                        ?>
                      </td>
                      <td class="p-3 align-top">
                        <a href="?action=show&id=<?= htmlspecialchars($book['id']) ?>" class="text-[#c1121f] font-semibold mr-3">Detail</a>
                        <a href="?action=edit&id=<?= htmlspecialchars($book['id']) ?>" class="text-[#c1121f] font-semibold mr-3">Upravit</a>
                        <a href="?action=delete&id=<?= htmlspecialchars($book['id']) ?>" onclick="return confirm('Opravdu chcete tuto knihu smazat?')" class="text-[#c1121f] font-semibold">Smazat</a>
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
