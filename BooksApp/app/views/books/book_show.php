<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail knihy</title>
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
            width: min(1100px, calc(100% - 32px));
            margin: 0 auto;
            padding: 32px 0;
        }
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid rgba(193,18,31,0.12);
            box-shadow: 0 28px 80px rgba(0,0,0,0.08);
            padding: 32px;
        }
        .back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
        }
        .back:hover { color: var(--accent-dark); }
        h2 {
            margin: 0 0 8px;
            font-size: clamp(2rem, 2.5vw, 2.4rem);
        }
        .detail-grid {
            display: grid;
            gap: 18px;
            margin-top: 24px;
        }
        .detail-grid > div {
            padding: 18px;
            border-radius: 18px;
            background: #fff8f0;
            border: 1px solid rgba(193,18,31,0.12);
        }
        .detail-grid dt {
            font-weight: 700;
            color: #423a37;
            margin-bottom: 8px;
        }
        .detail-grid dd {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }
        .images-list {
            display: grid;
            gap: 10px;
        }
        .images-list div {
            padding: 12px 14px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--border);
            color: var(--text);
            word-break: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <a class="back" href="BookController.php?action=index">&larr; Zpět na seznam knih</a>
            <h2>Detail knihy</h2>
            <p>Prohlédněte si údaje o knize <strong><?= htmlspecialchars($book['title']) ?></strong>.</p>
            <div class="detail-grid">
                <div>
                    <dl>
                        <dt>ID</dt>
                        <dd><?= htmlspecialchars($book['id']) ?></dd>
                        <dt>Název</dt>
                        <dd><?= htmlspecialchars($book['title']) ?></dd>
                        <dt>Autor</dt>
                        <dd><?= htmlspecialchars($book['author']) ?></dd>
                        <dt>ISBN</dt>
                        <dd><?= htmlspecialchars($book['isbn']) ?></dd>
                        <dt>Datum vydání</dt>
                        <dd><?= htmlspecialchars($book['published_date']) ?></dd>
                        <dt>Cena</dt>
                        <dd><?= htmlspecialchars($book['price']) ?></dd>
                    </dl>
                </div>
                <div>
                    <dl>
                        <dt>Popis</dt>
                        <dd><?= nl2br(htmlspecialchars($book['description'])) ?></dd>
                        <dt>Obrázky</dt>
                        <dd class="images-list">
                            <?php
                                // Render actual <img> tags for stored image paths
                                $images = json_decode($book['images'], true);
                                if (is_array($images) && count($images) > 0):
                                    foreach ($images as $image):
                                        $src = htmlspecialchars($image);
                                        echo '<div><img src="' . $src . '" alt="obrázek" style="max-width:220px;max-height:160px;border-radius:8px;object-fit:cover;"></div>';
                                    endforeach;
                                elseif (!empty($book['images'])):
                                    $src = htmlspecialchars($book['images']);
                                    echo '<div><img src="' . $src . '" alt="obrázek" style="max-width:220px;max-height:160px;border-radius:8px;object-fit:cover;"></div>';
                                else:
                                    echo '<div>Žádné obrázky</div>';
                                endif;
                            ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</body>
</html>