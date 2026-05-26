<?php

// Session musí být spuštěna jako první – před jakýmkoliv výstupem i před include
session_start();

require_once '../models/Vinyl.php';
require_once '../dto/VinylDTO.php';

/**
 * Třída VinylController – hlavní controller aplikace
 *
 * Zodpovídá za veškerou logiku spojenou s vinylovými deskami:
 * zobrazení seznamu, detail, přidání, úprava a mazání.
 *
 * Architektonický vzor MVC (Model-View-Controller):
 *  - Model  = Vinyl.php, Category.php, Subcategory.php (práce s DB)
 *  - View   = soubory v /views/vinyls/ (HTML šablony)
 *  - Controller = tento soubor (logika a přesměrování)
 *
 * Autorizační pravidla:
 *  - Čtení (index, show) je povoleno všem (i nepřihlášeným)
 *  - Zápis (create, store) vyžaduje přihlášení
 *  - Úprava a mazání (edit, update, delete) vyžaduje přihlášení + vlastnictví záznamu
 *  - Administrátor (is_admin=1) může upravovat a mazat záznamy všech uživatelů
 */
class VinylController {

    // =========================================================================
    // STORE – uložení nového vinylu (zpracování POST formuláře)
    // =========================================================================

    /**
     * Zpracuje odeslaný formulář pro přidání nového vinylu.
     *
     * Sestaví VinylDTO z POST dat, zpracuje nahrané obrázky a předá vše modelu.
     */
    public function store() {
        // --- Autorizace: přihlášení je povinné pro zápis ---
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro přidání vinylu se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Zpracování nahraných obrázků provedeme jednou, výsledek předáme do DTO
            // (processImageUploads přesune soubory do public/uploads/ a vrátí pole názvů)
            $uploadedImages = $this->processImageUploads();

            // Sestavení DTO z POST dat – (int) přetypování category/subcategory
            // zajistí, že do DB se uloží číslo, ne řetězec "0" nebo prázdný string
            $data = [
                'album_name'  => $_POST['album_name']  ?? '',
                'artist'      => $_POST['artist']      ?? '',
                'release_year'=> $_POST['release_year']?? '',
                'genre'       => $_POST['genre']       ?? '',
                'price'       => $_POST['price']       ?? '',
                'album_cover' => $uploadedImages,
                'category'    => (int)($_POST['category']    ?? 0),
                'subcategory' => (int)($_POST['subcategory'] ?? 0),
            ];

            $dto = new VinylDTO($data);

            $vinylModel = new Vinyl();

            // ID přihlášeného uživatele → uloží se do sloupce created_by
            $userId = $_SESSION['user_id'] ?? null;

            if ($vinylModel->createFromDTO($dto, $userId)) {
                $this->addSuccessMessage('Vinyl byl úspěšně přidán.');
                header("Location: VinylController.php?action=index");
                exit;
            } else {
                echo "Chyba při přidávání vinylu.";
            }
        }
    }

    // =========================================================================
    // INDEX – seznam všech vinylů
    // =========================================================================

    /**
     * Načte všechny vinyly z DB a předá je do view pro zobrazení.
     * Přístupné všem (i nepřihlášeným návštěvníkům).
     */
    public function index() {
        $vinyl  = new Vinyl();
        // getAll() vrací pole asociativních polí – každé = jeden vinyl
        // JOIN v getAll() přidá i category_name a subcategory_name
        $vinyls = $vinyl->getAll();
        include __DIR__ . '/../views/vinyls/vinyl_index.php';
    }

    // =========================================================================
    // CREATE – zobrazení formuláře pro přidání nového vinylu
    // =========================================================================

    /**
     * Zobrazí formulář pro přidání nového vinylu.
     * Načte dostupné kategorie a podkategorie pro select-boxy ve formuláři.
     */
    public function create() {
        // --- Autorizace ---
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro přidání vinylu se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }

        // Načtení kategorií a podkategorií pro select-boxy ve formuláři
        // require_once zajistí, že třídy se nenačtou vícekrát pokud jsou již includovány
        require_once __DIR__ . '/../models/Category.php';
        require_once __DIR__ . '/../models/Subcategory.php';

        $categoryModel    = new Category();
        $categories       = $categoryModel->getAllCategories();

        $subcategoryModel = new Subcategory();
        $subcategories    = $subcategoryModel->getAllSubcategories();

        // Proměnné $categories a $subcategories jsou dostupné i v includeovaném view
        include __DIR__ . '/../views/vinyls/vinyl_create.php';
    }

    // =========================================================================
    // EDIT – zobrazení formuláře pro úpravu existujícího vinylu
    // =========================================================================

    /**
     * Zobrazí předvyplněný formulář pro editaci existujícího vinylu.
     *
     * Ověří: přihlášení → existence záznamu → vlastnictví (nebo admin práva)
     *
     * @param int|null $id ID vinylu (může přijít z URL nebo z parametru metody)
     */
    public function edit($id = null) {
        // ID může přijít jako parametr metody (z routeru), nebo z URL ?id=X
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        // --- Autorizace: přihlášení ---
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro úpravu vinylu se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }

        // --- Validace: ID musí být zadáno ---
        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID vinylu k úpravě.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        $vinyl     = new Vinyl();
        $vinylData = $vinyl->getById($id);

        // --- Validace: vinyl musí existovat ---
        if (!$vinylData) {
            $this->addErrorMessage('Požadovaný vinyl nebyl v databázi nalezen.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        // --- Autorizace: pouze admin může editovat záznamy ---
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

        if (!$isAdmin) {
            $this->addErrorMessage('Úpravy vinylů jsou povoleny pouze administrátorům.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        // $vinyl přepíšeme z objektu Vinyl na asociativní pole dat (pro view)
        $vinyl = $vinylData;

        // Načtení kategorií a podkategorií pro předvyplněné select-boxy
        require_once __DIR__ . '/../models/Category.php';
        require_once __DIR__ . '/../models/Subcategory.php';

        $categoryModel    = new Category();
        $categories       = $categoryModel->getAllCategories();

        $subcategoryModel = new Subcategory();
        $subcategories    = $subcategoryModel->getAllSubcategories();

        include __DIR__ . '/../views/vinyls/vinyl_edit.php';
    }

    // =========================================================================
    // UPDATE – uložení editace existujícího vinylu
    // =========================================================================

    /**
     * Zpracuje POST formulář s úpravami existujícího vinylu.
     *
     * Ověří přihlášení, vlastnictví záznamu, zpracuje obrázky a uloží data.
     * Pokud nejsou nahrány nové obrázky, zachová stávající.
     *
     * @param int|null $id ID vinylu k aktualizaci
     */
    public function update($id = null) {
        // --- Autorizace: přihlášení ---
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro úpravu vinylu se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }

        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID vinylu k aktualizaci.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        // Aktualizaci provedeme pouze pro POST požadavky (formulář odeslán)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->addNoticeMessage('Pro úpravu vinylu je nutné odeslat formulář.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        // Sestavení DTO z POST dat
        $data = [
            'album_name'   => $_POST['album_name']   ?? '',
            'artist'       => $_POST['artist']        ?? '',
            'release_year' => $_POST['release_year']  ?? '',
            'genre'        => $_POST['genre']          ?? '',
            'price'        => $_POST['price']          ?? '',
            'category'     => (int)($_POST['category']    ?? 0),
            'subcategory'  => (int)($_POST['subcategory'] ?? 0),
        ];

        $dto   = new VinylDTO($data);
        $vinyl = new Vinyl();

        // Načteme stávající záznam pro ověření vlastnictví a záchranné obrázky
        $existingVinyl = $vinyl->getById($id);

        // --- Autorizace: pouze admin může ukládat úpravy ---
        $currentUserId = $_SESSION['user_id'] ?? null;
        $isAdmin       = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

        if (!$isAdmin) {
            $this->addErrorMessage('Úpravy vinylů jsou povoleny pouze administrátorům.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        // --- Zpracování obrázků ---
        $uploadedImages = [];

        // Pokud uživatel nahrál nové soubory, zpracujeme je
        if (isset($_FILES['album_cover']) && !empty($_FILES['album_cover']['name'][0])) {
            $uploadedImages = $this->processImageUploads();
        }

        // Záchrana: pokud nebyl nahrán žádný nový obrázek, ponecháme stávající.
        // JSON decode převede uložený řetězec zpět na PHP pole názvů souborů.
        if (empty($uploadedImages) && $existingVinyl && !empty($existingVinyl['album_cover'])) {
            $uploadedImages = json_decode($existingVinyl['album_cover'], true);
            if (!is_array($uploadedImages)) {
                $uploadedImages = [$existingVinyl['album_cover']];
            }
        }

        $dto->album_cover = $uploadedImages;

        $isUpdated = $vinyl->updateFromDTO($id, $dto, $currentUserId);

        if ($isUpdated) {
            $this->addSuccessMessage('Vinyl byl úspěšně upraven.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        $this->addErrorMessage('Nastala chyba. Změny se nepodařilo uložit.');
        header("Location: VinylController.php?action=index");
        exit;
    }

    // =========================================================================
    // SHOW – detail jednoho vinylu
    // =========================================================================

    /**
     * Zobrazí detailní stránku jednoho vinylu.
     * Přístupné všem (i nepřihlášeným). Tlačítka Upravit/Smazat
     * se zobrazí pouze vlastníkovi nebo adminu.
     *
     * @param int|null $id ID vinylu k zobrazení
     */
    public function show($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID vinylu.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        $vinyl     = new Vinyl();
        $vinylData = $vinyl->getById($id);

        if (!$vinylData) {
            $this->addErrorMessage('Vinyl nebyl nalezen.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        // Přepíšeme proměnnou $vinyl z objektu na asociativní pole pro view
        $vinyl = $vinylData;
        include __DIR__ . '/../views/vinyls/vinyl_show.php';
    }

    // =========================================================================
    // DELETE – smazání vinylu
    // =========================================================================

    /**
     * Trvale smaže vinyl z databáze.
     * Ověří přihlášení a vlastnictví záznamu (nebo admin práva).
     *
     * @param int|null $id ID vinylu ke smazání
     */
    public function delete($id = null) {
        // --- Autorizace: přihlášení ---
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro smazání vinylu se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }

        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID vinylu ke smazání.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        $vinyl     = new Vinyl();
        $vinylData = $vinyl->getById($id);

        // --- Autorizace: pouze admin může mazat záznamy ---
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

        if (!$isAdmin) {
            $this->addErrorMessage('Mazání vinylů je povoleno pouze administrátorům.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        $isDeleted = $vinyl->delete($id);

        if ($isDeleted) {
            $this->addSuccessMessage('Vinyl byl trvale smazán z databáze.');
        } else {
            $this->addErrorMessage('Nastala chyba. Vinyl se nepodařilo smazat.');
        }

        header("Location: VinylController.php?action=index");
        exit;
    }

    // =========================================================================
    // Pomocné metody – flash zprávy (notifikace v session)
    // =========================================================================
    // Zprávy se ukládají do session jako pole a zobrazí se v header.php
    // při příštím načtení stránky (pak jsou vymazány).

    protected function addSuccessMessage($message) {
        $_SESSION['messages']['success'][] = $message;
    }

    protected function addNoticeMessage($message) {
        $_SESSION['messages']['notice'][] = $message;
    }

    protected function addErrorMessage($message) {
        $_SESSION['messages']['error'][] = $message;
    }

    // =========================================================================
    // Pomocná metoda – zpracování nahrání obrázků
    // =========================================================================

    /**
     * Přesune nahrané soubory z dočasné složky PHP do public/uploads/.
     *
     * Bezpečnostní opatření:
     *  - Whitelist přípon (jpg, jpeg, png, webp, gif)
     *  - Náhodný název souboru (vinyl_ + uniqid + 4 hex znaky) → zabraňuje přepsání
     *
     * @return array Pole názvů souborů (bez cesty), které byly úspěšně nahrány
     */
    protected function processImageUploads() {
        $uploadedFiles = [];

        // Absolutní cesta k cílové složce (od tohoto souboru: ../../public/uploads/)
        $uploadDir = __DIR__ . '/../../public/uploads/';

        // Pokud složka neexistuje, vytvoříme ji (rekurzivně) s právy 0777
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // $_FILES['album_cover'] je pole struktur (name, tmp_name, error, size)
        // pro každý nahraný soubor (formulář má atribut multiple)
        if (isset($_FILES['album_cover']) && !empty($_FILES['album_cover']['name'][0])) {
            $fileCount = count($_FILES['album_cover']['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                // Přeskočíme soubory s chybou (nebyly nahrány)
                if ($_FILES['album_cover']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $tmpName       = $_FILES['album_cover']['tmp_name'][$i];
                $originalName  = basename($_FILES['album_cover']['name'][$i]);
                $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                // Whitelist povolených přípon – zamezujeme nahrání PHP/JS souborů
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                if (!in_array($fileExtension, $allowedExtensions)) {
                    continue; // Přeskočíme soubory s nepovolenou příponou
                }

                // Unikátní název: vinyl_ + uniqid() + 4 náhodné hex znaky + přípona
                // Tím předejdeme kolizím při nahrání dvou souborů se stejným jménem
                $newName        = 'vinyl_' . uniqid() . '_' . substr(md5(mt_rand()), 0, 4) . '.' . $fileExtension;
                $targetFilePath = $uploadDir . $newName;

                // move_uploaded_file() bezpečně přesune soubor z dočasné složky PHP
                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    $uploadedFiles[] = $newName; // Ukládáme pouze název, ne celou cestu
                }
            }
        }

        return $uploadedFiles;
    }
}

// =============================================================================
// Routing – přepínač akcí podle URL parametru ?action= a HTTP metody
// =============================================================================
// Tento kód se spustí při každém požadavku na VinylController.php.
// Vytvoří instanci controlleru a zavolá příslušnou metodu.

$controller = new VinylController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST požadavky: ukládání (store) nebo aktualizace (update)
    if (isset($_GET['action']) && $_GET['action'] === 'update') {
        $controller->update($_GET['id'] ?? null);
    } else {
        $controller->store();
    }
} elseif (isset($_GET['action'])) {
    // GET požadavky: zobrazení různých stránek dle ?action=...
    switch ($_GET['action']) {
        case 'show':
            $controller->show($_GET['id'] ?? null);
            break;
        case 'delete':
            $controller->delete($_GET['id'] ?? null);
            break;
        case 'edit':
            $controller->edit($_GET['id'] ?? null);
            break;
        case 'index':
            $controller->index();
            break;
        case 'create':
            $controller->create();
            break;
        default:
            echo 'Neplatná akce.';
    }
} else {
    echo 'Neplatný požadavek. Přejděte na <a href="VinylController.php?action=index">seznam vinylů</a>.';
}
