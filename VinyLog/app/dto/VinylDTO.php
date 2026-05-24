<?php

/**
 * Třída VinylDTO – Data Transfer Object pro přenos dat o vinylu
 *
 * DTO (Data Transfer Object) je jednoduchý přepravní kontejner bez logiky.
 * Jeho úkolem je přenést data mezi vrstvami aplikace (Controller → Model)
 * bez přímého předávání raw $_POST pole.
 *
 * Výhody oproti přímé práci s $_POST:
 *  - centralizovaná definice datové struktury (jasno, co vinyl obsahuje)
 *  - controller připraví data jednou, model je dostane čistě
 *  - snadné testování a rozšiřování
 */
class VinylDTO {

    // -------------------------------------------------------------------------
    // Vlastnosti – přesně odpovídají sloupcům v tabulce `vinyls`
    // -------------------------------------------------------------------------

    /** @var string Název alba (povinné) */
    public $album_name;

    /** @var string Jméno umělce / kapely (povinné) */
    public $artist;

    /** @var string|null Rok vydání alba (volitelné) */
    public $release_year;

    /** @var string|null Hudební žánr (volitelné) */
    public $genre;

    /** @var string|null Cena v Kč (volitelné) */
    public $price;

    /** @var array Seznam názvů souborů obálek alba; uloženo jako JSON do DB */
    public $album_cover;

    /** @var int|null ID kategorie (cizí klíč do tabulky categories) */
    public $category;

    /** @var int|null ID podkategorie (cizí klíč do tabulky subcategories) */
    public $subcategory;

    // -------------------------------------------------------------------------
    // Konstruktor – naplnění DTO z asociativního pole
    // -------------------------------------------------------------------------

    /**
     * Vytvoří instanci DTO a nastaví vlastnosti z předaného pole.
     *
     * Operátor ?? (null coalescing) zajistí výchozí hodnoty pro volitelné položky,
     * takže controller nemusí validovat existenci klíčů před předáním.
     *
     * @param array $data Asociativní pole, typicky z $_POST ve VinylControlleru
     */
    public function __construct($data = []) {
        $this->album_name  = $data['album_name']  ?? '';
        $this->artist      = $data['artist']      ?? '';
        $this->release_year = $data['release_year'] ?? '';
        $this->genre       = $data['genre']       ?? '';
        $this->price       = $data['price']       ?? '';
        $this->album_cover = $data['album_cover'] ?? [];
        $this->category    = $data['category']    ?? null;
        $this->subcategory = $data['subcategory'] ?? null;
    }
}
