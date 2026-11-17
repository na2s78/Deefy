<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddTrackAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['playlist']) || !($_SESSION['playlist'] instanceof Playlist)) {
            return "<div class='alert alert-danger'>
                <p>Aucune playlist courante n'est sélectionnée en session.</p>
                <p><a class='btn btn-primary' href='?action=my-playlists'>Choisir une playlist</a> ou <a class='btn btn-secondary' href='?action=add-playlist'>En créer une</a></p>
            </div>";
        }

        $playlist = $_SESSION['playlist'];
        $idPlaylist = (int)$playlist->__get('id');

        if ($this->http_method === 'GET') {
            $nomPlaylist = htmlspecialchars($playlist->__get('nom'), ENT_QUOTES);
            return "<div class='card form-card'>
                <h2>Ajouter une piste à \"{$nomPlaylist}\"</h2>
                <form enctype='multipart/form-data' action='?action=add-track' method='post'>
                    <div class='form-group'>
                        <label for='userfile'>Fichier audio (.mp3) :</label>
                        <input type='file' id='userfile' name='userfile' accept='.mp3,audio/mpeg' required>
                    </div>

                    <div class='form-group'>
                        <label for='titre'>Titre de la piste :</label>
                        <input type='text' id='titre' name='titre' required maxlength='100'>
                    </div>

                    <div class='form-group'>
                        <label for='type'>Type de piste :</label>
                        <select id='type' name='type'>
                            <option value='A'>Musique d'album</option>
                            <option value='P'>Podcast</option>
                        </select>
                    </div>

                    <div class='form-group'>
                        <label for='auteur'>Artiste / Auteur :</label>
                        <input type='text' id='auteur' name='auteur' required maxlength='100'>
                    </div>

                    <div class='form-group'>
                        <label for='genre'>Genre musical :</label>
                        <input type='text' id='genre' name='genre' maxlength='50'>
                    </div>

                    <div class='form-group'>
                        <label for='duree'>Durée (en secondes) :</label>
                        <input type='number' id='duree' name='duree' min='1' value='180' required>
                    </div>

                    <fieldset class='form-fieldset'>
                        <legend>Informations complémentaires (si album)</legend>
                        <div class='form-group'>
                            <label for='album'>Nom de l'album :</label>
                            <input type='text' id='album' name='album' maxlength='100'>
                        </div>
                        <div class='form-row'>
                            <div class='form-group col'>
                                <label for='annee'>Année :</label>
                                <input type='number' id='annee' name='annee' min='1900' max='2099' value='2026'>
                            </div>
                            <div class='form-group col'>
                                <label for='numero'>Numéro de piste :</label>
                                <input type='number' id='numero' name='numero' min='1' value='1'>
                            </div>
                        </div>
                    </fieldset>

                    <div class='form-actions'>
                        <button type='submit' class='btn btn-primary'>Enregistrer la piste</button>
                        <a class='btn btn-outline' href='?action=current-playlist'>Annuler</a>
                    </div>
                </form>
            </div>";
        }

        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            return "<div class='alert alert-danger'><p>Erreur lors du téléversement du fichier audio.</p></div>
            <p><a class='btn btn-outline' href='?action=add-track'>Réessayer</a></p>";
        }

        $nomOriginal = $_FILES['userfile']['name'];
        if (strtolower(substr($nomOriginal, -4)) !== '.mp3' || $_FILES['userfile']['type'] !== 'audio/mpeg') {
            return "<div class='alert alert-danger'><p>Le fichier sélectionné doit obligatoirement être un fichier audio .mp3.</p></div>
            <p><a class='btn btn-outline' href='?action=add-track'>Réessayer</a></p>";
        }

        $titre = trim($_POST['titre'] ?? '');
        $auteur = trim($_POST['auteur'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $duree = filter_var($_POST['duree'] ?? 0, FILTER_VALIDATE_INT);
        $type = ($_POST['type'] ?? 'A') === 'P' ? 'P' : 'A';
        $album = trim($_POST['album'] ?? '');
        $annee = filter_var($_POST['annee'] ?? 0, FILTER_VALIDATE_INT) ?: 2026;
        $numero = filter_var($_POST['numero'] ?? 1, FILTER_VALIDATE_INT) ?: 1;

        if ($titre === '' || $duree <= 0) {
            return "<div class='alert alert-danger'><p>Veuillez fournir un titre valide et une durée positive.</p></div>
            <p><a class='btn btn-outline' href='?action=add-track'>Réessayer</a></p>";
        }

        $nouveauNom = uniqid() . '.mp3';
        $cheminRelatif = 'audio/' . $nouveauNom;

        if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $cheminRelatif)) {
            return "<div class='alert alert-danger'><p>Échec du déplacement du fichier audio vers le dossier audio/.</p></div>
            <p><a class='btn btn-outline' href='?action=add-track'>Réessayer</a></p>";
        }

        if ($type === 'P') {
            $track = new PodcastTrack($titre, $cheminRelatif);
            $track->__set('auteur', $auteur);
            $track->__set('genre', $genre);
            $track->__set('duree', $duree);
        } else {
            $track = new AlbumTrack($titre, $cheminRelatif);
            $track->__set('auteur', $auteur);
            $track->__set('genre', $genre);
            $track->__set('duree', $duree);
            $track->__set('album', $album);
            $track->__set('annee', $annee);
            $track->__set('numero_piste', $numero);
        }

        $repo = DeefyRepository::getInstance();
        $repo->addTrackToPlaylist($idPlaylist, $track);

        $playlist->ajouterPiste($track);
        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $titreHtml = htmlspecialchars($titre, ENT_QUOTES);
        $html = "<div class='alert alert-success'><p>La piste <strong>{$titreHtml}</strong> a été ajoutée à la playlist.</p></div>";
        $html .= "<div class='card'>";
        $html .= $renderer->render();
        $html .= "<div class='playlist-actions-bar'>";
        $html .= "<a class='btn btn-primary' href='?action=add-track'>+ Ajouter encore une piste</a>";
        $html .= "<a class='btn btn-outline' href='?action=current-playlist'>Afficher la playlist</a>";
        $html .= "<a class='btn btn-outline' href='?action=my-playlists'>Voir mes playlists</a>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}
