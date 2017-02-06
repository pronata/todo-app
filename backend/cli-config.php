<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use pronata\Config\EntityManagerCreator;

require_once __DIR__.'/vendor/autoload.php';

$emCreator = new EntityManagerCreator();
return ConsoleRunner::createHelperSet($emCreator->getOrmEntityManager());