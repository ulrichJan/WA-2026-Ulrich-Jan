<?php
session_start();

require_once '../models/Book.php';

class BookController {

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $book = new Book();

            $book->title = $_POST['title'] ?? '';
            $book->author = $_POST['author'] ?? '';
            $book->isbn = $_POST['isbn'] ?? '';
            $book->published_date = $_POST['published_date'] ?? '';
            $book->price = $_POST['price'] ?? '';
            $book->description = $_POST['description'] ?? '';

            // Handle image uploads
            if (isset($_FILES['images'])) {
                $book->uploadImages($_FILES);
            }

            if ($book->create()) {
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

    public function edit($id = null) {
        if ($id === null) {
            $id = $_GET['id'] ?? null;
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

        $book = $bookData;
        include '../views/books/book_edit.php';
    }

    public function update($id = null) {
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

        $title = htmlspecialchars($_POST['title'] ?? '');
        $author = htmlspecialchars($_POST['author'] ?? '');
        $isbn = htmlspecialchars($_POST['isbn'] ?? '');
        $published_date = htmlspecialchars($_POST['published_date'] ?? '');
        $price = htmlspecialchars($_POST['price'] ?? '');
        $description = htmlspecialchars($_POST['description'] ?? '');

        $book = new Book();
        $existingBook = $book->getById($id);
        $uploadedImages = [];

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $uploadedImages = $book->uploadImages($_FILES);
        } elseif ($existingBook && !empty($existingBook['images'])) {
            $uploadedImages = json_decode($existingBook['images'], true);
            if (!is_array($uploadedImages)) {
                $uploadedImages = [$existingBook['images']];
            }
        }

        $isUpdated = $book->update(
            $id,
            $title,
            $author,
            $isbn,
            $published_date,
            $price,
            $description,
            $uploadedImages
        );

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
        if ($id === null) {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy ke smazání.');
            header("Location: BookController.php?action=index");
            exit;
        }

        $book = new Book();
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
} else {
    echo 'Neplatný požadavek. Zadejte formulář pro přidání knihy nebo přejděte na seznam knih.';
}
?>
