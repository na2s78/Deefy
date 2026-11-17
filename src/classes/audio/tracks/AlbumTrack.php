<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;

class AlbumTrack extends AudioTrack {
    protected string $album = '';
    protected int $annee = 0;
    protected int $numero_piste = 0;

    public function __construct(string $titre, string $cheminFichier = '') {
        parent::__construct($titre, $cheminFichier);
    }

    public function __get(string $n): mixed {
        if ($n === 'album') return $this->album;
        if ($n === 'annee') return $this->annee;
        if ($n === 'numero_piste') return $this->numero_piste;
        return parent::__get($n);
    }

    public function __set(string $n, mixed $v): void {
        if ($n === 'album') {
            $this->album = (string)$v;
        } elseif ($n === 'annee') {
            $this->annee = (int)$v;
        } elseif ($n === 'numero_piste') {
            $this->numero_piste = (int)$v;
        } else {
            parent::__set($n, $v);
        }
    }

    public function __toString(): string {
        $res = $this->titre . ' - by ' . $this->auteur;
        if ($this->album !== '') {
            $res .= ' (album : ' . $this->album . ')';
        }
        return $res;
    }
}
