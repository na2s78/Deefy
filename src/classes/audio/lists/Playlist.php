<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class Playlist extends AudioList {

    public function __construct(string $n, array $tab = []) {
        parent::__construct($n, $tab);
    }

    public function ajouterPiste(AudioTrack $p): void {
        $this->piste[] = $p;
        $this->calculer();
    }

    public function supprimerElement(int $ind): void {
        if (isset($this->piste[$ind])) {
            unset($this->piste[$ind]);
            $this->piste = array_values($this->piste);
            $this->calculer();
        }
    }

    public function ajouterListeElem(array $t): void {
        foreach ($t as $piste) {
            $this->ajouterPiste($piste);
        }
    }
}
