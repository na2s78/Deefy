<?php

namespace iutnc\deefy\repository;

use Exception;
use PDO;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class DeefyRepository {
    private static ?DeefyRepository $instance = null;
    private static array $config = [];
    private PDO $pdo;

    private function __construct(array $config) {
        $dsn = $config['dsn'];
        $user = $config['username'];
        $pass = $config['password'];

        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new Exception("Impossible de lire le fichier de configuration : $file");
        }

        $driver = $conf['driver'];
        $host = $conf['host'];
        $database = $conf['database'];
        $username = $conf['username'];
        $password = $conf['password'];

        self::$config = [
            'dsn' => "{$driver}:host={$host};dbname={$database};charset=utf8",
            'username' => $username,
            'password' => $password
        ];
    }

    public static function getInstance(): DeefyRepository {
        if (self::$instance === null) {
            if (empty(self::$config)) {
                throw new Exception("La configuration de la base de données n'a pas été initialisée.");
            }
            self::$instance = new self(self::$config);
        }
        return self::$instance;
    }

    public function findAllPlaylists(): array {
        $stmt = $this->pdo->prepare("select id, nom from playlist order by id");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findPlaylistById(int $idPlaylist): ?Playlist {
        $stmt = $this->pdo->prepare("select id, nom from playlist where id = ?");
        $stmt->execute([$idPlaylist]);
        $plRow = $stmt->fetch();
        if (!$plRow) {
            return null;
        }

        $stmtTracks = $this->pdo->prepare("select track.id, track.titre, track.genre, track.duree, track.filename, track.type, track.artiste_album, track.titre_album, track.annee_album, track.numero_album, track.auteur_podcast, track.date_posdcast from track, playlist2track where track.id = playlist2track.id_track and playlist2track.id_pl = ? order by playlist2track.no_piste_dans_liste asc");
        $stmtTracks->execute([$idPlaylist]);
        $trackRows = $stmtTracks->fetchAll();

        $pistes = [];
        foreach ($trackRows as $t) {
            $filename = (string)($t['filename'] ?? '');
            $cheminFichier = ($filename !== '') ? 'audio/' . $filename : '';

            if ($t['type'] === 'P') {
                $trackObj = new PodcastTrack((string)$t['titre'], $cheminFichier);
                $trackObj->__set('auteur', (string)($t['auteur_podcast'] ?? ''));
                $trackObj->__set('date', (string)($t['date_posdcast'] ?? ''));
            } else {
                $trackObj = new AlbumTrack((string)$t['titre'], $cheminFichier);
                $trackObj->__set('auteur', (string)($t['artiste_album'] ?? ''));
                $trackObj->__set('album', (string)($t['titre_album'] ?? ''));
                $trackObj->__set('annee', (int)($t['annee_album'] ?? 0));
                $trackObj->__set('numero_piste', (int)($t['numero_album'] ?? 1));
            }

            $trackObj->__set('id', (int)$t['id']);
            $trackObj->__set('genre', (string)($t['genre'] ?? ''));
            $trackObj->__set('duree', (int)($t['duree'] ?? 0));

            $pistes[] = $trackObj;
        }

        $playlist = new Playlist($plRow['nom'], $pistes);
        $playlist->__set('id', (int)$plRow['id']);
        return $playlist;
    }

    public function saveEmptyPlaylist(string $nom): Playlist {
        $stmt = $this->pdo->prepare("insert into playlist (nom) values (?)");
        $stmt->execute([$nom]);
        $idPlaylist = (int)$this->pdo->lastInsertId();

        $playlist = new Playlist($nom, []);
        $playlist->__set('id', $idPlaylist);
        return $playlist;
    }

    public function addTrackToPlaylist(int $idPlaylist, AudioTrack $track): void {
        $type = 'A';
        $artisteAlbum = null;
        $titreAlbum = null;
        $anneeAlbum = null;
        $numeroAlbum = null;
        $auteurPodcast = null;
        $datePodcast = null;

        if ($track instanceof PodcastTrack) {
            $type = 'P';
            $auteurPodcast = $track->__get('auteur');
            $datePodcast = $track->__get('date') ?: date('Y-m-d');
        } elseif ($track instanceof AlbumTrack) {
            $type = 'A';
            $artisteAlbum = $track->__get('auteur');
            $titreAlbum = $track->__get('album');
            $anneeAlbum = (int)$track->__get('annee');
            $numeroAlbum = (int)$track->__get('numero_piste');
        }

        $nomFichier = basename($track->__get('nomFichierAudio'));

        $stmt = $this->pdo->prepare("insert into track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album, auteur_podcast, date_posdcast) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $track->__get('titre'),
            $track->__get('genre'),
            (int)$track->__get('duree'),
            $nomFichier,
            $type,
            $artisteAlbum,
            $titreAlbum,
            $anneeAlbum,
            $numeroAlbum,
            $auteurPodcast,
            $datePodcast
        ]);

        $idTrack = (int)$this->pdo->lastInsertId();
        $track->__set('id', $idTrack);

        $stmtMax = $this->pdo->prepare("select max(no_piste_dans_liste) as maxi from playlist2track where id_pl = ?");
        $stmtMax->execute([$idPlaylist]);
        $ligne = $stmtMax->fetch();
        $nextNo = (int)($ligne['maxi'] ?? 0) + 1;

        $stmtAssoc = $this->pdo->prepare("insert into playlist2track (id_pl, id_track, no_piste_dans_liste) values (?, ?, ?)");
        $stmtAssoc->execute([$idPlaylist, $idTrack, $nextNo]);
    }
}
