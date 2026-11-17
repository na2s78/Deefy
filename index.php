<?php

require_once 'src/classes/loader/Psr4ClassLoader.php';
$loader = new \iutnc\deefy\loader\Psr4ClassLoader('iutnc\\deefy\\', 'src/classes/');
$loader->register();

session_start();

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\dispatch\Dispatcher;

DeefyRepository::setConfig('conf/db.config.ini');

$dispatcher = new Dispatcher();
$dispatcher->run();
