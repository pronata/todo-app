<?php

namespace pronata\Config;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader extends FileLoader
{
    public function load($resource, $type = null)
    {
        // The locator receives a collection of locations where it should look for files.
        // The first argument of locate() is the name of the file to look for.
        // The second argument may be the current path and when supplied, the locator will look in this directory first.
        // The third argument indicates whether or not the locator should return the first file it has found or an array containing all matches.
        $configValues = Yaml::parse(
            file_get_contents($this->getLocator()->locate($resource, null, true))
        );
        $processor = new Processor();
        $configuration = new Configuration();
        $processedConfiguration = $processor->processConfiguration(
            $configuration,
            $configValues
        );
        return $processedConfiguration;

    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }

}