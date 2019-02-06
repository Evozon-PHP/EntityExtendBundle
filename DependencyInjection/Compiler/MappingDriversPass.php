<?php

namespace Pj\EntityExtendBundle\DependencyInjection\Compiler;

use Pj\EntityExtendBundle\Mapping\Driver\AnnotationDriver;
use Pj\EntityExtendBundle\Mapping\Driver\SimplifiedYamlDriver;
use Pj\EntityExtendBundle\Mapping\Driver\SimplifiedXmlDriver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MappingDriversPass.
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 */
class MappingDriversPass implements CompilerPassInterface
{
    /**
     * Overrides default mapping drivers.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->configureAnnotationDriver($container);
        $this->configureYamlDriver($container);
        $this->configureXmlDriver($container);
    }

    /**
     * Configures annotation mapping driver.
     *
     * @param ContainerBuilder $container
     */
    protected function configureAnnotationDriver(ContainerBuilder $container)
    {
        $serviceName = 'doctrine.orm.default_annotation_metadata_driver';
        if ($container->hasDefinition($serviceName)) {
            $container->setParameter('doctrine.orm.metadata.annotation.class', AnnotationDriver::class);

            $definition = $container->getDefinition($serviceName);
            $definition->addMethodCall('setExtendedEntities', [$container->getParameter('extended_entities')]);
            $definition->addMethodCall('setNonTransient', [$container->getParameter('non_transient')]);
            $definition->addMethodCall('setEntityManager', [new Reference('doctrine.orm.default_entity_manager')]);
        }
    }

    /**
     * Configures yml mapping driver.
     *
     * @param ContainerBuilder $container
     */
    protected function configureYamlDriver(ContainerBuilder $container)
    {
        $serviceName = 'doctrine.orm.default_yml_metadata_driver';
        if ($container->hasDefinition($serviceName)) {
            $container->setParameter('doctrine.orm.metadata.yml.class', SimplifiedYamlDriver::class);

            $definition = $container->getDefinition($serviceName);
            $definition->addMethodCall('setExtendedEntities', [$container->getParameter('extended_entities')]);
            $definition->addMethodCall('setNonTransient', [$container->getParameter('non_transient')]);
        }
    }

    /**
     * Configures xml mapping driver.
     *
     * @param ContainerBuilder $container
     */
    protected function configureXmlDriver(ContainerBuilder $container)
    {
        $serviceName = 'doctrine.orm.default_xml_metadata_driver';
        if ($container->hasDefinition($serviceName)) {
            $container->setParameter('doctrine.orm.metadata.xml.class', SimplifiedXmlDriver::class);

            $definition = $container->getDefinition($serviceName);
            $definition->addMethodCall('setExtendedEntities', [$container->getParameter('extended_entities')]);
            $definition->addMethodCall('setNonTransient', [$container->getParameter('non_transient')]);
        }
    }
}