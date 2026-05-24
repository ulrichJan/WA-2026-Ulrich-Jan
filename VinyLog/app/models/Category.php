<?php

require_once 'Database.php';

/**
 * Třída Category – model pro práci s tabulkou `categories`
 *
 * Uchovává metody pro čtení kategorií z databáze.
 * Kategorie jsou pevně dány v DB tabulce – umožňují konzistentní třídění
 * vinylů bez rizika překlepů nebo duplicit, které by nastaly při volném textu.
 */
class Category {

    /** @var PDO Aktivní databázové připojení */
    private $conn;

    /** @var string Název DB tabulky, s níž model pracuje */
    private $table_name = "categories";

    /**
     * Konstruktor – při vytvoření instance automaticky naváže DB spojení.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // -------------------------------------------------------------------------
    // Metoda getAllCategories – načtení všech kategorií ze z DB
    // -------------------------------------------------------------------------

    /**
     * Vrátí pole všech kategorií seřazených abecedně podle názvu.
     *
     * Každý prvek pole je asociativní array s klíči:
     *   - 'id'   => int  – primární klíč kategorie
     *   - 'name' => string – název kategorie (např. "Rock", "Jazz")
     *
     * @return array Pole kategorií, nebo prázdné pole při chybě
     */
    public function getAllCategories() {
        // Připravený dotaz (prepared statement) chrání před SQL injection.
        // Řadíme abecedně, aby select-box ve formuláři byl přehledný.
        $stmt = $this->conn->prepare(
            "SELECT * FROM " . $this->table_name . " ORDER BY name ASC"
        );
        $stmt->execute();

        // PDO::FETCH_ASSOC vrátí data jako asociativní pole (klíč = název sloupce)
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
