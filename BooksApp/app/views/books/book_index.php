<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam knih</title>
    <style>
        :root {
            --bg: #fdf0d5;
            --surface: #ffffff;
            --accent: #c1121f;
            --accent-dark: #930f1b;
            --text: #222222;
            --muted: #5d4f47;
            --border: #ecd6c2;
            --radius: 20px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, sans-serif;
            background: linear-gradient(180deg, var(--bg) 0%, #fff9e9 100%);
            color: var(--text);
        }
        .container {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
            padding: 32px 0;
        }
        .topbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
        }
        h2 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 2.6rem);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            background: var(--accent);
            color: white;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform .2s ease, background .2s ease;
        }
        .btn:hover { background: var(--accent-dark); transform: translateY(-1px); }
        .message {
            margin-bottom: 24px;
            padding: 18px 22px;
            border: 1px solid rgba(193,18,31,0.18);
            border-radius: 16px;
            background: #fff0ec;
            color: #801217;
        }
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid rgba(193,18,31,0.12);
            box-shadow: 0 28px 80px rgba(0,0,0,0.07);
            padding: 28px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
        }
        th, td {
            padding: 16px 18px;
            border-bottom: 1px solid #f4e5d7;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #fdf0d5;
            color: var(--accent);
            font-size: 0.95rem;
            letter-spacing: 0.02em;
        }
        tr:last-child td { border-bottom: none; }
        .actions a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
            margin-right: 12px;
        }
        .actions a:hover { text-decoration: underline; }
        .images-list div {
            font-size: 0.9rem;
            color: var(--muted);
            word-break: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <h2>Seznam knih</h2>
            <a class="btn" href="../views/books/book_create.php">Přidat novou knihu</a>
        </div>
        <div class="card">

    <?php if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])): ?>
        <div class="notifications-container">
            <?php foreach ($_SESSION['messages'] as $type => $messages): ?>
                <?php
                    $color = 'black';
                    if ($type === 'success') $color = '#1f7a3a';
                    if ($type === 'error') $color = 'var(--accent)';
                    if ($type === 'notice') $color = '#d97706';
                ?>
                <?php foreach ($messages as $message): ?>
                    <div style="color: <?= $color ?>; border: 1px solid <?= $color ?>; padding: 10px; margin-bottom: 10px; border-radius:8px; background: #fff;">
                        <strong><?= htmlspecialchars($message) ?></strong>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php unset($_SESSION['messages']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($books)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Název</th>
                    <th>Autor</th>
                    <th>ISBN</th>
                    <th>Datum vydání</th>
                    <th>Cena</th>
                    <th>Popis</th>
                    <th>Obrázky</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= htmlspecialchars($book['id']) ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['isbn']) ?></td>
                        <td><?= htmlspecialchars($book['published_date']) ?></td>
                        <td><?= htmlspecialchars($book['price']) ?></td>
                        <td><?= nl2br(htmlspecialchars($book['description'])) ?></td>
                        <td class="images-list">
                            <?php
                                // Show count of images (0,1,...)
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
                        <td class="actions">
                            <a href="?action=show&id=<?= htmlspecialchars($book['id']) ?>">Detail</a>
                            <a href="?action=edit&id=<?= htmlspecialchars($book['id']) ?>">Upravit</a>
                            <a href="?action=delete&id=<?= htmlspecialchars($book['id']) ?>" onclick="return confirm('Opravdu chcete tuto knihu smazat?')">Smazat</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Žádné knihy nebyly nalezeny.</p>
    <?php endif; ?>
        </div>
    </div>
</body>
</html>
