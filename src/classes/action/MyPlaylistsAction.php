<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class MyPlaylistsAction extends Action {

    public function execute(): string {
        $repo = DeefyRepository::getInstance();
        $playlists = $repo->findAllPlaylists();

        $html = "<div class='card'>";
        $html .= "<h2>Mes Playlists</h2>";
        $html .= "<p class='subtitle'>Cliquez sur une playlist pour l'afficher.</p>";

        if (empty($playlists)) {
            $html .= "<div class='empty-state'>";
            $html .= "<p>Aucune playlist enregistrée.</p>";
            $html .= "<a class='btn btn-primary' href='?action=add-playlist'>Créer une playlist</a>";
            $html .= "</div>";
        } else {
            $html .= "<div class='playlist-grid'>";
            foreach ($playlists as $pl) {
                $id = (int)$pl['id'];
                $nom = htmlspecialchars($pl['nom'], ENT_QUOTES);

                $html .= "<div class='playlist-card'>";
                $html .= "<a class='playlist-link' href='?action=display-playlist&id={$id}'>";
                $html .= "<div class='playlist-info'>";
                $html .= "<h3>{$nom}</h3>";
                $html .= "</div>";
                $html .= "</a>";
                $html .= "</div>";
            }
            $html .= "</div>";

            $html .= "<div class='actions-footer'>";
            $html .= "<a class='btn btn-primary' href='?action=add-playlist'>+ Créer une nouvelle playlist</a>";
            $html .= "</div>";
        }

        $html .= "</div>";
        return $html;
    }
}
