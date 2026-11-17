<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

class PodcastTrackRenderer extends AudioTrackRenderer {

    public function __construct(PodcastTrack $track) {
        parent::__construct($track);
    }

    protected function renderCompact(): string {
        $titre = htmlspecialchars($this->track->__get('titre'), ENT_QUOTES);
        $auteur = htmlspecialchars($this->track->__get('auteur'), ENT_QUOTES);
        $fichier = htmlspecialchars($this->track->__get('nomFichierAudio'), ENT_QUOTES);

        $html = "<div class='track-item track-podcast'>";
        $html .= "<span class='badge-podcast'>Podcast</span> <span class='track-title'>{$titre}</span> - <span class='track-author'>{$auteur}</span>";
        if ($fichier !== '') {
            $html .= "<div class='track-audio'><audio controls src='{$fichier}' preload='none'></audio></div>";
        }
        $html .= "</div>";
        return $html;
    }

    protected function renderLong(): string {
        $titre = htmlspecialchars($this->track->__get('titre'), ENT_QUOTES);
        $auteur = htmlspecialchars($this->track->__get('auteur'), ENT_QUOTES);
        $date = htmlspecialchars($this->track->__get('date'), ENT_QUOTES);
        $duree = (int)$this->track->__get('duree');
        $fichier = htmlspecialchars($this->track->__get('nomFichierAudio'), ENT_QUOTES);

        $html = "<div class='track-item track-long'>";
        $html .= "<span class='badge-podcast'>Podcast</span> <span class='track-title'>{$titre}</span> - {$auteur} ({$date}) - {$duree} s";
        if ($fichier !== '') {
            $html .= "<div class='track-audio'><audio controls src='{$fichier}' preload='none'></audio></div>";
        }
        $html .= "</div>";
        return $html;
    }
}
