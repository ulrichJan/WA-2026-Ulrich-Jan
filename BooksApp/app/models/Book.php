<?php

require_once 'Database.php';

class Book {
    private $conn;
    private $table_name = "books";

    public $title;
    public $author;
    public $isbn;
    public $published_date;
    public $price;
    public $description;
    public $images;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET title=:title, author=:author, isbn=:isbn, published_date=:published_date, price=:price, description=:description, images=:images";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->published_date = htmlspecialchars(strip_tags($this->published_date));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->images = json_encode($this->images); // Assuming images is an array of paths

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":isbn", $this->isbn);
        $stmt->bindParam(":published_date", $this->published_date);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":images", $this->images);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Method to handle image uploads
    public function uploadImages($files) {
        $uploadedImages = [];
        $uploadDir = 'uploads/'; // Assuming a directory for uploads

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($files['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $files['images']['name'][$key];
            $file_tmp = $files['images']['tmp_name'][$key];
            $file_size = $files['images']['size'][$key];
            $file_error = $files['images']['error'][$key];

            // Basic validation
            if ($file_error === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($file_ext, $allowed_ext) && $file_size < 5000000) { // 5MB limit
                    $new_file_name = uniqid() . '.' . $file_ext;
                    $file_path = $uploadDir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $file_path)) {
                        $uploadedImages[] = $file_path;
                    }
                }
            }
        }

        $this->images = $uploadedImages;
        return $uploadedImages;
    }
}
?>