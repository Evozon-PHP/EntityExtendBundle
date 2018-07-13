<?php

namespace Pj\EntityExtendBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pj_entity_extend');
        $rootNode
            ->children()
                ->arrayNode('extended_entities')
                ->useAttributeAsKey('name')
                ->prototype('scalar')
            ->end();

        return $treeBuilder;
    }
}
