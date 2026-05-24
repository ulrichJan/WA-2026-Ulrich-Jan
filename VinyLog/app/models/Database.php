<?php

/**
 * Třída Database – správa připojení k databázi pomocí PDO
 *
 * PDO (PHP Data Objects) je moderní a bezpečné rozhraní pro práci s databázemi v PHP.
 * Podporuje více typů databází (MySQL, PostgreSQL, SQLite…) a chrání před SQL injection
 * díky tzv. "prepared statements" (připravené dotazy s parametry).
 *
 * Tato třída zapouzdřuje přihlašovací údaje a vrací aktivní PDO spojení.
 */
class Database {

    // -------------------------------------------------------------------------
    // Přihlašovací údaje pro připojení k databázi
    // Tyto hodnoty odpovídají výchozímu nastavení XAMPP na localhostu.
    // -------------------------------------------------------------------------

    /** @var string Hostname nebo IP adresa databázového serveru */
    private $host = "localhost";

    /** @var string Název databáze, do které se připojujeme */
    private $db_name = "wa_2026_ju_01";

    /** @var string Uživatelské jméno pro přístup k databázi */
    private $username = "root";

    /** @var string Heslo k databázi (prázdné u výchozí instalace XAMPP) */
    private $password = "";

    /** @var PDO|null Aktivní PDO objekt; null pokud připojení selhalo */
    public $conn;

    // -------------------------------------------------------------------------
    // Metoda getConnection – navázání (nebo obnova) připojení k DB
    // -------------------------------------------------------------------------

    /**
     * Vrátí PDO připojení k databázi.
     *
     * Každé volání vytvoří nové PDO spojení (případné staré se přepíše).
     * To je záměr – jednoduché řešení vhodné pro školní projekt.
     * V produkci by se používal connection-pool nebo singleton pattern.
     *
     * @return PDO|null PDO objekt při úspěchu, null při chybě připojení
     */
    public function getConnection() {

        // Vynulujeme případné předchozí spojení (uvolníme paměť)
        $this->conn = null;

        try {
            // Sestavíme DSN (Data Source Name) – řetězec popisující typ a cíl připojení
            // Formát: "mysql:host=<server>;dbname=<databáze>"
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            // Nastavíme PDO do režimu výjimek:
            // místo tichého selhání vrátí výjimku (PDOException) při chybě dotazu.
            // Díky tomu odhalíme chyby hned, místo abychom dostávali záhadná false/null.
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Nastavíme kódování spojení na UTF-8, aby správně fungovaly české znaky
            $this->conn->exec("SET NAMES 'utf8mb4'");

        } catch (PDOException $exception) {
            // Pokud připojení selže (špatné heslo, server neběží apod.),
            // zobrazíme chybovou zprávu a vrátíme null.
            echo "Chyba připojení k databázi: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
