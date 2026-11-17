<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {

    public function execute(): string {
        if (!isset($_GET['id'])) {
            return "<div class='alert alert-danger'><p>Aucune playlist sélectionnée.</p>
                <p><a class='btn btn-outline' href='?action=my-playlists'>Retour à mes playlists</a></p>
            </div>";
        }

        $idPlaylist = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if ($idPlaylist === false) {
            return "<div class='alert alert-danger'><p>Identifiant de playlist invalide.</p></div>";
        }

        $repo = DeefyRepository::getInstance();
        $playlist = $repo->findPlaylistById($idPlaylist);
        if ($playlist === null) {
            return "<div class='alert alert-danger'><p>Playlist introuvable.</p></div>";
        }

        $_SESSION['playlist'] = $playlist;

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
