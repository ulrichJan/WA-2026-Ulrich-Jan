<?php

require_once 'Database.php';
require_once __DIR__ . '/../dto/VinylDTO.php';

class Vinyl {
    private $conn;
    private $table_name = "vinyls";

    public $album_name;
    public $artist;
    public $release_year;
    public $genre;
    public $price;
    public $album_cover;
    public $created_by;
    public $updated_by;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($userId = null) {
        $query = "INSERT INTO " . $this->table_name . " SET album_name=:album_name, artist=:artist, release_year=:release_year, genre=:genre, price=:price, album_cover=:album_cover, created_by=:created_by";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->album_name = htmlspecialchars(strip_tags($this->album_name));
        $this->artist = htmlspecialchars(strip_tags($this->artist));
        $this->release_year = htmlspecialchars(strip_tags($this->release_year));
        $this->genre = htmlspecialchars(strip_tags($this->genre));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->album_cover = json_encode($this->album_cover); // Assuming album_cover is an array of paths
        $this->created_by = $userId;

        // Bind values
        $stmt->bindParam(":album_name", $this->album_name);
        $stmt->bindParam(":artist", $this->artist);
        $stmt->bindParam(":release_year", $this->release_year);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":album_cover", $this->album_cover);
        $stmt->bindParam(":created_by", $this->created_by);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // New: create from DTO
    public function createFromDTO(VinylDTO $dto, $userId = null) {
        $query = "INSERT INTO " . $this->table_name . " SET album_name=:album_name, artist=:artist, release_year=:release_year, genre=:genre, price=:price, album_cover=:album_cover, created_by=:created_by";

        $stmt = $this->conn->prepare($query);

        // Sanitize incoming DTO data
        $album_name = htmlspecialchars(strip_tags($dto->album_name));
        $artist = htmlspecialchars(strip_tags($dto->artist));
        $release_year = htmlspecialchars(strip_tags($dto->release_year ?? ''));
        $genre = htmlspecialchars(strip_tags($dto->genre ?? ''));
        $price = htmlspecialchars(strip_tags($dto->price ?? ''));
        $album_cover = json_encode($dto->album_cover ?? []);

        // Bind values
        $stmt->bindParam(":album_name", $album_name);
        $stmt->bindParam(":artist", $artist);
        $stmt->bindParam(":release_year", $release_year);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":album_cover", $album_cover);
        $stmt->bindValue(":created_by", $userId, $userId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $album_name, $artist, $release_year, $genre, $price, $album_cover = [], $updatedBy = null) {
        $query = "UPDATE " . $this->table_name . " SET album_name = :album_name, artist = :artist, release_year = :release_year, genre = :genre, price = :price, album_cover = :album_cover, updated_by = :updated_by WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $album_name = htmlspecialchars(strip_tags($album_name));
        $artist = htmlspecialchars(strip_tags($artist));
        $release_year = htmlspecialchars(strip_tags($release_year));
        $genre = htmlspecialchars(strip_tags($genre));
        $price = htmlspecialchars(strip_tags($price));
        $album_cover = json_encode($album_cover);

        return $stmt->execute([
            ':id' => $id,
            ':album_name' => $album_name,
            ':artist' => $artist,
            ':release_year' => $release_year,
            ':genre' => $genre,
            ':price' => $price,
            ':album_cover' => $album_cover,
            ':updated_by' => $updatedBy
        ]);
    }

    // New: update from DTO
    public function updateFromDTO($id, VinylDTO $dto, $updatedBy = null) {
        $query = "UPDATE " . $this->table_name . " SET album_name = :album_name, artist = :artist, release_year = :release_year, genre = :genre, price = :price, album_cover = :album_cover, updated_by = :updated_by WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $album_name = htmlspecialchars(strip_tags($dto->album_name));
        $artist = htmlspecialchars(strip_tags($dto->artist));
        $release_year = htmlspecialchars(strip_tags($dto->release_year ?? ''));
        $genre = htmlspecialchars(strip_tags($dto->genre ?? ''));
        $price = htmlspecialchars(strip_tags($dto->price ?? ''));
        $album_cover = json_encode($dto->album_cover ?? []);

        return $stmt->execute([
            ':id' => $id,
            ':album_name' => $album_name,
            ':artist' => $artist,
            ':release_year' => $release_year,
            ':genre' => $genre,
            ':price' => $price,
            ':album_cover' => $album_cover,
            ':updated_by' => $updatedBy
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // Method to handle image uploads
    public function uploadImages($files) {
        $uploadedImages = [];
        $uploadDir = 'uploads/'; // Assuming a directory for uploads

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($files['album_cover']['tmp_name'] as $key => $tmp_name) {
            $file_name = $files['album_cover']['name'][$key];
            $file_tmp = $files['album_cover']['tmp_name'][$key];
            $file_size = $files['album_cover']['size'][$key];
            $file_error = $files['album_cover']['error'][$key];

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

        $this->album_cover = $uploadedImages;
        return $uploadedImages;
    }
}
?>