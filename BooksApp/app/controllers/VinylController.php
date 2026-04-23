<?php
session_start();

require_once '../models/Vinyl.php';
require_once '../dto/VinylDTO.php';

class VinylController {

    public function store() {
        // !!! ZMĚNA: Autorizace: Pokud uživatel není přihlášen, nemá tu co dělat
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro přidání vinylu se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Build DTO from POST
            $data = [
                'album_name' => $_POST['album_name'] ?? '',
                'artist' => $_POST['artist'] ?? '',
                'release_year' => $_POST['release_year'] ?? '',
                'genre' => $_POST['genre'] ?? '',
                'price' => $_POST['price'] ?? '',
                'album_cover' => $this->processImageUploads(),
            ];

            $dto = new VinylDTO($data);

            $vinylModel = new Vinyl();

            // Handle image uploads and attach to DTO (processed by controller helper)
            $uploaded = $this->processImageUploads();
            $dto->images = $uploaded;

            // Pass current user id to model so we store `created_by`
            $userId = $_SESSION['user_id'] ?? null;

            if ($vinylModel->createFromDTO($dto, $userId)) {
                $this->addSuccessMessage('Kniha byla úspěšně přidána.');
                header("Location: BookController.php?action=index");
                exit;
            } else {
                echo "Chyba při přidávání knihy.";
            }
        }
    }

    public function index() {
        $vinyl = new Vinyl();
        $vinyls = $vinyl->getAll();
        include '../views/vinyls/vinyl_index.php';
    }

    //  Show create form
    public function create() {
        // !!! ZMĚNA: Autorizace: Pokud uživatel není přihlášen, nemá tu co dělat
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro přidání vinylu se musíte nejprve přihlásit.');

            header('Location: AuthController.php?action=login');
            exit;
        }
        include '../views/vinyls/vinyl_create.php';
    }

    public function edit($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        // Kontrola přihlášení
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro úpravu vinylu se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID vinylu k úpravě.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        $vinyl = new Vinyl();
        $vinylData = $vinyl->getById($id);

        if (!$vinylData) {
            $this->addErrorMessage('Požadovaný vinyl nebyl v databázi nalezen.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        // Kontrola vlastnictví
        $currentUserId = $_SESSION['user_id'] ?? null;
        if ($currentUserId === null || (int)$vinylData['created_by'] !== (int)$currentUserId) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tento vinyl, protože nejste jeho autorem.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        $vinyl = $vinylData;
        include '../views/vinyls/vinyl_edit.php';
    }

    public function update($id = null) {
        // !!! ZMĚNA: Autorizace: Pokud uživatel není přihlášen, nemá tu co dělat
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro úpravu vinylu se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy k aktualizaci.');
            header("Location: BookController.php?action=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->addNoticeMessage('Pro úpravu knihy je nutné odeslat formulář.');
            header("Location: BookController.php?action=index");
            exit;
        }

        // Build DTO from POST
        $data = [
            'album_name' => $_POST['album_name'] ?? '',
            'artist' => $_POST['artist'] ?? '',
            'release_year' => $_POST['release_year'] ?? '',
            'genre' => $_POST['genre'] ?? '',
            'price' => $_POST['price'] ?? '',
        ];

        $dto = new VinylDTO($data);

        $vinyl = new Vinyl();
        $existingVinyl = $vinyl->getById($id);

        // Kontrola vlastnictví před provedením aktualizace
        $currentUserId = $_SESSION['user_id'] ?? null;
        if ($currentUserId === null || (int)$existingVinyl['created_by'] !== (int)$currentUserId) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tento vinyl, protože nejste jeho autorem.');
            header("Location: VinylController.php?action=index");
            exit;
        }
        $uploadedImages = [];

        // If files were submitted, try to process them
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadedImages = $this->processImageUploads();
        }

        // Rescue: if no new images were uploaded (or processing yielded none), keep existing images
        if (empty($uploadedImages) && $existingVinyl && !empty($existingVinyl['album_cover'])) {
            $uploadedImages = json_decode($existingVinyl['album_cover'], true);
            if (!is_array($uploadedImages)) {
                $uploadedImages = [$existingVinyl['album_cover']];
            }
        }

        $dto->album_cover = $uploadedImages;

        $isUpdated = $vinyl->updateFromDTO($id, $dto, $currentUserId);

        if ($isUpdated) {
            $this->addSuccessMessage('Kniha byla úspěšně upravena.');
            header("Location: BookController.php?action=index");
            exit;
        }

        $this->addErrorMessage('Nastala chyba. Změny se nepodařilo uložit.');
        header("Location: BookController.php?action=index");
        exit;
    }

    public function show($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy.');
            header("Location: BookController.php?action=index");
            exit;
        }

        $vinyl = new Vinyl();
        $vinylData = $vinyl->getById($id);

        if (!$vinylData) {
            $this->addErrorMessage('Vinyl nebyl nalezen.');
            header("Location: VinylController.php?action=index");
            exit;
        }

        $vinyl = $vinylData;
        include '../views/vinyls/vinyl_show.php';
    }

    public function delete($id = null) {
        // !!! ZMĚNA: Autorizace: Pokud uživatel není přihlášen, nemá tu co dělat
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro smazání knihy se musíte nejprve přihlásit.');
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

        $vinyl = new Vinyl();
        $vinylData = $vinyl->getById($id);

        // 🛡️ Kontrola vlastnictví před smazáním
        $currentUserId = $_SESSION['user_id'] ?? null;
        if ($currentUserId === null || (int)$vinylData['created_by'] !== (int)$currentUserId) {
            $this->addErrorMessage('Nemáte oprávnění smazat tento vinyl, protože nejste jeho autorem.');
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

    // --- Pomocné metody pro systém notifikací ---
    protected function addSuccessMessage($message) {
        $_SESSION['messages']['success'][] = $message;
    }

    protected function addNoticeMessage($message) {
        $_SESSION['messages']['notice'][] = $message;
    }

    protected function addErrorMessage($message) {
        $_SESSION['messages']['error'][] = $message;
    }

    // --- Pomocná metoda pro zpracování nahrávání obrázků ---
    protected function processImageUploads() {
        $uploadedFiles = [];

        // Cesta ke složce, kam se budou obrázky fyzicky ukládat (relativně od tohoto kontroleru)
        $uploadDir = __DIR__ . '/../../public/uploads/';

        // Zkontrolujeme, zda vůbec existuje adresář, pokud ne, vytvoříme ho
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Zkontrolujeme, zda byl odeslán alespoň jeden soubor
        if (isset($_FILES['album_cover']) && !empty($_FILES['album_cover']['name'][0])) {
            $fileCount = count($_FILES['album_cover']['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['album_cover']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['album_cover']['tmp_name'][$i];
                    $originalName = basename($_FILES['album_cover']['name'][$i]);
                    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    if (!in_array($fileExtension, $allowedExtensions)) {
                        continue;
                    }

                    $newName = 'vinyl_' . uniqid() . '_' . substr(md5(mt_rand()), 0, 4) . '.' . $fileExtension;
                    $targetFilePath = $uploadDir . $newName;

                    if (move_uploaded_file($tmpName, $targetFilePath)) {
                        $uploadedFiles[] = $newName; // store only filename
                    }
                }
            }
        }

        return $uploadedFiles;
    }
}

// Instantiate and route the request
$controller = new VinylController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['action']) && $_GET['action'] === 'update') {
        $controller->update($_GET['id'] ?? null);
    } else {
        $controller->store();
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'show') {
    $controller->show($_GET['id'] ?? null);
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $controller->delete($_GET['id'] ?? null);
} elseif (isset($_GET['action']) && $_GET['action'] === 'edit') {
    $controller->edit($_GET['id'] ?? null);
} elseif (isset($_GET['action']) && $_GET['action'] === 'index') {
    $controller->index();
} elseif (isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create();
} else {
    echo 'Neplatný požadavek. Zadejte formulář pro přidání vinylu nebo přejděte na seznam vinylů.';
}
?>
