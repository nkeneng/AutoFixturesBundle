<?php


namespace Steven\AutoFixturesBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * define the differents keys available in the config file
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder('auto_fixtures');
        $rootNode = $tree->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('min_number')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('max_number')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
                ->scalarNode('text')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
                ->scalarNode('title')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
        ->end();

        return $tree;
    }
}
