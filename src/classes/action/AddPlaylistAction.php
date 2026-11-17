<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action {

    public function execute(): string {
        if ($this->http_method === 'GET') {
            return "<div class='card form-card'>
                <h2>Créer une nouvelle playlist</h2>
                <form action='?action=add-playlist' method='post'>
                    <div class='form-group'>
                        <label for='nom'>Nom de la playlist :</label>
                        <input type='text' id='nom' name='nom' required maxlength='100'>
                    </div>
                    <div class='form-actions'>
                        <button type='submit' class='btn btn-primary'>Créer la playlist</button>
                        <a class='btn btn-outline' href='?action=my-playlists'>Annuler</a>
                    </div>
                </form>
            </div>";
        }

        $nom = trim($_POST['nom'] ?? '');

        if ($nom === '') {
            return "<div class='alert alert-danger'><p>Le nom de la playlist ne peut pas être vide.</p></div>
            <p><a class='btn btn-outline' href='?action=add-playlist'>Réessayer</a></p>";
        }

        $repo = DeefyRepository::getInstance();
        $playlist = $repo->saveEmptyPlaylist($nom);

        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $nomHtml = htmlspecialchars($nom, ENT_QUOTES);
        $html = "<div class='alert alert-success'><p>La playlist <strong>{$nomHtml}</strong> a été créée.</p></div>";
        $html .= "<div class='card'>";
        $html .= $renderer->render();
        $html .= "<div class='playlist-actions-bar'>";
        $html .= "<a class='btn btn-primary' href='?action=add-track'>+ Ajouter une piste à cette playlist</a>";
        $html .= "<a class='btn btn-outline' href='?action=my-playlists'>Voir mes playlists</a>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}
