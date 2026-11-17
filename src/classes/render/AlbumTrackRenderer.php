<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

class AlbumTrackRenderer extends AudioTrackRenderer {

    public function __construct(AlbumTrack $track) {
        parent::__construct($track);
    }

    protected function renderCompact(): string {
        $titre = htmlspecialchars($this->track->__get('titre'), ENT_QUOTES);
        $auteur = htmlspecialchars($this->track->__get('auteur'), ENT_QUOTES);
        $fichier = htmlspecialchars($this->track->__get('nomFichierAudio'), ENT_QUOTES);

        $html = "<div class='track-item track-album'>";
        $html .= "<span class='track-title'>{$titre}</span> - <span class='track-author'>{$auteur}</span>";
        if ($fichier !== '') {
            $html .= "<div class='track-audio'><audio controls src='{$fichier}' preload='none'></audio></div>";
        }
        $html .= "</div>";
        return $html;
    }

    protected function renderLong(): string {
        $titre = htmlspecialchars($this->track->__get('titre'), ENT_QUOTES);
        $auteur = htmlspecialchars($this->track->__get('auteur'), ENT_QUOTES);
        $album = htmlspecialchars($this->track->__get('album'), ENT_QUOTES);
        $duree = (int)$this->track->__get('duree');
        $fichier = htmlspecialchars($this->track->__get('nomFichierAudio'), ENT_QUOTES);

        $html = "<div class='track-item track-long'>";
        $html .= "<span class='track-title'>{$titre}</span> - {$auteur} (Album : {$album}) - {$duree} s";
        if ($fichier !== '') {
            $html .= "<div class='track-audio'><audio controls src='{$fichier}' preload='none'></audio></div>";
        }
        $html .= "</div>";
        return $html;
    }
}
