<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList {
    protected int $id = 0;
    protected string $nom;
    protected int $nombrePiste = 0;
    protected int $dureeTot = 0;
    protected array $piste = [];

    public function __construct(string $nom, array $pistes = []) {
        $this->nom = $nom;
        $this->piste = $pistes;
        $this->calculer();
    }

    public function calculer(): void {
        $this->nombrePiste = count($this->piste);
        $this->dureeTot = 0;
        foreach ($this->piste as $p) {
            $this->dureeTot += $p->__get('duree');
        }
    }

    public function ajouter(AudioTrack $aud): void {
        $this->piste[] = $aud;
        $this->calculer();
    }

    public function __get(string $n): mixed {
        if ($n === 'liste_pistes') return $this->piste;
        if ($n === 'nb_pistes') return $this->nombrePiste;
        if ($n === 'duree_totale') return $this->dureeTot;
        if (property_exists($this, $n)) {
            return $this->$n;
        }
        throw new InvalidPropertyNameException("$n : propriete inexistante");
    }

    public function __set(string $n, mixed $v): void {
        if ($n === 'id') {
            $this->id = (int)$v;
        } elseif ($n === 'nom') {
            $this->nom = (string)$v;
        } else {
            throw new InvalidPropertyNameException("$n : propriete inexistante");
        }
    }
}
