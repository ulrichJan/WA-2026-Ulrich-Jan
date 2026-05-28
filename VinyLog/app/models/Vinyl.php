<?php

require_once 'Database.php';
require_once __DIR__ . '/../dto/VinylDTO.php';

/**
 * Třída Vinyl – model pro práci s tabulkou `vinyls`
 *
 * Zajišťuje veškeré CRUD operace (Create, Read, Update, Delete) nad databázovou
 * tabulkou vinylových desek. Komunikuje s DB výhradně přes PDO prepared statements.
 *
 * Tabulka `vinyls` obsahuje:
 *   id, album_name, artist, release_year, genre, price,
 *   album_cover (JSON pole názvů souborů), category_id, subcategory_id,
 *   created_by, updated_by
 */
class Vinyl {

    /** @var PDO Aktivní databázové připojení */
    private $conn;

    /** @var string Název DB tabulky, s níž model pracuje */
    private $table_name = "vinyls";

    // -------------------------------------------------------------------------
    // Veřejné vlastnosti – odpovídají sloupcům tabulky (používá stará metoda create())
    // -------------------------------------------------------------------------

    public $album_name;
    public $artist;
    public $release_year;
    public $genre;
    public $price;
    public $album_cover;
    public $created_by;
    public $updated_by;
    public $category_id;

    /**
     * Konstruktor – naváže DB spojení při vytvoření instance modelu.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // =========================================================================
    // CREATE – vložení nového záznamu
    // =========================================================================

    /**
     * Starší metoda pro vytvoření vinylu přímo přes veřejné vlastnosti objektu.
     * Zachována pro zpětnou kompatibilitu. Preferovaná je createFromDTO().
     *
     * @param int|null $userId ID přihlášeného uživatele (uloženo jako created_by)
     * @return bool true při úspěchu, false při chybě
     */
    public function create($userId = null) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET album_name=:album_name, artist=:artist, release_year=:release_year,
                      genre=:genre, price=:price, album_cover=:album_cover,
                      category_id=:category_id, created_by=:created_by";

        $stmt = $this->conn->prepare($query);

        // Sanitizace vstupů – strip_tags odstraní HTML tagy, htmlspecialchars zakóduje
        // speciální znaky jako <, >, &, " do HTML entit (ochrana před XSS)
        $this->album_name   = htmlspecialchars(strip_tags($this->album_name));
        $this->artist       = htmlspecialchars(strip_tags($this->artist));
        $this->release_year = htmlspecialchars(strip_tags($this->release_year));
        $this->genre        = htmlspecialchars(strip_tags($this->genre));
        $this->price        = htmlspecialchars(strip_tags($this->price));

        // Obrázky jsou pole názvů souborů – do DB ukládáme jako JSON řetězec
        $this->album_cover  = json_encode($this->album_cover);
        $this->created_by   = $userId;
        $this->category_id  = $this->category_id ?? null;

        // Svázání parametrů (binding) – PDO je bezpečně vloží do dotazu
        $stmt->bindParam(":album_name",   $this->album_name);
        $stmt->bindParam(":artist",       $this->artist);
        $stmt->bindParam(":release_year", $this->release_year);
        $stmt->bindParam(":genre",        $this->genre);
        $stmt->bindParam(":price",        $this->price);
        $stmt->bindParam(":album_cover",  $this->album_cover);
        $stmt->bindParam(":category_id",  $this->category_id);
        $stmt->bindParam(":created_by",   $this->created_by);

