<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\MyPlaylistsAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\DisplayCurrentPlaylistAction;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddTrackAction;

class Dispatcher {
    private string $action;
    private array $tab = [];

    public function __construct() {
        $this->tab = [];
        $this->action = $_GET['action'] ?? 'default';

        $this->addRoute('default', DefaultAction::class);
        $this->addRoute('my-playlists', MyPlaylistsAction::class);
        $this->addRoute('display-playlist', DisplayPlaylistAction::class);
        $this->addRoute('current-playlist', DisplayCurrentPlaylistAction::class);
        $this->addRoute('add-playlist', AddPlaylistAction::class);
        $this->addRoute('add-track', AddTrackAction::class);
    }

    public function addRoute(string $action, string $class): self {
        $this->tab[$action] = $class;
        return $this;
    }

    public function run(): void {
        if (!array_key_exists($this->action, $this->tab)) {
            $this->action = 'default';
        }

        $classe = $this->tab[$this->action];
        $obj = new $classe();
        $html = $obj->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        $actifAccueil = ($this->action === 'default') ? ' active' : '';
        $actifPlaylists = ($this->action === 'my-playlists') ? ' active' : '';
        $actifAddPl = ($this->action === 'add-playlist') ? ' active' : '';
        $actifDisplayPl = in_array($this->action, ['display-playlist', 'current-playlist', 'add-track'], true) ? ' active' : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deefy</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="app-header">
    <div class="header-container">
        <div class="brand">
            <a href="?action=default" class="logo">
                <span class="logo-text">Deefy</span>
            </a>
        </div>

        <nav class="main-nav">
            <a class="nav-link<?= $actifAccueil ?>" href="?action=default">Accueil</a>
            <a class="nav-link<?= $actifPlaylists ?>" href="?action=my-playlists">Mes playlists</a>
            <a class="nav-link<?= $actifAddPl ?>" href="?action=add-playlist">+ Créer une playlist</a>
            <a class="nav-link<?= $actifDisplayPl ?>" href="?action=current-playlist">Playlist courante</a>
        </nav>
    </div>
</header>

<main class="main-container">
    <?= $html ?>
</main>

<footer class="app-footer">
    <p>Deefy - BUT Informatique S3</p>
</footer>

</body>
</html>
<?php
    }
}
