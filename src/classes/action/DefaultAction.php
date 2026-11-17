<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action {

    public function execute(): string {
        $html = "<div class='card home-card'>";
        $html .= "<h2>Bienvenue sur Deefy</h2>";
        $html .= "<p class='lead'>Gestion de playlists audio.</p>";

        $html .= "<div class='action-shortcuts'>";
        $html .= "<a class='btn btn-primary' href='?action=my-playlists'>Voir mes playlists</a>";
        $html .= "<a class='btn btn-secondary' href='?action=add-playlist'>Créer une playlist vide</a>";
        if (isset($_SESSION['playlist'])) {
            $html .= "<a class='btn btn-outline' href='?action=current-playlist'>Afficher la playlist courante</a>";
        }
        $html .= "</div>";

        $html .= "</div>";
        return $html;
    }
}
