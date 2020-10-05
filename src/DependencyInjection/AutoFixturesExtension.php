<?php


namespace Steven\AutoFixturesBundle\DependencyInjection;


use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AutoFixturesExtension extends  Extension
{
    /**
     * register my services.yaml
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration,$configs);
        $container->setParameter('fixtures.number_per_entity',$config['number_per_entity']);
        $container->setParameter('fixtures.number_word_text',$config['number_word_text']);
        $container->setParameter('fixtures.number_word_title',$config['number_word_title']);
        $container->setParameter('fixtures.language',$config['language']);
    }

}
