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
                ->scalarNode('number_per_entity')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('number_word_text')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
                ->scalarNode('number_word_title')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
                ->scalarNode('language')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
        ->end();

        return $tree;
    }
}
