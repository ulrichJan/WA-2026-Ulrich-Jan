<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--script src="https://cdn.tailwindcss.com"></script-->
    <title>Document</title>
</head>
<body>
    <div>
        <h2>Přidat knihu</h2>
        <p>Vyplňte formulář pro přidání nové knihy do databáze.</p>
    </div>
        <form action="../../controllers/BookController.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="title">Název knihy: <span>*</span></label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="author">Autor: <span>*</span></label>
                <input type="text" id="author" name="author" required>
            </div>
            <div>
                <label for="isbn">ISBN: <span>*</span></label>
                <input type="text" id="isbn" name="isbn" required>
            </div>
            <div>
                <label for="published_date">Datum vydání: <span>*</span></label>
                <input type="date" id="published_date" name="published_date">
            </div>
            <div>
                <label for="price">Cena knihy: <span>*</span></label>
                <input type="number" id="price" name="price" step="0.5" required>
            </div>
            <div>
                <label for="description">Popis:</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>


        <div>
                        <label >Obrázky (můžete nahrát více)</label>
                        <label for="images">
                            <span >Klikni pro výběr souborů</span>
                            <span >JPG / PNG / WebP – více souborů najednou</span>
                            <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
                        </label>
                    </div>



            <div>
                <button type="submit">Přidat knihu</button>
            </div>
        </form>
    </div>
</body>
</html>