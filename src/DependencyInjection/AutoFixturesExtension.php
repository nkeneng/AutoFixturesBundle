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
        $container->setParameter('fixtures.min_number',$config['min_number']);
        $container->setParameter('fixtures.max_number',$config['max_number']);
        $container->setParameter('fixtures.text',$config['text']);
        $container->setParameter('fixtures.title',$config['title']);
    }

}