        return $stmt->execute() ? true : false;
    }

    /**
     * Moderní metoda pro vytvoření vinylu z DTO objektu.
     *
     * Přijme VinylDTO, který byl sestaven v controlleru z dat formuláře,
     * sanitizuje data a vloží nový řádek do DB.
     * Hodnoty null pro category_id/subcategory_id jsou ukládány jako SQL NULL
     * (jiné chování než ukládání 0, které by narušilo cizí klíč).
     *
     * @param VinylDTO $dto    Data Transfer Object se všemi daty vinylu
     * @param int|null $userId ID přihlášeného uživatele (uloží se jako created_by)
     * @return bool true při úspěšném vložení
     */
    public function createFromDTO(VinylDTO $dto, $userId = null) {
        $query = "INSERT INTO " . $this->table_name . "
                  SET album_name=:album_name, artist=:artist, release_year=:release_year,
                      genre=:genre, price=:price, album_cover=:album_cover,
                      category_id=:category_id, subcategory_id=:subcategory_id,
                      created_by=:created_by";

        $stmt = $this->conn->prepare($query);

        // Sanitizace všech textových polí z DTO
        $album_name    = htmlspecialchars(strip_tags($dto->album_name));
        $artist        = htmlspecialchars(strip_tags($dto->artist));
        $release_year  = htmlspecialchars(strip_tags($dto->release_year ?? ''));
        $genre         = htmlspecialchars(strip_tags($dto->genre ?? ''));
        $price         = htmlspecialchars(strip_tags($dto->price ?? ''));

        // Pole obrázků serializujeme do JSON (uloží se jako řetězec do DB)
        $album_cover   = json_encode($dto->album_cover ?? []);

        // Přetypování na int nebo null – 0 je chybná hodnota (neodkazuje na žádnou kategorii)
        $category_id    = isset($dto->category)    && $dto->category    ? (int)$dto->category    : null;
        $subcategory_id = isset($dto->subcategory) && $dto->subcategory ? (int)$dto->subcategory : null;

        $stmt->bindParam(":album_name",   $album_name);
        $stmt->bindParam(":artist",       $artist);
        $stmt->bindParam(":release_year", $release_year);
        $stmt->bindParam(":genre",        $genre);
        $stmt->bindParam(":price",        $price);
        $stmt->bindParam(":album_cover",  $album_cover);

        // bindValue s explicitním typem PDO::PARAM_NULL pro null hodnoty –
        // jinak by PDO uložilo řetězec "NULL" místo skutečného SQL NULL
        $stmt->bindValue(":category_id",    $category_id,    $category_id    === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(":subcategory_id", $subcategory_id, $subcategory_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(":created_by",     $userId,         $userId          === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        return $stmt->execute();
    }

    // =========================================================================
    // READ – čtení záznamů z DB
    // =========================================================================

    /**
     * Vrátí všechny vinyly seřazené od nejnovějšího (ORDER BY id DESC).
     *
     * Používá LEFT JOIN pro připojení názvů kategorií a podkategorií,
     * aby view nemusel dělat další dotazy (výkonnostně efektivní).
     * LEFT JOIN zajišťuje, že vinyl se zobrazí i bez přiřazené kategorie.
     *
     * Fallback: pokud tabulka `subcategories` ještě neexistuje v DB
     * (SQL migrace nebyla spuštěna), vrátí záznamy bez sloupce subcategory_name.
     *
     * @return array Pole asociativních polí; každé = jeden vinyl s category_name a subcategory_name
     */
    public function getAll() {
        try {
            // Plný dotaz s JOIN na categories i subcategories
            $query = "SELECT vinyls.*,
                             categories.name   AS category_name,
                             subcategories.name AS subcategory_name
                      FROM vinyls
                      LEFT JOIN categories   ON vinyls.category_id    = categories.id
                      LEFT JOIN subcategories ON vinyls.subcategory_id = subcategories.id
                      ORDER BY vinyls.id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Záložní dotaz – spustí se pokud subcategories tabulka nebo sloupec subcategory_id
            // ještě neexistuje (uživatel nespustil SQL migraci pro Issue #24)
            $query = "SELECT vinyls.*,
                             categories.name AS category_name,
                             NULL             AS subcategory_name
                      FROM vinyls
                      LEFT JOIN categories ON vinyls.category_id = categories.id
                      ORDER BY vinyls.id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Vrátí jeden vinyl dle ID, obohacený o názvy kategorie a podkategorie.
     *
     * Používá LEFT JOIN – stejná logika jako getAll().
     * Fallback na jednodušší dotaz pokud subcategories tabulka neexistuje.
     *
     * @param int $id Primární klíč hledaného vinylu
     * @return array|false Asociativní pole s daty vinylu, nebo false pokud neexistuje
     */
    public function getById($id) {
        try {
            $query = "SELECT vinyls.*,
                             categories.name   AS category_name,
                             subcategories.name AS subcategory_name
                      FROM vinyls
                      LEFT JOIN categories    ON vinyls.category_id    = categories.id
                      LEFT JOIN subcategories ON vinyls.subcategory_id = subcategories.id
                      WHERE vinyls.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Fallback bez subcategories JOIN
            $query = "SELECT vinyls.*,
                             categories.name AS category_name,
                             NULL             AS subcategory_name
                      FROM vinyls
                      LEFT JOIN categories ON vinyls.category_id = categories.id
                      WHERE vinyls.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    // =========================================================================
    // UPDATE – aktualizace záznamu
    // =========================================================================

    /**
     * Starší přímá metoda pro aktualizaci vinylu.
     * Zachována pro zpětnou kompatibilitu. Preferovaná je updateFromDTO().
     *
     * @param int        $id           ID vinylu k aktualizaci
     * @param string     $album_name   Nový název alba
     * @param string     $artist       Nový umělec
     * @param string     $release_year Rok vydání
     * @param string     $genre        Žánr
     * @param string     $price        Cena
     * @param array      $album_cover  Pole názvů souborů obrázků
     * @param int|null   $updatedBy    ID uživatele, který provedl úpravu
     * @return bool true při úspěchu
     */
    public function update($id, $album_name, $artist, $release_year, $genre, $price, $album_cover = [], $updatedBy = null) {
        $query = "UPDATE " . $this->table_name . "
                  SET album_name    = :album_name,
                      artist        = :artist,
                      release_year  = :release_year,
                      genre         = :genre,
                      price         = :price,
                      album_cover   = :album_cover,
                      category_id   = :category_id,
                      updated_by    = :updated_by
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizace vstupů
        $album_name   = htmlspecialchars(strip_tags($album_name));
        $artist       = htmlspecialchars(strip_tags($artist));
        $release_year = htmlspecialchars(strip_tags($release_year));
        $genre        = htmlspecialchars(strip_tags($genre));
        $price        = htmlspecialchars(strip_tags($price));
        $album_cover  = json_encode($album_cover);
        $category_id  = $this->category_id ?? null;

        return $stmt->execute([
            ':id'           => $id,
            ':album_name'   => $album_name,
            ':artist'       => $artist,
            ':release_year' => $release_year,
            ':genre'        => $genre,
            ':price'        => $price,
            ':album_cover'  => $album_cover,
            ':category_id'  => $category_id,
            ':updated_by'   => $updatedBy,
        ]);
    }

    /**
     * Moderní metoda pro aktualizaci vinylu z DTO objektu.
     *
     * Aktualizuje všechna pole vinylu dle dat v DTO, včetně category_id
     * a subcategory_id. Používá bindValue s explicitním NULL typem.
     *
     * @param int      $id        ID vinylu v DB
     * @param VinylDTO $dto       DTO s novými hodnotami
     * @param int|null $updatedBy ID uživatele provádějícího úpravu (uloží se jako updated_by)
     * @return bool true při úspěchu
     */
    public function updateFromDTO($id, VinylDTO $dto, $updatedBy = null) {
        $query = "UPDATE " . $this->table_name . "
                  SET album_name     = :album_name,
                      artist         = :artist,
                      release_year   = :release_year,
                      genre          = :genre,
                      price          = :price,
                      album_cover    = :album_cover,
                      category_id    = :category_id,
                      subcategory_id = :subcategory_id,
                      updated_by     = :updated_by
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizace dat z DTO
        $album_name    = htmlspecialchars(strip_tags($dto->album_name));
        $artist        = htmlspecialchars(strip_tags($dto->artist));
        $release_year  = htmlspecialchars(strip_tags($dto->release_year ?? ''));
        $genre         = htmlspecialchars(strip_tags($dto->genre ?? ''));
        $price         = htmlspecialchars(strip_tags($dto->price ?? ''));
        $album_cover   = json_encode($dto->album_cover ?? []);
        $category_id    = isset($dto->category)    && $dto->category    ? (int)$dto->category    : null;
        $subcategory_id = isset($dto->subcategory) && $dto->subcategory ? (int)$dto->subcategory : null;

        $stmt->bindValue(':id',             $id,             PDO::PARAM_INT);
        $stmt->bindParam(':album_name',     $album_name);
        $stmt->bindParam(':artist',         $artist);
        $stmt->bindParam(':release_year',   $release_year);
        $stmt->bindParam(':genre',          $genre);
        $stmt->bindParam(':price',          $price);
        $stmt->bindParam(':album_cover',    $album_cover);
        $stmt->bindValue(':category_id',    $category_id,    $category_id    === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':subcategory_id', $subcategory_id, $subcategory_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':updated_by',     $updatedBy,      $updatedBy      === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        return $stmt->execute();
    }

    // =========================================================================
    // DELETE – smazání záznamu
    // =========================================================================

    /**
     * Trvale odstraní vinyl z DB dle ID.
     *
     * Poznámka: fyzické soubory obrázků nejsou mazány – to zajišťuje controller
     * (v budoucí verzi).
     *
     * @param int $id ID vinylu ke smazání
     * @return bool true při úspěchu
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // =========================================================================
    // STATISTIKY – agregovaná data pro statistickou stránku
    // =========================================================================

    /**
     * Vrátí souhrnná statistická data o celé sbírce.
     *
     * Provede několik agregačních dotazů a vrátí výsledky jako asociativní pole.
     * Všechny dotazy jsou read-only (SELECT) – model nemodifikuje data.
     *
     * @return array{
     *   overview: array,
     *   byCategory: array[],
     *   byGenre: array[],
     *   mostExpensive: array|false,
     *   latestAdded: array|false
     * }
     */
    public function getStats() {
        /* --- Přehledová čísla --- */
        $stmt = $this->conn->query("
            SELECT
                COUNT(*)                                                   AS total_count,
                COALESCE(SUM(IF(price > 0, price, 0)), 0)                 AS total_value,
                COALESCE(AVG(IF(price > 0 AND price IS NOT NULL, price, NULL)), 0) AS avg_price,
                MIN(IF(release_year > 0, release_year, NULL))              AS oldest_year,
                MAX(IF(release_year > 0, release_year, NULL))              AS newest_year,
                MAX(IF(price > 0, price, NULL))                            AS max_price
            FROM vinyls
        ");
        $overview = $stmt->fetch(PDO::FETCH_ASSOC);

        /* --- Rozdělení dle kategorie --- */
        $stmt = $this->conn->query("
            SELECT COALESCE(c.name, 'Nezařazeno') AS name, COUNT(v.id) AS cnt
            FROM vinyls v
            LEFT JOIN categories c ON v.category_id = c.id
            GROUP BY c.id, c.name
            ORDER BY cnt DESC
        ");
        $byCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* --- Top žánry (max 8) --- */
        $stmt = $this->conn->query("
            SELECT genre AS name, COUNT(*) AS cnt
            FROM vinyls
            WHERE genre IS NOT NULL AND genre != ''
            GROUP BY genre
            ORDER BY cnt DESC
            LIMIT 8
        ");
        $byGenre = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* --- Nejdražší deska --- */
        $stmt = $this->conn->query("
            SELECT album_name, artist, price
            FROM vinyls
            WHERE price > 0
            ORDER BY price + 0 DESC
            LIMIT 1
        ");
        $mostExpensive = $stmt->fetch(PDO::FETCH_ASSOC);

        /* --- Naposledy přidaná deska --- */
        $stmt = $this->conn->query("
            SELECT album_name, artist, id
            FROM vinyls
            ORDER BY id DESC
            LIMIT 1
        ");
        $latestAdded = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'overview'      => $overview,
            'byCategory'    => $byCategory,
            'byGenre'       => $byGenre,
            'mostExpensive' => $mostExpensive,
            'latestAdded'   => $latestAdded,
        ];
    }

    // =========================================================================
    // POMOCNÉ METODY – práce s obrázky
    // =========================================================================

    /**
     * Statická helper metoda – vypočítá webovou URL a cestu v souborovém systému
     * pro nahraný obrázek dle jeho názvu souboru.
     *
     * Proč je to složité: projekt běží v podsložce webového serveru
     * (/WA-2026-Ulrich-Jan/VinyLog/), takže URL nelze pevně napsat –
     * musí se odvodit z DOCUMENT_ROOT a absolutní cesty projektu.
     *
     * @param string $filename Čistý název souboru (bez cesty), např. "vinyl_abc123.jpg"
     * @return array{url: string, path: string, exists: bool}
     *              url    = veřejná URL obrázku pro použití v <img src>
     *              path   = absolutní cesta na disku
     *              exists = zda soubor fyzicky existuje
     */
    public static function getUploadInfo($filename) {
        // Bezpečnostní opatření: basename() odstraní případné cesty (path traversal attack)
        $filename = trim((string)$filename);
        $filename = basename($filename);

        // Absolutní cesta ke složce public/uploads/ na disku
        $uploadsFs = realpath(__DIR__ . '/../../public/uploads');
        if ($uploadsFs === false) {
            // Fallback pokud realpath() selhalo (složka neexistuje)
            $uploadsFs = __DIR__ . '/../../public/uploads';
        }

        // Normalizace lomítek pro cross-platform porovnání (Windows používá \, Linux /)
        $normalizedFs = str_replace('\\', '/', realpath($uploadsFs) ?: $uploadsFs);
        $docRoot = isset($_SERVER['DOCUMENT_ROOT'])
            ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT'])
            : null;

        $urlBase = '/public/uploads';

        // Pokus 1: Odvodit URL prefix z kořene projektu (nejspolehlivější)
        $projectRoot = realpath(__DIR__ . '/../../');
        $normalizedProject = $projectRoot ? str_replace('\\', '/', $projectRoot) : null;

        if ($docRoot && $normalizedProject && strpos($normalizedProject, $docRoot) === 0) {
            // Projekt je podsložkou DOCUMENT_ROOT → odečteme document root, dostaneme relativní URL cestu
            $relativeProj = '/' . trim(substr($normalizedProject, strlen($docRoot)), '/');
            $urlBase = $relativeProj . '/public/uploads';

        } elseif ($docRoot && strpos($normalizedFs, $docRoot) === 0) {
            // Pokus 2: Odvodit z cesty ke složce uploads
            $relative = substr($normalizedFs, strlen($docRoot));
            $relative = '/' . trim(str_replace('\\', '/', $relative), '/');
            $urlBase  = str_replace('/public/uploads/uploads', '/public/uploads', $relative . '/uploads');

        } else {
            // Pokus 3: Odvodit z SCRIPT_NAME (záloha pro neobvyklé konfigurace)
            if (isset($_SERVER['SCRIPT_NAME'])) {
                $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
                if (strpos($script, '/app/') !== false) {
                    $urlBase = rtrim(substr($script, 0, strpos($script, '/app/')), '/') . '/public/uploads';
                } elseif (strpos($script, '/public/') !== false) {
                    $urlBase = rtrim(substr($script, 0, strpos($script, '/public/')), '/') . '/public/uploads';
                }
            }
        }

        // Sestavení finální URL (URL-encode názvu souboru, normalizace lomítek)
        $url = preg_replace('#/+#', '/', $urlBase . '/' . rawurlencode($filename));
        $url = '/' . ltrim($url, '/');

        // Ověření existence souboru na disku
        $fsPathCandidate = rtrim($normalizedFs, '/') . '/' . $filename;
        $exists = file_exists($fsPathCandidate);

        return ['url' => $url, 'path' => $fsPathCandidate, 'exists' => $exists];
    }

    /**
     * Starší metoda pro nahrání obrázků přímo z $_FILES.
     * Zachována pro zpětnou kompatibilitu. Controller nyní používá processImageUploads().
     *
     * @param array $files Pole $_FILES
     * @return array Pole cest k nahraným souborům
     */
    public function uploadImages($files) {
        $uploadedImages = [];
        $uploadDir = 'uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($files['album_cover']['tmp_name'] as $key => $tmp_name) {
            $file_name  = $files['album_cover']['name'][$key];
            $file_tmp   = $files['album_cover']['tmp_name'][$key];
            $file_size  = $files['album_cover']['size'][$key];
            $file_error = $files['album_cover']['error'][$key];

            if ($file_error === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Whitelist povolených přípon – zamezujeme nahrání spustitelných souborů
                if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'webp']) && $file_size < 5000000) {
                    $new_file_name = uniqid() . '.' . $file_ext;
                    if (move_uploaded_file($file_tmp, $uploadDir . $new_file_name)) {
                        $uploadedImages[] = $uploadDir . $new_file_name;
                    }
                }
            }
        }

        $this->album_cover = $uploadedImages;
        return $uploadedImages;
    }
}
