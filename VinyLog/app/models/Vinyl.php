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
    public $category_id;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($userId = null) {
        $query = "INSERT INTO " . $this->table_name . " SET album_name=:album_name, artist=:artist, release_year=:release_year, genre=:genre, price=:price, album_cover=:album_cover, category_id=:category_id, created_by=:created_by";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->album_name = htmlspecialchars(strip_tags($this->album_name));
        $this->artist = htmlspecialchars(strip_tags($this->artist));
        $this->release_year = htmlspecialchars(strip_tags($this->release_year));
        $this->genre = htmlspecialchars(strip_tags($this->genre));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->album_cover = json_encode($this->album_cover); // Assuming album_cover is an array of paths
        $this->created_by = $userId;
        $this->category_id = $this->category_id ?? null;

        // Bind values
        $stmt->bindParam(":album_name", $this->album_name);
        $stmt->bindParam(":artist", $this->artist);
        $stmt->bindParam(":release_year", $this->release_year);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":album_cover", $this->album_cover);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":created_by", $this->created_by);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // New: create from DTO
    public function createFromDTO(VinylDTO $dto, $userId = null) {
        $query = "INSERT INTO " . $this->table_name . " SET album_name=:album_name, artist=:artist, release_year=:release_year, genre=:genre, price=:price, album_cover=:album_cover, category_id=:category_id, subcategory_id=:subcategory_id, created_by=:created_by";

        $stmt = $this->conn->prepare($query);

        // Sanitize incoming DTO data
        $album_name = htmlspecialchars(strip_tags($dto->album_name));
        $artist = htmlspecialchars(strip_tags($dto->artist));
        $release_year = htmlspecialchars(strip_tags($dto->release_year ?? ''));
        $genre = htmlspecialchars(strip_tags($dto->genre ?? ''));
        $price = htmlspecialchars(strip_tags($dto->price ?? ''));
        $album_cover = json_encode($dto->album_cover ?? []);
        $category_id = isset($dto->category) && $dto->category ? (int)$dto->category : null;
        $subcategory_id = isset($dto->subcategory) && $dto->subcategory ? (int)$dto->subcategory : null;

        // Bind values
        $stmt->bindParam(":album_name", $album_name);
        $stmt->bindParam(":artist", $artist);
        $stmt->bindParam(":release_year", $release_year);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":album_cover", $album_cover);
        $stmt->bindValue(":category_id", $category_id, $category_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(":subcategory_id", $subcategory_id, $subcategory_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(":created_by", $userId, $userId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT vinyls.*, categories.name AS category_name, subcategories.name AS subcategory_name
                  FROM vinyls
                  LEFT JOIN categories ON vinyls.category_id = categories.id
                  LEFT JOIN subcategories ON vinyls.subcategory_id = subcategories.id
                  ORDER BY vinyls.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT vinyls.*, categories.name AS category_name, subcategories.name AS subcategory_name
                  FROM vinyls
                  LEFT JOIN categories ON vinyls.category_id = categories.id
                  LEFT JOIN subcategories ON vinyls.subcategory_id = subcategories.id
                  WHERE vinyls.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $album_name, $artist, $release_year, $genre, $price, $album_cover = [], $updatedBy = null) {
        $query = "UPDATE " . $this->table_name . " SET album_name = :album_name, artist = :artist, release_year = :release_year, genre = :genre, price = :price, album_cover = :album_cover, category_id = :category_id, updated_by = :updated_by WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $album_name = htmlspecialchars(strip_tags($album_name));
        $artist = htmlspecialchars(strip_tags($artist));
        $release_year = htmlspecialchars(strip_tags($release_year));
        $genre = htmlspecialchars(strip_tags($genre));
        $price = htmlspecialchars(strip_tags($price));
        $album_cover = json_encode($album_cover);
        $category_id = $this->category_id ?? null;

        return $stmt->execute([
            ':id' => $id,
            ':album_name' => $album_name,
            ':artist' => $artist,
            ':release_year' => $release_year,
            ':genre' => $genre,
            ':price' => $price,
            ':album_cover' => $album_cover,
            ':category_id' => $category_id,
            ':updated_by' => $updatedBy
        ]);
    }

    // New: update from DTO
    public function updateFromDTO($id, VinylDTO $dto, $updatedBy = null) {
        $query = "UPDATE " . $this->table_name . " SET album_name = :album_name, artist = :artist, release_year = :release_year, genre = :genre, price = :price, album_cover = :album_cover, category_id = :category_id, subcategory_id = :subcategory_id, updated_by = :updated_by WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $album_name = htmlspecialchars(strip_tags($dto->album_name));
        $artist = htmlspecialchars(strip_tags($dto->artist));
        $release_year = htmlspecialchars(strip_tags($dto->release_year ?? ''));
        $genre = htmlspecialchars(strip_tags($dto->genre ?? ''));
        $price = htmlspecialchars(strip_tags($dto->price ?? ''));
        $album_cover = json_encode($dto->album_cover ?? []);
        $category_id = isset($dto->category) && $dto->category ? (int)$dto->category : null;
        $subcategory_id = isset($dto->subcategory) && $dto->subcategory ? (int)$dto->subcategory : null;

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':album_name', $album_name);
        $stmt->bindParam(':artist', $artist);
        $stmt->bindParam(':release_year', $release_year);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':album_cover', $album_cover);
        $stmt->bindValue(':category_id', $category_id, $category_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':subcategory_id', $subcategory_id, $subcategory_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $updatedBy, $updatedBy === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // Helper: returns info about an uploaded file (web URL, filesystem path, exists)
    public static function getUploadInfo($filename) {
        $filename = trim((string)$filename);
        $filename = basename($filename);

        // Resolve filesystem path to public/uploads
        $uploadsFs = realpath(__DIR__ . '/../../public/uploads');
        if ($uploadsFs === false) {
            // fallback relative path
            $uploadsFs = __DIR__ . '/../../public/uploads';
        }

        // Normalize slashes for comparisons
        $normalizedFs = str_replace('\\', '/', realpath($uploadsFs) ?: $uploadsFs);
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT']) : null;

        $urlBase = '/public/uploads';

        // Prefer deriving from project root (more stable) and fall back to uploads fs check
        $projectRoot = realpath(__DIR__ . '/../../');
        $normalizedProject = $projectRoot ? str_replace('\\', '/', $projectRoot) : null;
        if ($docRoot && $normalizedProject && strpos($normalizedProject, $docRoot) === 0) {
            $relativeProj = '/' . trim(substr($normalizedProject, strlen($docRoot)), '/');
            $urlBase = $relativeProj . '/public/uploads';
        } elseif ($docRoot && strpos($normalizedFs, $docRoot) === 0) {
            $relative = substr($normalizedFs, strlen($docRoot));
            $relative = str_replace('\\', '/', $relative);
            $relative = '/' . trim($relative, '/');
            $urlBase = $relative . '/uploads';
            // If uploads are already inside a `public/uploads` folder, ensure we don't duplicate
            $urlBase = str_replace('/public/uploads/uploads', '/public/uploads', $urlBase);
        } else {
            // Try to derive project base from script name (works when served from subpath)
            if (isset($_SERVER['SCRIPT_NAME'])) {
                $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
                if (strpos($script, '/app/') !== false) {
                    $base = substr($script, 0, strpos($script, '/app/'));
                    $urlBase = rtrim($base, '/') . '/public/uploads';
                } elseif (strpos($script, '/public/') !== false) {
                    $base = substr($script, 0, strpos($script, '/public/'));
                    $urlBase = rtrim($base, '/') . '/public/uploads';
                }
            }
        }

        // Make URL safe
        $url = $urlBase . '/' . rawurlencode($filename);
        // Normalize duplicate slashes and ensure a single leading slash
        $url = preg_replace('#/+#', '/', $url);
        $url = '/' . ltrim($url, '/');

        // Filesystem check
        $fsPathCandidate = rtrim($normalizedFs, '/') . '/' . $filename;
        $exists = file_exists($fsPathCandidate);

        return ['url' => $url, 'path' => $fsPathCandidate, 'exists' => $exists];
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