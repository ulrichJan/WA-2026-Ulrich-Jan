<?php

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
                echo "Kniha byla úspěšně přidána.";
                // Optionally redirect to a success page
                // header("Location: success.php");
            } else {
                echo "Chyba při přidávání knihy.";
            }
        }
    }
}

// Instantiate and call the method
$controller = new BookController();
$controller->store();
?>
