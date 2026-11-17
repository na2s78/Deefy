<?php

namespace iutnc\deefy\audio\lists;

class Album extends AudioList {
    protected string $artiste = '';
    protected string $dateSortie = '';

    public function __construct(string $nom, array $pistes = []) {
        parent::__construct($nom, $pistes);
    }

    public function setArtiste(string $a): void {
        $this->artiste = $a;
    }

    public function setDateSortie(string $d): void {
        $this->dateSortie = $d;
    }
}
