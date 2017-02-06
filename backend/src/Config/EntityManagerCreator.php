<?php

namespace pronata\Config;

use Symfony\Component\Config\FileLocator;

class EntityManagerCreator
{
    private $ormEntityManager;

    public function __construct()
    {
        $configDirectories = array(dirname(dirname(__DIR__)).'/config');
        $locator = new FileLocator($configDirectories);
        $loader = new YamlFileLoader($locator);
        $configParams = $loader->load('config.yml');

        $paths = array(dirname(__DIR__).'/Entity');
        $isDevMode = false;
        $dbParams = $configParams['database'];
        $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $this->ormEntityManager = \Doctrine\ORM\EntityManager::create($dbParams, $config);
    }

    public function getOrmEntityManager()
    {
        return $this->ormEntityManager;
    }
}