<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;

class DisplayCurrentPlaylistAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['playlist']) || !($_SESSION['playlist'] instanceof Playlist)) {
            return "<div class='card'>
                <h2>Playlist courante</h2>
                <p>Aucune playlist courante n'est stockée en session.</p>
                <div class='action-shortcuts'>
                    <a class='btn btn-primary' href='?action=my-playlists'>Choisir dans mes playlists</a>
                    <a class='btn btn-secondary' href='?action=add-playlist'>Créer une playlist vide</a>
                </div>
            </div>";
        }

        $playlist = $_SESSION['playlist'];

        $renderer = new AudioListRenderer($playlist);
        $html = "<div class='card'>";
        $html .= $renderer->render();
        $html .= "<div class='playlist-actions-bar'>";
        $html .= "<a class='btn btn-primary' href='?action=add-track'>+ Ajouter une piste</a>";
        $html .= "<a class='btn btn-outline' href='?action=my-playlists'>Retour à mes playlists</a>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}
