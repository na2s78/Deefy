<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack {
    protected int $id = 0;
    protected string $titre;
    protected string $auteur = '';
    protected string $genre = '';
    protected int $duree = 0;
    protected string $nomFichierAudio = '';
    protected string $date = '';

    public function __construct(string $titre, string $cheminFichier = '') {
        $this->titre = $titre;
        $this->nomFichierAudio = $cheminFichier;
    }

    public function __get(string $n): mixed {
        if ($n === 'fichierAudio') {
            return $this->nomFichierAudio;
        }
        if (property_exists($this, $n)) {
            return $this->$n;
        }
        throw new InvalidPropertyNameException("$n : propriete invalide");
    }

    public function __set(string $n, mixed $v): void {
        if ($n === 'id') {
            $this->id = (int)$v;
        } elseif ($n === 'titre') {
            $this->titre = (string)$v;
        } elseif ($n === 'auteur') {
            $this->auteur = (string)$v;
        } elseif ($n === 'genre') {
            $this->genre = (string)$v;
        } elseif ($n === 'duree') {
            if ((int)$v >= 0) {
                $this->duree = (int)$v;
            } else {
                throw new InvalidPropertyValueException("$v : la duree doit etre positive");
            }
        } elseif ($n === 'nomFichierAudio' || $n === 'fichierAudio') {
            $this->nomFichierAudio = (string)$v;
        } elseif ($n === 'date') {
            $this->date = (string)$v;
        } else {
            throw new InvalidPropertyNameException("$n : propriete invalide");
        }
    }

    public function __toString(): string {
        return $this->titre . ($this->auteur !== '' ? ' - ' . $this->auteur : '');
    }
}
