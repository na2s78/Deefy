<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack {
    public function __construct(string $titre, string $cheminFichier = '') {
        parent::__construct($titre, $cheminFichier);
        $this->date = date('Y-m-d');
    }

    public function __toString(): string {
        return $this->titre . ($this->auteur !== '' ? ' - par ' . $this->auteur : '') . ' [Podcast]';
    }
}
