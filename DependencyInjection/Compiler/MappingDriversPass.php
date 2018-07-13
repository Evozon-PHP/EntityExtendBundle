<?php

namespace Pj\EntityExtendBundle\DependencyInjection\Compiler;

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
        $this->configureYmlDriver($container);
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
            $container->setParameter(
                'doctrine.orm.metadata.annotation.class',
                'Pj\EntityExtendBundle\Mapping\Driver\AnnotationDriver'
            );

            $em = new Reference('doctrine.orm.default_entity_manager');

            $definition = $container->getDefinition($serviceName);
            $definition->addMethodCall('setExtendedEntities', [$container->getParameter('extended_entities')]);
            $definition->addMethodCall('setEntityManager', [$em]);
        }
    }

    /**
     * Configures yml mapping driver.
     *
     * @param ContainerBuilder $container
     */
    protected function configureYmlDriver(ContainerBuilder $container)
    {
        $serviceName = 'doctrine.orm.default_yml_metadata_driver';
        if ($container->hasDefinition($serviceName)) {
            $container->setParameter(
                'doctrine.orm.metadata.yml.class',
                'Pj\EntityExtendBundle\Mapping\Driver\SimplifiedYmlDriver'
            );

            $definition = $container->getDefinition($serviceName);
            $definition->addMethodCall('setExtendedEntities', [$container->getParameter('extended_entities')]);
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
            $container->setParameter(
                'doctrine.orm.metadata.xml.class',
                'Pj\EntityExtendBundle\Mapping\Driver\SimplifiedXmlDriver'
            );

            $definition = $container->getDefinition($serviceName);
            $definition->addMethodCall('setExtendedEntities', [$container->getParameter('extended_entities')]);
        }
    }
}