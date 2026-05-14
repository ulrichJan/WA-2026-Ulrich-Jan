<?php

require_once 'Database.php';

class Subcategory {
    private $conn;
    private $table_name = "subcategories";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllSubcategories() {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " ORDER BY category_id ASC, name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
