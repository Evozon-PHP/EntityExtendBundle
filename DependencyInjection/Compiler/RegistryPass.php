<?php

namespace Pj\EntityExtendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Pj\EntityExtendBundle\Manager\Registry;

/**
 * Class RegistryPass.
 *
 * @copyright Evozon Systems SRL (http://www.evozon.com/)
 * @author    Csaba Balazs <csaba.balazs@evozon.com>
 */
class RegistryPass implements CompilerPassInterface
{
    /**
     * Overrides default doctrine registry.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $serviceName = 'doctrine';
        if ($container->hasDefinition($serviceName)) {
            $container->setParameter('doctrine.class', Registry::class);

            $definition = $container->getDefinition($serviceName);
            $definition->addMethodCall('setExtendedEntities', [$container->getParameter('extended_entities')]);
            $definition->addMethodCall('setNonTransient', [$container->getParameter('non_transient')]);
        }
    }
}
