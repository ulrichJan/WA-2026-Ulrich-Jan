<?php

require_once __DIR__ . '/Database.php';

/**
 * Model Comment – správa komentářů k vinylům
 *
 * Komentáře jsou propojeny s konkrétním vinylem (vinyl_id) a uživatelem (user_id).
 * Tabulka: comments (id, vinyl_id, user_id, content, created_at)
 *
 * Vztahy:
 *   comments.vinyl_id → vinyls.id (ON DELETE CASCADE – smazání vinylu smaže i jeho komentáře)
 *   comments.user_id  → users.id  (ON DELETE CASCADE – smazání uživatele smaže jeho komentáře)
 */
class Comment {

    /** @var PDO Aktivní PDO spojení */
    private $conn;

    /** @var string Název tabulky */
    private $table_name = "comments";

    public function __construct() {
        $database   = new Database();
        $this->conn = $database->getConnection();
    }

    // =========================================================================
    // Čtení – načtení komentářů
    // =========================================================================

    /**
     * Vrátí všechny komentáře pro daný vinyl, seřazené od nejnovějšího.
     * LEFT JOIN s tabulkou users přidá jméno a přezdívku komentátora.
     *
     * @param int $vinylId ID vinylu
     * @return array Pole asociativních polí
     */
    public function getByVinylId($vinylId) {
        $stmt = $this->conn->prepare("
            SELECT c.*,
                   u.username,
                   u.nickname
            FROM   " . $this->table_name . " AS c
            LEFT JOIN users AS u ON c.user_id = u.id
            WHERE  c.vinyl_id = :vinyl_id
            ORDER BY c.created_at DESC
        ");
        $stmt->bindValue(':vinyl_id', (int)$vinylId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vrátí jeden komentář dle ID – používá se při ověření vlastnictví před smazáním.
     *
     * @param int $id ID komentáře
     * @return array|false Asociativní pole nebo false pokud neexistuje
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM " . $this->table_name . " WHERE id = :id
        ");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // Zápis – přidání a smazání komentáře
    // =========================================================================

    /**
     * Vloží nový komentář do databáze.
     *
     * @param int    $vinylId ID vinylu, ke kterému komentář patří
     * @param int    $userId  ID přihlášeného uživatele
     * @param string $content Text komentáře (bude ořezán od bílých znaků)
     * @return bool true při úspěchu
     */
    public function create($vinylId, $userId, $content) {
        $stmt = $this->conn->prepare("
            INSERT INTO " . $this->table_name . " (vinyl_id, user_id, content)
            VALUES (:vinyl_id, :user_id, :content)
        ");
        $stmt->bindValue(':vinyl_id', (int)$vinylId,   PDO::PARAM_INT);
        $stmt->bindValue(':user_id',  (int)$userId,    PDO::PARAM_INT);
        $stmt->bindValue(':content',  trim($content),  PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Trvale smaže komentář dle ID.
     *
     * @param int $id ID komentáře ke smazání
     * @return bool true při úspěchu
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("
            DELETE FROM " . $this->table_name . " WHERE id = :id
        ");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
