<?php

class VinylDTO {
    public $album_name;
    public $artist;
    public $release_year;
    public $genre;
    public $price;
    public $album_cover;

    public function __construct($data = []) {
        $this->album_name = $data['album_name'] ?? '';
        $this->artist = $data['artist'] ?? '';
        $this->release_year = $data['release_year'] ?? '';
        $this->genre = $data['genre'] ?? '';
        $this->price = $data['price'] ?? '';
        $this->album_cover = $data['album_cover'] ?? [];
    }
}
