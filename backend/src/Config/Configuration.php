<?php

namespace pronata\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("todo-api-config");
        $rootNode
            ->children()
                ->arrayNode('database')
                    ->children()
                        ->scalarNode("dbname")
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode("user")
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode("password")
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode("host")
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->enumNode("driver")
                            ->values(array('pdo_mysql', 'pdo_sqlite', 'pdo_pgsql', 'pdo_oci',
                                'oci8', 'ibm_db2', 'pdo_sqlsrv', 'mysqli', 'drizzle_pdo_mysql', 'sqlanywhere', 'sqlsrv'))
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}