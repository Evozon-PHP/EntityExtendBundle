<?php

namespace Pj\EntityExtendBundle;

use Pj\EntityExtendBundle\DependencyInjection\Compiler\MappingDriversPass;
use Pj\EntityExtendBundle\DependencyInjection\Compiler\RegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EntityExtendBundle.
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 * @author Balazs Csaba <csaba.balazs@evozon.com>
 */
class EntityExtendBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MappingDriversPass());
        $container->addCompilerPass(new RegistryPass());
    }
}
