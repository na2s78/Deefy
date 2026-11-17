<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class AudioListRenderer implements Renderer {
    protected AudioList $liste;

    public function __construct(AudioList $liste) {
        $this->liste = $liste;
    }

    public function render(int $selector = self::COMPACT): string {
        $nom = htmlspecialchars($this->liste->__get('nom'), ENT_QUOTES);
        $nb = (int)$this->liste->__get('nombrePiste');
        $dureeTot = (int)$this->liste->__get('dureeTot');

        $minutes = floor($dureeTot / 60);
        $secondes = $dureeTot % 60;
        $dureeTexte = sprintf("%d min %02d s", $minutes, $secondes);

        $html = "<div class='playlist-view'>";
        $html .= "<div class='playlist-header'>";
        $html .= "<h2>{$nom}</h2>";
        $html .= "<p class='playlist-stats'>{$nb} piste(s) - Durée totale : {$dureeTexte} ({$dureeTot} s)</p>";
        $html .= "</div>";

        if ($nb === 0) {
            $html .= "<p class='empty-message'>Cette playlist ne contient aucune piste pour le moment.</p>";
        } else {
            $html .= "<ol class='track-list'>";
            $pistes = $this->liste->__get('piste');
            foreach ($pistes as $piste) {
                $html .= "<li class='track-row'>";
                $renderer = $this->rendererPour($piste);
                $html .= $renderer->render($selector);
                $html .= "</li>";
            }
            $html .= "</ol>";
        }

        $html .= "</div>";
        return $html;
    }

    private function rendererPour(AudioTrack $piste): AudioTrackRenderer {
        if ($piste instanceof AlbumTrack) {
            return new AlbumTrackRenderer($piste);
        }
        if ($piste instanceof PodcastTrack) {
            return new PodcastTrackRenderer($piste);
        }
        return new AudioTrackRenderer($piste);
    }
}
