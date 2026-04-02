<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravit knihu</title>
    <style>
        :root {
            --bg: #fdf0d5;
            --surface: #ffffff;
            --accent: #c1121f;
            --accent-dark: #930f1b;
            --text: #222222;
            --muted: #5d4f47;
            --border: #e7d1c0;
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
        a.back {
            display: inline-block;
            margin-bottom: 24px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
        }
        a.back:hover {
            color: var(--accent-dark);
        }
        h2 {
            margin: 0 0 8px;
            font-size: clamp(2rem, 2.5vw, 2.4rem);
        }
        p { color: var(--muted); line-height: 1.7; }
        form {
            display: grid;
            gap: 18px;
        }
        .field {
            display: grid;
            gap: 10px;
        }
        label {
            font-weight: 600;
            color: #423a37;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fff;
            color: var(--text);
            font: inherit;
        }
        textarea { min-height: 140px; resize: vertical; }
        button {
            padding: 14px 28px;
            border: none;
            border-radius: 999px;
            background: var(--accent);
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s ease, transform .2s ease;
        }
        button:hover { background: var(--accent-dark); transform: translateY(-1px); }
        .upload-label {
            display: inline-flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px;
            border: 1px dashed var(--border);
            border-radius: 16px;
            background: #fff8f0;
            color: var(--muted);
            cursor: pointer;
        }
        .upload-label input {
            display: none;
        }
        span.required {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <p><a class="back" href="BookController.php?action=index">&larr; Zpět na seznam knih</a></p>
            <div>
                <h2>Upravit knihu (ID záznamu: <?= htmlspecialchars($book['id']) ?>)</h2>
                <p>Upravujete data pro knihu: <strong><?= htmlspecialchars($book['title']) ?></strong></p>
                <p>Změňte požadované údaje a uložte formulář.</p>
            </div>
            <form action="BookController.php?action=update&id=<?= htmlspecialchars($book['id']) ?>" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label for="id_display">ID v databázi</label>
                    <input type="text" id="id_display" value="<?= htmlspecialchars($book['id']) ?>" readonly>
                </div>
                <div class="field">
                    <label for="title">Název knihy <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
                </div>
                <div class="field">
                    <label for="author">Autor <span class="required">*</span></label>
                    <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
                </div>
                <div class="field">
                    <label for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>">
                </div>
                <div class="field">
                    <label for="published_date">Datum vydání</label>
                    <input type="date" id="published_date" name="published_date" value="<?= htmlspecialchars($book['published_date']) ?>">
                </div>
                <div class="field">
                    <label for="price">Cena knihy</label>
                    <input type="number" id="price" name="price" step="0.5" value="<?= htmlspecialchars($book['price']) ?>">
                </div>
                <div class="field">
                    <label for="description">Popis</label>
                    <textarea id="description" name="description" rows="5"><?= htmlspecialchars($book['description']) ?></textarea>
                </div>
                <div class="field">
                    <label class="upload-label" for="images">
                        <span>Obrázky (můžete zvolit nové soubory, budou přidány)</span>
                        <input type="file" id="images" name="images[]" multiple accept="image/*">
                    </label>
                </div>
                <div>
                    <button type="submit">Uložit změny do DB</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
