<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

class AudioTrackRenderer implements Renderer {
    protected AudioTrack $track;

    public function __construct(AudioTrack $track) {
        $this->track = $track;
    }

    public function render(int $selector = self::COMPACT): string {
        switch ($selector) {
            case self::LONG:
                return $this->renderLong();
            case self::COMPACT:
            default:
                return $this->renderCompact();
        }
    }

    protected function renderCompact(): string {
        $titre = htmlspecialchars($this->track->__get('titre'), ENT_QUOTES);
        $auteur = htmlspecialchars($this->track->__get('auteur'), ENT_QUOTES);
        $fichier = htmlspecialchars($this->track->__get('nomFichierAudio'), ENT_QUOTES);

        $html = "<div class='track-item'>";
        $html .= "<span class='track-title'>{$titre}</span>";
        if ($auteur !== '') {
            $html .= " - <span class='track-author'>{$auteur}</span>";
        }
        if ($fichier !== '') {
            $html .= "<div class='track-audio'><audio controls src='{$fichier}' preload='none'></audio></div>";
        }
        $html .= "</div>";
        return $html;
    }

    protected function renderLong(): string {
        return $this->renderCompact();
    }
}
