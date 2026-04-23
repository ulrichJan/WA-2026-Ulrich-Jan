<?php
session_start();

require_once '../models/Book.php';
require_once '../dto/BookDTO.php';

class BookController {

    public function store() {
        // !!! ZMĚNA: Autorizace: Pokud uživatel není přihlášen, nemá tu co dělat
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro přidání knihy se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Build DTO from POST
            $data = [
                'title' => $_POST['title'] ?? '',
                'author' => $_POST['author'] ?? '',
                'isbn' => $_POST['isbn'] ?? '',
                'published_date' => $_POST['published_date'] ?? '',
                'price' => $_POST['price'] ?? '',
                'description' => $_POST['description'] ?? '',
            ];

            $dto = new BookDTO($data);

            $bookModel = new Book();

            // Handle image uploads and attach to DTO (processed by controller helper)
            $uploaded = $this->processImageUploads();
            $dto->images = $uploaded;

            // Pass current user id to model so we store `created_by`
            $userId = $_SESSION['user_id'] ?? null;

            if ($bookModel->createFromDTO($dto, $userId)) {
                $this->addSuccessMessage('Kniha byla úspěšně přidána.');
                header("Location: BookController.php?action=index");
                exit;
            } else {
                echo "Chyba při přidávání knihy.";
            }
        }
    }

    public function index() {
        $book = new Book();
        $books = $book->getAll();
        include '../views/books/book_index.php';
    }

    //  Show create form
    public function create() {
        // !!! ZMĚNA: Autorizace: Pokud uživatel není přihlášen, nemá tu co dělat
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro přidání knihy se musíte nejprve přihlásit.');

            header('Location: AuthController.php?action=login');
            exit;
        }
        include '../views/books/book_create.php';
    }

    public function edit($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        // Kontrola přihlášení
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro úpravu knihy se musíte nejprve přihlásit.');
            header('Location: AuthController.php?action=login');
            exit;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy k úpravě.');
            header("Location: BookController.php?action=index");
            exit;
        }

        $book = new Book();
        $bookData = $book->getById($id);

        if (!$bookData) {
            $this->addErrorMessage('Požadovaná kniha nebyla v databázi nalezena.');
            header("Location: BookController.php?action=index");
            exit;
        }

        // Kontrola vlastnictví
        $currentUserId = $_SESSION['user_id'] ?? null;
        if ($currentUserId === null || (int)$bookData['created_by'] !== (int)$currentUserId) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tuto knihu, protože nejste jejím autorem.');
            header("Location: BookController.php?action=index");
            exit;
        }

        $book = $bookData;
        include '../views/books/book_edit.php';
    }

    public function update($id = null) {
        // !!! ZMĚNA: Autorizace: Pokud uživatel není přihlášen, nemá tu co dělat
        if (!isset($_SESSION['user_id'])) {
            $this->addErrorMessage('Pro úpravu knihy se musíte nejprve přihlásit.');
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
            'title' => $_POST['title'] ?? '',
            'author' => $_POST['author'] ?? '',
            'isbn' => $_POST['isbn'] ?? '',
            'published_date' => $_POST['published_date'] ?? '',
            'price' => $_POST['price'] ?? '',
            'description' => $_POST['description'] ?? '',
        ];

        $dto = new BookDTO($data);

        $book = new Book();
        $existingBook = $book->getById($id);

        // 🛡️ Kontrola vlastnictví před provedením aktualizace
        $currentUserId = $_SESSION['user_id'] ?? null;
        if ($currentUserId === null || (int)$existingBook['created_by'] !== (int)$currentUserId) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tuto knihu, protože nejste jejím autorem.');
            header("Location: BookController.php?action=index");
            exit;
        }
        $uploadedImages = [];

        // If files were submitted, try to process them
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadedImages = $this->processImageUploads();
        }

        // Rescue: if no new images were uploaded (or processing yielded none), keep existing images
        if (empty($uploadedImages) && $existingBook && !empty($existingBook['images'])) {
            $uploadedImages = json_decode($existingBook['images'], true);
            if (!is_array($uploadedImages)) {
                $uploadedImages = [$existingBook['images']];
            }
        }

        $dto->images = $uploadedImages;

        $isUpdated = $book->updateFromDTO($id, $dto);

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

        $book = new Book();
        $bookData = $book->getById($id);

        if (!$bookData) {
            $this->addErrorMessage('Kniha nebyla nalezena.');
            header("Location: BookController.php?action=index");
            exit;
        }

        $book = $bookData;
        include '../views/books/book_show.php';
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
            $this->addErrorMessage('Nebylo zadáno ID knihy ke smazání.');
            header("Location: BookController.php?action=index");
            exit;
        }

        $book = new Book();
        $bookData = $book->getById($id);

        // 🛡️ Kontrola vlastnictví před smazáním
        $currentUserId = $_SESSION['user_id'] ?? null;
        if ($currentUserId === null || (int)$bookData['created_by'] !== (int)$currentUserId) {
            $this->addErrorMessage('Nemáte oprávnění smazat tuto knihu, protože nejste jejím autorem.');
            header("Location: BookController.php?action=index");
            exit;
        }

        $isDeleted = $book->delete($id);

        if ($isDeleted) {
            $this->addSuccessMessage('Kniha byla trvale smazána z databáze.');
        } else {
            $this->addErrorMessage('Nastala chyba. Knihu se nepodařilo smazat.');
        }
        header("Location: BookController.php?action=index");
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
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $fileCount = count($_FILES['images']['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['images']['tmp_name'][$i];
                    $originalName = basename($_FILES['images']['name'][$i]);
                    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    if (!in_array($fileExtension, $allowedExtensions)) {
                        continue;
                    }

                    $newName = 'book_' . uniqid() . '_' . substr(md5(mt_rand()), 0, 4) . '.' . $fileExtension;
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
$controller = new BookController();

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
    echo 'Neplatný požadavek. Zadejte formulář pro přidání knihy nebo přejděte na seznam knih.';
}
?>
