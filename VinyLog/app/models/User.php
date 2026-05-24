<?php

/**
 * Třída User – model pro práci s uživatelskými účty
 *
 * Zodpovídá za registraci, vyhledávání a načítání uživatelů z DB tabulky `users`.
 * Hesla jsou vždy hashována – do databáze se nikdy neukládá heslo v čitelné podobě.
 *
 * Struktura tabulky `users` (zjednodušeně):
 *   id, username, email, password (hash), first_name, last_name, nickname,
 *   is_admin (0/1), created_at
 */
class User {

    /** @var PDO Aktivní databázové připojení (předáno zvenku, ne vytvořeno uvnitř) */
    private $db;

    /**
     * Konstruktor přijímá PDO spojení jako závislost (Dependency Injection).
     * Model sám o sobě neví, jak se připojit – dostane hotové spojení z AuthControlleru.
     *
     * @param PDO $db Aktivní PDO instance
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // -------------------------------------------------------------------------
    // Metoda register – vytvoření nového uživatelského účtu
    // -------------------------------------------------------------------------

    /**
     * Zaregistruje nového uživatele do databáze.
     *
     * Před uložením ověří, zda email není již obsazen (volá findByEmail).
     * Heslo je bezpečně zahashováno pomocí password_hash() s algoritmem bcrypt.
     *
     * @param string      $username  Uživatelské jméno (přihlašovací)
     * @param string      $email     E-mail (musí být unikátní)
     * @param string      $password  Heslo v čitelné podobě (bude ihned zahashováno)
     * @param string|null $firstName Křestní jméno (volitelné)
     * @param string|null $lastName  Příjmení (volitelné)
     * @param string|null $nickname  Přezdívka zobrazená v UI (volitelné)
     * @return bool true při úspěšné registraci, false pokud email již existuje
     */
    public function register(
        string $username,
        string $email,
        string $password,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $nickname = null
    ): bool {
        // Ověříme, zda uživatel s tímto e-mailem již neexistuje.
        // Pokud ano, registraci zamítneme – duplicitní emaily nejsou povoleny.
        if ($this->findByEmail($email)) {
            return false;
        }

        // BEZPEČNOST: Heslo nikdy neukládáme v čitelné podobě!
        // password_hash() vygeneruje bezpečný bcrypt hash (PASSWORD_DEFAULT).
        // Každé volání vytvoří jiný hash (díky zabudovanému "salt"), ale
        // password_verify() je schopné hash a heslo správně porovnat.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Připravený parametrizovaný dotaz – chrání před SQL injection
        $sql = "INSERT INTO users (username, email, password, first_name, last_name, nickname)
                VALUES (:username, :email, :password, :first_name, :last_name, :nickname)";
        $stmt = $this->db->prepare($sql);

        // execute() přijme asociativní pole parametrů; PDO je bezpečně vloží do dotazu
        return $stmt->execute([
            ':username'   => $username,
            ':email'      => $email,
            ':password'   => $hashedPassword,
            ':first_name' => $firstName,
            ':last_name'  => $lastName,
            ':nickname'   => $nickname,
        ]);
    }

    // -------------------------------------------------------------------------
    // Metoda findByEmail – vyhledání uživatele dle e-mailu (používá se při přihlášení)
    // -------------------------------------------------------------------------

    /**
     * Najde uživatele v DB podle e-mailové adresy.
     *
     * Vrací celý řádek včetně sloupce `password` (hash) a `is_admin`,
     * protože AuthController potřebuje hash pro password_verify() a is_admin pro session.
     *
     * @param string $email E-mailová adresa
     * @return array|false Asociativní pole s daty uživatele, nebo false pokud neexistuje
     */
    public function findByEmail(string $email) {
        // SELECT * vrátí všechny sloupce (id, username, email, password, is_admin, …)
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        // fetch() vrátí první (a jediný) nalezený řádek, nebo false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------------------------
    // Metoda findById – načtení veřejných dat uživatele (bez hesla)
    // -------------------------------------------------------------------------

    /**
     * Vrátí veřejné informace o uživateli dle jeho ID.
     *
     * Sloupec `password` záměrně NENÍ zahrnut – tato metoda slouží
     * k zobrazení profilu, ne k autentizaci.
     *
     * @param int $id ID uživatele
     * @return array|false Asociativní pole nebo false
     */
    public function findById(int $id) {
        $sql = "SELECT id, username, email, first_name, last_name, nickname, created_at
                FROM users
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
