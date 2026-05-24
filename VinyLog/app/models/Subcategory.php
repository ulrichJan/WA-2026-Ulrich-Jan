<?php

require_once 'Database.php';

/**
 * Třída Subcategory – model pro práci s tabulkou `subcategories`
 *
 * Podkategorie upřesňují hlavní kategorii vinylu. Každá podkategorie patří
 * právě jedné kategorii (sloupec category_id jako cizí klíč).
 *
 * Příklad: Kategorie "Rock" → Podkategorie "Punk", "Metal", "Classic Rock"
 *
 * SQL pro vytvoření tabulky (spustit v phpMyAdmin):
 *   CREATE TABLE `subcategories` (
 *     `id` INT(11) NOT NULL AUTO_INCREMENT,
 *     `category_id` INT(11) NOT NULL,
 *     `name` VARCHAR(100) NOT NULL,
 *     PRIMARY KEY (`id`),
 *     FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 */
class Subcategory {

    /** @var PDO Aktivní databázové připojení */
    private $conn;

    /** @var string Název DB tabulky, s níž model pracuje */
    private $table_name = "subcategories";

    /**
     * Konstruktor – při vytvoření instance automaticky naváže DB spojení.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // -------------------------------------------------------------------------
    // Metoda getAllSubcategories – načtení všech podkategorií
    // -------------------------------------------------------------------------

    /**
     * Vrátí pole všech podkategorií seřazených podle nadřazené kategorie a abecedy.
     *
     * Každý prvek je asociativní array s klíči:
     *   - 'id'          => int  – primární klíč podkategorie
     *   - 'category_id' => int  – ID nadřazené kategorie
     *   - 'name'        => string – název podkategorie
     *
     * JavaScript ve formuláři pak toto pole použije pro dynamické filtrování:
     * po výběru kategorie zobrazí pouze odpovídající podkategorie.
     *
     * @return array Pole podkategorií (může být prázdné, pokud tabulka neobsahuje záznamy)
     */
    public function getAllSubcategories() {
        $stmt = $this->conn->prepare(
            "SELECT * FROM " . $this->table_name . " ORDER BY category_id ASC, name ASC"
        );
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
